<?php


namespace App\Validators;

use Respect\Validation\Validator as v;
use App\Validators\BaseValidator;

class ResetPasswordValidator extends BaseValidator
{
    private $validations;
    public function __construct()
    {
        $this->validations = [
            'new_password' => v::key('new_password', v::stringType()->notEmpty()),
            'confirm_password' => v::key('confirm_password', v::stringType()->notEmpty()),
            'code' => v::key('code', v::stringType()->notEmpty()),
        ];
        parent::__construct($this->validations);
    }
}
