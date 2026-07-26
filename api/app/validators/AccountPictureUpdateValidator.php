<?php

namespace App\Validators;

use Respect\Validation\Validator as v;
use App\Validators\BaseValidator;

class AccountPictureUpdateValidator extends BaseValidator
{

    private $validations;
    public function __construct()
    {
        $this->validations = [
            'photo' => v::key('photo', v::image())
        ];
        parent::__construct($this->validations);
    }
}
