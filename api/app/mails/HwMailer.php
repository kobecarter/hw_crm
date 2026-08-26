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
        //HWA settings

        $this->isSMTP();
        $this->Host       = 'helloworld-agency.com';
        $this->SMTPAuth   = true;                                   //Enable SMTP authentication
        $this->Priority = 1;
        $this->Port       = 465;
        $this->Username   = 'noreply@helloworld-agency.com';                  //SMTP username
        $this->Password   = 'hv,6l0b[(S9D';                               //SMTP password
        $this->CharSet    = 'UTF-8';
        // Sans ça, PHPMailer attend jusqu'à 5 min (défaut) si le serveur SMTP
        // ne répond pas - un client mobile abandonne bien avant (10s), donc
        // l'utilisateur voit un timeout générique au lieu d'un vrai message
        // d'erreur. 8s laisse une marge sous le timeout client tout en
        // échouant vite si le SMTP est injoignable depuis cet hébergeur.
        $this->Timeout = 8;
        $this->SMTPKeepAlive = false;
        // Mailtrap settings for testing
        // $this->isSMTP();
        // $this->Host = 'smtp.mailtrap.io';
        // $this->SMTPAuth = true;
        // $this->Port = 2525;
        // $this->Username = 'b91e35307bd305';
        // $this->Password = 'ceabab2ad72419';
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
