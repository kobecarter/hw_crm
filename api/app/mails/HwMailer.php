<?php

namespace App\Mails;

use App\Utils\GetV;
use App\Utils\ResponseMessages;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class HwMailer extends PHPMailer
{


    private $error;


    public function __construct()
    {
        parent::__construct();
        $this->initMailer();
        $this->initSMTP();
    }

    public function trigger()
    {
        try {
            if (!$this->to || empty($this->to)) {
                throw new \Exception(ResponseMessages::messages('noEmail'));
            }
            if (!$this->Body) {
                throw (new \Exception(ResponseMessages::messages('mailNotSent')));
            }
            // /send 
            $sent = $this->send();

            if ($sent) {

                return true;
            } else {
                throw (new \Exception(ResponseMessages::messages('mailNotSent')));
            }
        } catch (\Throwable $th) {
            $this->error = $th->getMessage();
        }
        return false;
    }


    private function initMailer()
    {
        $this->setFrom(GetV::$hwLabelEmail, 'Hello World Agency');
        $this->isHTML(true);
    }

    private function initSMTP()
    {
        // Les identifiants 'noreply@helloworld-agency.com' codés en dur ici
        // ne sont plus valides côté serveur mail (535 Incorrect authentication
        // data, confirmé en direct). On réutilise donc le même compte SMTP que
        // reclamation::sendReponseEmail() (components/com_reclamation/classes/
        // reclamation.php), déjà fonctionnel en prod, via les constantes
        // définies dans config.secrets.php - chargé par api/index.php via
        // ../config.php, donc disponible ici sans dupliquer les secrets.
        $this->isSMTP();
        $this->Host       = defined('SMTP_HOST') ? SMTP_HOST : 'helloworld-agency.com';
        $this->SMTPAuth   = true;                                   //Enable SMTP authentication
        $this->Priority = 1;
        $this->Port       = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $this->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->Username   = defined('SMTP_USERNAME') ? SMTP_USERNAME : 'noreply@helloworld-agency.com';
        $this->Password   = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
        $this->CharSet    = 'UTF-8';
        // Sans ça, PHPMailer attend jusqu'à 5 min (défaut) si le serveur SMTP
        // ne répond pas - un client mobile abandonne bien avant (10s), donc
        // l'utilisateur voit un timeout générique au lieu d'un vrai message
        // d'erreur. 8s laisse une marge sous le timeout client tout en
        // échouant vite si le SMTP est injoignable depuis cet hébergeur.
        $this->Timeout = 8;
        $this->SMTPKeepAlive = false;
    }



    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;
    }
}
