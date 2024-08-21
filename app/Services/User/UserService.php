<?php

// app/Services/UserService.php

namespace App\Services\User;

use App\Repositories\User\UserRepository;

class UserService
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        $users = $this->userRepository->getAllUsers();
        
        if (!is_array($users)) {
            throw new \RuntimeException('El repositorio ha devuelto un formato de datos inesperado.');
        }

        return $users;
    }

    public function createUser()
    {
        //
    }

    public function getUserById($id)
    {
        return $this->userRepository->getUserById($id);
    }
}
