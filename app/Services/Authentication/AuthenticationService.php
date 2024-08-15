<?php

// app/Services/UserService.php

namespace App\Services\Authentication;

use App\Repositories\User\UserRepository;
use App\Services\Security\PasswordHasher;
use App\DTO\User\UserDTO;
use App\Exceptions\Authentication\AuthenticationException;
use App\Exceptions\Authentication\RegistrationException;
use App\Exceptions\Authentication\LoginException;
use App\Exceptions\Authentication\UserNotFoundException;
use App\Exceptions\IncorrectPasswordException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationService
{
    protected $userRepository;
    protected $passwordHasher;

    public function __construct(UserRepository $userRepository, PasswordHasher $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function registerUser(UserDTO $userDTO)
    {
        try {
            return $this->createUser($userDTO); 
        } catch (Exception $e) {
            return $this->handleRegistrationError($e);
        }
    }

    public function authenticateUser(UserDTO $userDTO)
    {
        try {
            $user = $this->findUserByEmail($userDTO);
            //dd($user->password." | ".$userDTO->password);
            $this->verifyPassword($userDTO->password, $user->password);
            return $this->generateToken($userDTO);
        } catch (Exception $e) {
           return $this->handleLoginError($e);
        }
    }

    private function findUserByEmail(UserDTO $userDTO): User|null
    {
        $user = $this->userRepository->findByEmail($userDTO->email);
        if(!$user){
            Log::error("Usuario no encontrado: $userDTO->email");
            throw new Exception('Usuario no encontrado');
        }

        return $user;
    }

    private function verifyPassword(string $password, string $hashedPassword): bool
    {
        $validated = $this->passwordHasher->check($password, $hashedPassword);
        if(!$validated){
            Log::error("Contraseña incorrecta.");
            throw new Exception('Contraseña incorrecta');
        }
        
        return $validated;
    }

    private function generateToken(UserDTO $userDTO): string
    {
        $credentials = ["email"=>$userDTO->email, "password"=>$userDTO->password];

        if (!$token = JWTAuth::attempt($credentials)) {
            Log::error("Error al generar token de acceso");
            throw new Exception("Error al generar token de acceso");
        }
        
        return $token; 
    }

    private function createUser(UserDTO $userDTO): null|array|object
    {
        $this->passwordHasher->hash($userDTO->password);
        return $this->userRepository->create($this->convertDTOToArray($userDTO));
    }

    private function convertDTOToArray(UserDTO $userDTO): array
    {
        return [
            'name' => $userDTO->name,
            'email' => $userDTO->email,
            'password' => $userDTO->password,
            'phone' => $userDTO->phone,
            'address' => $userDTO->address,
            'date_of_birth' => $userDTO->date_of_birth,
            'profile_picture' => $userDTO->profile_picture,
            'bio' => $userDTO->bio,
            'created_at' => $userDTO->created_at,
            'updated_at' => $userDTO->updated_at,
            'token' => $userDTO->token,
        ];
    }

    private function handleRegistrationError(Exception $e): void
    {
        Log::error("Error al registrar el usuario: " . $e->getMessage());
        throw new Exception("No se pudo registrar el usuario, por favor intente nuevamente.", 0, $e);
    }

    private function handleLoginError(Exception $e): array
    {
        Log::error("Error de autenticacion: " . $e->getMessage());
        throw new Exception ($e->getMessage());
    }
}
