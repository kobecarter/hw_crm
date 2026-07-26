<?php

namespace App\Validators;

use Respect\Validation\Validator as v;
use Respect\Validation\Rules;

class LoginRequestValidator
{
    private $fileds = [
        'email',
        'password'
    ];
    public function validate($inputs = [])
    {
        $validation =  v::keySet(
            v::key('email', v::email()->notEmpty()),
            v::key('password', v::stringType()->notEmpty())
        )->validate($inputs);

        return $validation;
    }

    public function getFileds()
    {
        return $this->fileds;
    }
}
