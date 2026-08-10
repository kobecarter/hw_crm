<?php

/* Orchestrateur "lancement de projet" : regroupe les actions qui doivent se
   déclencher pour un client donné à l'un de ces trois moments (peu importe
   lequel arrive en premier) :
     - un paiement est ajouté sur une de ses factures,
     - un de ses devis passe au statut "Paiement effectué",
     - une facture est créée pour lui.
   Actions : créer/retrouver son dossier Drive, créer le ticket Trello
   "Nouveau Projet | <client>" avec le récap + le lien Drive, prévenir
   l'équipe sur Slack (#familly), et envoyer un email à
   support@helloworld-agency.com. Ces trois notifications (Trello, Slack,
   email) ne se déclenchent qu'UNE SEULE FOIS par client, au tout premier des
   trois événements possibles : les déclenchements suivants ne font plus rien
   (le dossier Drive est simplement retrouvé/assuré en silence). Toute erreur
   est journalisée et n'interrompt jamais l'action métier d'origine (ajout de
   paiement / facture / changement de statut devis). */
class projectNotifier
{
    public static function launch($client, $devis = null, $eventLabel = null, $dossierDriveExistantChoisi = null)
    {
        try {
            if (!$client || $client->getId() == 0) {
                return;
            }

            $nomClient = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : trim($client->getNom() . ' ' . $client->getPrenom());
            $driveLink = (class_exists('googleDriveClient') && googleDriveClient::isConfigured()) ? googleDriveClient::ensureClientFolder($client, $dossierDriveExistantChoisi) : null;

            $cardName = 'Nouveau Projet | ' . $nomClient;

            // Le ticket Trello, le message Slack #familly et l'email support ne se
            // déclenchent qu'une seule fois par client : au tout premier des trois
            // événements (paiement / devis "Paiement effectué" / facture créée).
            // S'il existe déjà un ticket pour ce client, on ne refait plus rien
            // ensuite (le dossier Drive reste juste assuré/à jour en silence).
            if (self::findExistingCard($cardName)) {
                return;
            }

            self::createCard($cardName, $client, $devis, $driveLink);
            self::postSlackLaunch($nomClient);
            self::emailSupport($nomClient, $driveLink, $eventLabel);
        } catch (\Throwable $e) {
            error_log('projectNotifier::launch exception - ' . $e->getMessage());
        }
    }

    private static function trelloConfigured()
    {
        return defined('TRELLO_API_KEY') && TRELLO_API_KEY != '' && defined('TRELLO_TOKEN') && TRELLO_TOKEN != '' && defined('TRELLO_LIST_ID') && TRELLO_LIST_ID != '';
    }

    private static function findExistingCard($cardName)
    {
        if (!self::trelloConfigured()) {
            return null;
        }
        $url = 'https://api.trello.com/1/lists/' . TRELLO_LIST_ID . '/cards?fields=id,name&key=' . TRELLO_API_KEY . '&token=' . TRELLO_TOKEN;
        $ch = curl_init($url);
        curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode < 200 || $httpCode >= 300) {
            return null;
        }
        $cards = json_decode($response, true);
        if (!is_array($cards)) {
            return null;
        }
        foreach ($cards as $card) {
            if (isset($card['name']) && $card['name'] === $cardName) {
                return $card;
            }
        }
        return null;
    }

    private static function buildRecap($client, $devis, $driveLink)
    {
        $nomClient = $client->getRaisonSocial() != '' ? $client->getRaisonSocial() : trim($client->getNom() . ' ' . $client->getPrenom());
        $recap = "**Client :** " . $nomClient . "\n";

        if ($devis && $devis->getId()) {
            $recap .= "**Devis N° :** " . $devis->getNumero() . "\n\n**Prestations :**\n";
            $unitiesMap = getUnities();
            foreach ($devis->getItems() as $item) {
                $uniteLabel = isset($unitiesMap[$devis->getLangue()][$item->getUnite()]) ? $unitiesMap[$devis->getLangue()][$item->getUnite()] : $item->getUnite();
                $recap .= "- **" . $item->getTitre() . "**\n";
                // Durée = quantité + unité de la ligne de devis (ex: "3 Mois", "2 Semaine") -
                // seule donnée de durée réellement disponible par prestation (pas de champ "durée"
                // dédié sur item_devis), mise en avant explicitement plutôt que noyée entre
                // parenthèses comme avant.
                $recap .= "    *Durée :* " . $item->getQte() . ($uniteLabel != '' ? ' ' . $uniteLabel : '') . "\n";
                $description = trim(devis::descriptionToPlainText($item->getDescription()));
                if ($description != '') {
                    foreach (preg_split('/\r\n|\r|\n/', $description) as $line) {
                        $line = trim($line);
                        if ($line != '') {
                            $recap .= "    " . $line . "\n";
                        }
                    }
                }
            }
        }

        if ($driveLink) {
            $recap .= "\n**Dossier Drive :** " . $driveLink;
        }

        return $recap;
    }

    private static function createCard($cardName, $client, $devis, $driveLink)
    {
        if (!self::trelloConfigured()) {
            return;
        }
        $recap = self::buildRecap($client, $devis, $driveLink);
        $due = date('c', strtotime('+2 days'));

        $payload = array(
            'idList' => TRELLO_LIST_ID,
            'name' => $cardName,
            'desc' => $recap,
            'due' => $due,
            'key' => TRELLO_API_KEY,
            'token' => TRELLO_TOKEN
        );
        if (defined('TRELLO_MEMBER_ZAKARIA_ID') && TRELLO_MEMBER_ZAKARIA_ID != '') {
            $payload['idMembers'] = TRELLO_MEMBER_ZAKARIA_ID;
        }

        $ch = curl_init('https://api.trello.com/1/cards');
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_TIMEOUT => 15
        ));
        curl_exec($ch);
        curl_close($ch);
    }

    private static function postSlackLaunch($nomClient)
    {
        if (!defined('SLACK_WEBHOOK_URL_FAMILY') || SLACK_WEBHOOK_URL_FAMILY == '') {
            return;
        }
        $text = ":rocket: *Lancement de projet* : " . $nomClient . " ! Merci de préparer les éléments nécessaires. Récupérez les détails auprès de *Zakaria El Haboussi*.";
        $ch = curl_init(SLACK_WEBHOOK_URL_FAMILY);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json; charset=utf-8'),
            CURLOPT_POSTFIELDS => json_encode(array('text' => $text)),
            CURLOPT_TIMEOUT => 15
        ));
        curl_exec($ch);
        curl_close($ch);
    }

    private static function emailSupport($nomClient, $driveLink, $eventLabel)
    {
        if (!defined('SMTP_HOST') || SMTP_HOST == '' || !defined('GOOGLE_DRIVE_NOTIFY_EMAIL') || GOOGLE_DRIVE_NOTIFY_EMAIL === '') {
            return;
        }
        try {
            require_once __DIR__ . '/../../../vendor/autoload.php';

            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(SMTP_USERNAME, 'Hello World CRM');
            $mail->addAddress(GOOGLE_DRIVE_NOTIFY_EMAIL);

            $mail->isHTML(true);
            $mail->Subject = "Nouveau projet à planifier : " . $nomClient;
            $mail->Body = "Bonjour,<br><br>"
                . "Voici un <b>nouveau client</b> à prendre en considération dans le planning : <b>" . htmlspecialchars($nomClient) . "</b>.<br><br>"
                . ($eventLabel ? "<b>Déclencheur :</b> " . htmlspecialchars($eventLabel) . "<br><br>" : "")
                . ($driveLink ? "<b>Dossier Drive :</b> <a href=\"" . $driveLink . "\">" . $driveLink . "</a><br><br>" : "")
                . "Pour plus de détails sur les prestations et le brief du projet, merci de contacter le <b>département commercial</b>.<br><br>"
                . "L'équipe Hello World CRM";
            $mail->AltBody = "Nouveau client à prendre en considération dans le planning : " . $nomClient . ($eventLabel ? ' - ' . $eventLabel : '') . ($driveLink ? ' - ' . $driveLink : '') . " - Pour plus de details, merci de contacter le departement commercial.";

            $mail->send();
        } catch (\Throwable $e) {
            error_log('projectNotifier::emailSupport exception - ' . $e->getMessage());
        }
    }
}
