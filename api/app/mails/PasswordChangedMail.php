<?php

namespace App\Mails;

use App\Mails\HwMailer;
use App\Utils\EmailRender;
use App\Utils\GetV;

class PasswordChangedMail
{

    private \client $client;
    private HwMailer $mailer;
    private $error;
    public function __construct($client)
    {
        $this->mailer = new HwMailer();
        $this->client = $client;
    }

    private function init()
    {
        $this->mailer->Subject = 'Mot de passe changé';
        $this->mailer->addAddress($this->client->getEmail(), $this->client->getRaisonSocial());
        $this->mailer->Body    = $this->htmlContent();
    }
    public function send()
    {
        $this->init();
        $sent = $this->mailer->trigger();
        $this->error = $this->mailer->getError();
        return $sent;
    }

    public function htmlContent()
    {
        $renderer = new EmailRender(
            __DIR__ . '/templates/mail.password.changed.html',

            [
                'email' => $this->client->getEmail(),
                'name' => $this->client->getRaisonSocial() ?? $this->client->getNom(),
                'hw_email' => GetV::$hwLabelContactMail,
            ]
        );
        $body = $renderer->render();
        return $body;
    }
    public function getError()
    {
        return $this->error;
    }
}
