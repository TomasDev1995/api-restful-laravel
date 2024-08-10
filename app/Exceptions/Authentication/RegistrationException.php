<?php

namespace App\Exceptions\Authentication;

use Exception;

class RegistrationException extends Exception
{
    public function __construct($message = "Error durante el registro del usuario.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
