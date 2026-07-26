<?php

namespace App\Validators;

use Respect\Validation\Validator as v;
use App\Validators\BaseValidator;

class GetResetPassCodeValidator extends BaseValidator
{
    private $validations;
    public function __construct()
    {
        $this->validations = [
            'email' => v::key('email', v::email()->notEmpty()),
        ];
        parent::__construct($this->validations);
    }
}
