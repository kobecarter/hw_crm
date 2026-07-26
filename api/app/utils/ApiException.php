<?php

namespace App\Utils;

use App\Utils\ResponseMessages;

class ApiException extends \Exception
{
    private $statusCode;
    private $data;
    public function __construct(
        $data = array(),
        $statusCode = 500,
        $message = "",
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
        $this->statusCode = $statusCode;
    }


    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getData()
    {
        return $this->data ?? ResponseMessages::messages('generalError');
    }
}
