<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Hash;

class PasswordHasher
{
    /**
     * Hash the given password.
     *
     * @param string|null $password
     * @return string|null
     */
    public function hash(?string $password): ?string
    {
        return $password ? Hash::make($password) : null;
    }

    /**
     * Verify that the given password matches the hashed password.
     *
     * @param string $password
     * @param string $hashedPassword
     * @return bool
     */
    public function check(string $password, string $hashedPassword): bool
    {   
        return Hash::check($password, $hashedPassword);
    }
}
