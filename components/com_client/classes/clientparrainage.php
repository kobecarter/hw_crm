<?php

// Parrainage client -> filleul : un client existant recommande un prospect.
// Le CRM (crm_client_parrainage) est la source de vérité -- le site écrit ici
// via l'API à jeton (createParrainageApi) au lieu de garder ces données dans
// sa propre base, même principe que attestation/clientsocial (voir
// com_client/controleurs/router.php). Le suivi (statut/récompense) reste un
// geste manuel de l'agence, comme pour com_fidelite.
class clientparrainage
{
    static $table = __prefixe_db__ . "client_parrainage";

    // Vue client (API à jeton) : historique des filleuls recommandés par ce
    // client, plus récent d'abord.
    public static function findAllByClientApi($id_parrain)
    {
        if (!getToken()) return array();
        global $db;
        $items = array();
        $rows = $db->queryS(sprintf(
            "SELECT filleul_nom, filleul_entreprise, filleul_email, statut, recompense, recompense_donnee, date_add FROM " . static::$table . " WHERE id_parrain = %s ORDER BY date_add DESC",
            GetSQLValueString((int) $id_parrain, "int")
        ));
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $items[] = array(
                    'filleul_nom' => $row['filleul_nom'],
                    'filleul_entreprise' => $row['filleul_entreprise'],
                    'filleul_email' => $row['filleul_email'],
                    'statut' => (int) $row['statut'],
                    'recompense' => $row['recompense'],
                    'recompense_donnee' => (int) $row['recompense_donnee'],
                    'date_add' => $row['date_add'],
                );
            }
        }
        return $items;
    }

    // Création (API à jeton) : validation + anti-doublon faits ici, pas côté
    // site -- même principe que attestation::signApi("Déjà signée") : le CRM
    // ne fait confiance à aucune donnée "déjà validée" reçue du site.
    public static function createApi($idParrain, $parrainNom, $parrainEmail, $filleulNom, $filleulEntreprise, $filleulEmail, $filleulTel, $message)
    {
        if (!getToken()) {
            return array("icon" => "error", "message" => "Unauthorized");
        }
        global $db;
        $idParrain = (int) $idParrain;
        $filleulNom = trim((string) $filleulNom);
        $filleulEmail = trim((string) $filleulEmail);
        if ($idParrain <= 0 || $filleulNom === '' || $filleulEmail === '' || !filter_var($filleulEmail, FILTER_VALIDATE_EMAIL)) {
            return array("icon" => "warning", "message" => "Missing or invalid fields", "code" => "missing");
        }
        if ($parrainEmail !== '' && strcasecmp((string) $parrainEmail, $filleulEmail) === 0) {
            return array("icon" => "warning", "message" => "You cannot refer yourself", "code" => "self");
        }
        $dup = $db->queryS(sprintf(
            "SELECT id FROM " . static::$table . " WHERE id_parrain = %s AND filleul_email = %s LIMIT 1",
            GetSQLValueString($idParrain, "int"), GetSQLValueString($filleulEmail, "text")
        ));
        if (is_array($dup) && count($dup) > 0) {
            return array("icon" => "warning", "message" => "Already referred", "code" => "dup");
        }
        $now = date("Y-m-d H:i:s");
        $db->query(sprintf(
            "INSERT INTO " . static::$table . " (id_parrain, parrain_nom, parrain_email, filleul_nom, filleul_entreprise, filleul_email, filleul_tel, message, statut, recompense_donnee, date_add) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 0, 0, %s)",
            GetSQLValueString($idParrain, "int"), GetSQLValueString((string) $parrainNom, "text"), GetSQLValueString((string) $parrainEmail, "text"),
            GetSQLValueString($filleulNom, "text"), GetSQLValueString((string) $filleulEntreprise, "text"), GetSQLValueString($filleulEmail, "text"),
            GetSQLValueString((string) $filleulTel, "text"), GetSQLValueString((string) $message, "text"), GetSQLValueString($now, "text")
        ));
        if (!empty($db->getLink()->error)) {
            return array("icon" => "error", "message" => "There is a problem with the server");
        }
        self::notifyAgence($parrainNom, $parrainEmail, $filleulNom, $filleulEmail, $filleulEntreprise, $filleulTel, $message);
        return array("icon" => "success", "message" => "Merci ! Nous contactons votre filleul rapidement.", "code" => "ok");
    }

    // Aucun écran CRM dédié pour l'instant (comme hw_demande_client côté
    // site) : sans cet e-mail, l'agence n'a aucune visibilité sur les
    // recommandations reçues. Best-effort, n'échoue jamais createApi().
    private static function notifyAgence($parrainNom, $parrainEmail, $filleulNom, $filleulEmail, $filleulEntreprise, $filleulTel, $message)
    {
        if (!defined('SMTP_HOST') || SMTP_HOST == '') {
            error_log('[parrainage] email agence non envoye (SMTP non configure) -> filleul: ' . $filleulEmail);
            return;
        }
        require_once __DIR__ . '/../../../vendor/autoload.php';
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(SMTP_USERNAME, 'Espace client');
            $mail->addAddress(SMTP_USERNAME);
            $mail->isHTML(true);
            $mail->Subject = 'Nouvelle recommandation (parrainage) — ' . $filleulNom;
            $mail->Body = '<p>Un client a recommandé un prospect depuis son espace client.</p>'
                . '<table cellpadding="5">'
                . '<tr><td><strong>Parrain</strong></td><td>' . htmlspecialchars((string) $parrainNom) . ' (' . htmlspecialchars((string) $parrainEmail) . ')</td></tr>'
                . '<tr><td><strong>Filleul</strong></td><td>' . htmlspecialchars($filleulNom) . '</td></tr>'
                . '<tr><td><strong>Entreprise</strong></td><td>' . htmlspecialchars((string) $filleulEntreprise) . '</td></tr>'
                . '<tr><td><strong>Email</strong></td><td>' . htmlspecialchars($filleulEmail) . '</td></tr>'
                . '<tr><td><strong>Téléphone</strong></td><td>' . htmlspecialchars((string) $filleulTel) . '</td></tr>'
                . ($message !== '' ? '<tr><td><strong>Message</strong></td><td>' . nl2br(htmlspecialchars((string) $message)) . '</td></tr>' : '')
                . '</table>';
            $mail->AltBody = strip_tags($mail->Body);
            $mail->send();
        } catch (\Throwable $e) {
            error_log('[parrainage notifyAgence] ' . $e->getMessage());
        }
    }
}
