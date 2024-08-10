<?php

namespace App\Exceptions\Authentication;

use Exception;

class LoginException extends Exception
{
    public function __construct($message = "Error durante el inicio de sesion del usuario.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
