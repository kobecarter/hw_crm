<?php

namespace App\Mails;

use App\Mails\HwMailer;
use App\Utils\EmailRender;
use App\Utils\GetV;

class ContactMail
{

    private HwMailer $mailer;
    private $error;
    private $data;
    public function __construct($data = [])
    {
        $this->mailer = new HwMailer();
        $this->data = $data;
    }

    private function init()
    {
        $this->mailer->Subject = 'Contact';
        $this->mailer->addAddress(GetV::$hwLabelContactMail, 'Hello World Support');
        $this->mailer->setFrom($this->data['email'], $this->data['nom']);
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
            __DIR__ . '/templates/mail.contact.html',
            $this->data
        );
        $body = $renderer->render();
        return $body;
    }
    public function getError()
    {
        return $this->error;
    }
}
