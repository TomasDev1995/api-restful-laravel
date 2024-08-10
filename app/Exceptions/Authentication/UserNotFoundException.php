<?php

namespace App\Exceptions\Authentication;

use Exception;

class UserNotFoundException extends Exception
{
    public function __construct(string $message = 'Usuario no encontrado', int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
