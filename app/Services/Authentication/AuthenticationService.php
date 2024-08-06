<?php

// app/Services/UserService.php

namespace App\Services\Authentication;

use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepository;
use App\DTO\User\UserDTO;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;

class AuthenticationService
{
    protected $client;
    protected $collection;
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(UserDTO $userDTO)
    {
        $userData = [
            'name' => $userDTO->name,
            'email' => $userDTO->email,
            'password' => Hash::make($userDTO->password), // Asegúrate de encriptar la contraseña
            'phone' => $userDTO->phone,
            'address' => $userDTO->address,
            'date_of_birth' => $userDTO->date_of_birth,
            'profile_picture' => $userDTO->profile_picture,
            'bio' => $userDTO->bio,
            'created_at' => $userDTO->created_at,
            'updated_at' => $userDTO->updated_at,
        ];

        try {
            $this->userRepository->create($userData);
            return $userDTO;
        } catch (\Exception $e) {
            // Manejo de errores
            Log::error("Error al registrar el usuario: " . $e->getMessage());
            throw new \RuntimeException("No se pudo registrar el usuario, por favor intente nuevamente.");
        }
    }
}
