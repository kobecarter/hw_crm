<?php

namespace App\Controllers;

use App\Utils\GetV;

// Sert les PDF de factures/devis directement au format binaire. Contrairement
// au reste de l'API, ces URLs sont ouvertes par le navigateur externe du
// téléphone (launchUrl côté mobile) et non par le client HTTP de l'app : il
// n'y a donc pas d'en-tête Authorization possible, le JWT du client est
// transmis en paramètre de requête et vérifié ici à la main (même secret/algo
// que AuthController::check(), juste une source différente pour le token).
//
// La génération elle-même tourne dans un process PHP séparé (generate_pdf.php,
// exécuté via exec) : hw_crm/api/vendor/ (déjà chargé par index.php) et
// hw_crm/vendor/ (chargé en interne par facture::pdfFacture()/devis::pdfDevis())
// déclarent tous les deux mpdf/psr-log-aware-trait contre des versions
// incompatibles de psr/log - les charger tous les deux dans le même process
// fatale sur une redéclaration de méthode. Un process séparé n'a jamais
// hw_crm/api/vendor/ chargé, donc pas de collision.
class PdfController
{
    private static function authenticatedClientId()
    {
        $token = request()->get('token');
        if (!$token) {
            return null;
        }
        return GetV::verifyDownloadToken($token);
    }

    // PHP_BINARY pointe vers httpd (pas un exécutable "php") sous mod_php,
    // qui est comment cette API tourne ici - on ne peut donc pas s'y fier
    // pour lancer un script en CLI.
    private static function phpCliBinary()
    {
        if (defined('PHP_BINARY') && stripos(basename(PHP_BINARY), 'php') === 0) {
            return PHP_BINARY;
        }
        foreach (['/Applications/XAMPP/xamppfiles/bin/php', '/usr/bin/php', '/usr/local/bin/php'] as $candidate) {
            if (is_executable($candidate)) {
                return $candidate;
            }
        }
        return 'php';
    }

    private static function stream($type, $id)
    {
        $clientID = self::authenticatedClientId();
        if (!$clientID) {
            http_response_code(401);
            echo 'Unauthorized';
            exit;
        }
        $id = (int) $id;
        if (!$id) {
            http_response_code(400);
            echo 'Bad request';
            exit;
        }

        $script = escapeshellarg(__DIR__ . '/../../../generate_pdf.php');
        $cmd = escapeshellarg(self::phpCliBinary()) . ' ' . $script . ' '
            . escapeshellarg($type) . ' ' . escapeshellarg((string) $id) . ' ' . escapeshellarg((string) $clientID)
            . ' 2>/dev/null';
        $fileName = trim((string) shell_exec($cmd));
        // Le nom de fichier vient de la raison sociale du client (donnée CRM,
        // pas une entrée de cette requête) - on garde quand même une garde
        // stricte pour ne jamais laisser un '/' ou '..' sortir de uploads/.
        if (!$fileName || basename($fileName) !== $fileName) {
            http_response_code(404);
            echo 'Not found';
            exit;
        }

        $path = __DIR__ . '/../../../uploads/' . $fileName;
        if (!is_file($path)) {
            http_response_code(500);
            echo 'Generation failed';
            exit;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($fileName) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        @unlink($path);
        exit;
    }

    public function facture($id)
    {
        self::stream('facture', $id);
    }

    public function devis($id)
    {
        self::stream('devis', $id);
    }
}
