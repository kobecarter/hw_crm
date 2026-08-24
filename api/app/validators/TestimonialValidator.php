<?php

namespace App\Validators;

use Respect\Validation\Validator as v;
use App\Validators\BaseValidator;

class TestimonialValidator extends BaseValidator
{

    private $validations;
    public function __construct()
    {
        $this->validations  = [
            'fonction' => v::key('fonction', v::stringType()->notEmpty()),
            'temoignage' => v::key('temoignage', v::stringType()->notEmpty()),
            'note' => v::key('note', v::intType()->between(1, 5)),
        ];
        parent::__construct($this->validations);
    }
}
