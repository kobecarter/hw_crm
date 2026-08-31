<?php

namespace App\Validators;

use Respect\Validation\Validator as v;
use App\Validators\BaseValidator;

class ContactEmailValidator extends BaseValidator
{
    private $validations;
    public function __construct()
    {
        $this->validations = [
            'nom' => v::key('nom', v::stringType()->notEmpty()),
            'tel' => v::key('tel', v::stringType()->notEmpty()),
            'email' => v::key('email', v::email()->notEmpty()),
            // Optionnel : le champ "Choisissez la date et l'heure" du
            // formulaire mobile n'est pas obligatoire (pas de validateur
            // côté app) - l'exiger ici rejetait silencieusement toute
            // demande envoyée sans date choisie.
            'date' => v::key('date', v::stringType(), false),
            'message' => v::key('message', v::stringType()->notEmpty()),
        ];
        parent::__construct($this->validations);
    }
}
