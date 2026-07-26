<?php

namespace App\Mails;

use App\Mails\HwMailer;
use App\Utils\EmailRender;
use App\Utils\GetV;

class GetResetPassCodeMail
{

    private \client $client;
    private $code;
    private HwMailer $mailer;
    private $error;
    public function __construct($client, $code)
    {
        $this->mailer = new HwMailer();
        $this->client = $client;
        $this->code = $code;
    }

    private function init()
    {
        $this->mailer->Subject = 'Réinitialisation du mot de passe';
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
            __DIR__ . '/templates/mail.resetpass.code.html',
            [
                'name' => $this->client->getNom(),
                'code' => $this->code
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
