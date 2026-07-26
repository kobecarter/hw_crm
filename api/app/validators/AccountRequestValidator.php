<?php

namespace App\Validators;

use Respect\Validation\Validator as v;
use App\Validators\BaseValidator;

class AccountRequestValidator extends BaseValidator
{

    private $validations;
    public function __construct()
    {
        $this->validations = [
            // 'titre' => v::key('titre', v::stringType()->notEmpty()),
            'prenom' => v::key('prenom', v::stringType()->notEmpty()),
            'nom' => v::key('nom', v::stringType()->notEmpty()),
            'ice' => v::key('ice', v::notEmpty()),
            'email' => v::key('email', v::email()->notEmpty()),
            'raison_social' => v::key('raison_social', v::stringType()->notEmpty())
        ];
        parent::__construct($this->validations);
    }
}
