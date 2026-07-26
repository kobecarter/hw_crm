<?php

namespace App\Mails;

use App\Mails\HwMailer;
use App\Utils\EmailRender;
use App\Utils\GetV;

class AccountRegistrationMail
{

    private \client $client;
    private $password;
    private HwMailer $mailer;
    private $error;
    public function __construct($client, $password)
    {
        $this->mailer = new HwMailer();
        $this->client = $client;
        $this->password = $password;
    }

    private function init()
    {
        $this->mailer->Subject = 'Hello World Agency';
        $this->mailer->addAddress($this->client->getEmail());
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
            __DIR__ . '/templates/mail.account.create.html',
            [
                'email' => $this->client->getEmail(),
                'password' => $this->password
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
