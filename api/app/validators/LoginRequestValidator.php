<?php

namespace App\Validators;

use Respect\Validation\Validator as v;
use Respect\Validation\Rules;

class LoginRequestValidator
{
    private $fileds = [
        'login',
        'password'
    ];
    public function validate($inputs = [])
    {
        $validation =  v::keySet(
            v::key('login', v::stringType()->notEmpty()),
            v::key('password', v::stringType()->notEmpty())
        )->validate($inputs);

        return $validation;
    }

    public function getFileds()
    {
        return $this->fileds;
    }
}
