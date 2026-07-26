<?php

namespace App\Validators;

use Respect\Validation\Validator as v;

class BaseValidator
{



    private $missingFields = [];
    private $validations = [];

    public function __construct($validations = [])
    {
        $this->validations = $validations;
    }
    public function validate($inputs = [])
    {
        try {
            foreach ($this->validations as $name => $keyValidator) {
                try {
                    $keyValidator->check($inputs);
                } catch (\Throwable $th) {
                    $this->missingFields[] = $name;
                }
            }
            if (count($this->missingFields) > 0) {
                return false;
            }
            return true;
        } catch (\Throwable $th) {
            $this->missingFields[] = $th->getMessage();
            return false;
        }
    }

    public function getFileds()
    {
        return $this->missingFields;
    }
}
