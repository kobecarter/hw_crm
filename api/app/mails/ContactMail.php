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
        // 'source' est optionnel côté appelant (le site web existant ne
        // l'envoie pas) - valeur par défaut pour que {{ source }} du template
        // ne reste jamais littéralement affiché dans l'email.
        $data['source'] = $data['source'] ?? 'Site web';
        // Idem : le champ date du formulaire mobile est optionnel (voir
        // ContactEmailValidator) - sans valeur par défaut ici, {{ date }}
        // resterait affiché littéralement dans l'email.
        $data['date'] = $data['date'] ?? 'Non précisée';
        $this->data = $data;
    }

    private function init()
    {
        $this->mailer->Subject = 'Contact - ' . $this->data['source'];
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
