<?php

namespace App\Validators;

use Respect\Validation\Validator as v;
use App\Validators\BaseValidator;

class ReclamationValidator extends BaseValidator
{

    private $validations;
    public function __construct()
    {
        $this->validations  = [
            'sujet' => v::key('sujet', v::stringType()->notEmpty()),
            'message' => v::key('message', v::stringType()->notEmpty())
        ];
        parent::__construct($this->validations);
    }
}
