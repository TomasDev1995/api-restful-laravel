<?php

// app/Services/UserService.php

namespace App\Services\Authentication;

use App\Repositories\User\UserRepository;
use App\Services\Security\PasswordHasher;
use App\DTO\User\UserDTO;
use App\Exceptions\Authentication\AuthenticationException;
use App\Models\User;
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
            $userData = $this->mapUserDTOToDataArray($userDTO);
            $this->createUser($userData);
            return $userDTO;
        } catch (\RuntimeException $e) {
            $this->handleRegistrationError($e);
        } catch (\Exception $e) {
            // En caso de que ocurra algún otro tipo de error inesperado
            $this->handleUnexpectedError($e);
        }
    }

    public function login(UserDTO $userDTO): array
    {
        try {
            $user = $this->findUserByEmail($userDTO->email);
            $this->verifyPassword($userDTO->password, $user['password']);
            $token = $this->generateToken($user);
            
            return [
                'message' => 'Login exitoso',
                'token' => $token
            ];
        } catch (\RuntimeException $e) {
            $this->handleAuthenticationError($e);
        } catch (\Exception $e) {
            $this->handleUnexpectedError($e);
        }
    }

    private function findUserByEmail(string $email): User
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            throw new \RuntimeException('Usuario no encontrado');
        }
        return $user;
    }

    private function verifyPassword(string $password, string $hashedPassword): void
    {
        if (!$this->passwordHasher->check($password, $hashedPassword)) {
            throw new \RuntimeException('Contraseña incorrecta');
        }
    }

    private function generateToken(User $user): string
    {
        try {
            // Genera el token para el usuario autenticado
            return JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            Log::error("Error al generar el token JWT: " . $e->getMessage());
            throw new \RuntimeException("No se pudo generar el token JWT. Inténtelo de nuevo.");
        }
    }

    private function handleAuthenticationError(\RuntimeException $e): void
    {
        Log::warning("Error en la autenticación: " . $e->getMessage());
        throw new AuthenticationException("Autenticación fallida. Verifique sus credenciales e intente nuevamente.", 0, $e);
    }

    private function mapUserDTOToDataArray(UserDTO $userDTO)
    {
        return [
            'name' => $userDTO->name,
            'email' => $userDTO->email,
            'password' => $this->passwordHasher->hash($userDTO->password),
            'phone' => $userDTO->phone,
            'address' => $userDTO->address,
            'date_of_birth' => $userDTO->date_of_birth,
            'profile_picture' => $userDTO->profile_picture,
            'bio' => $userDTO->bio,
            'created_at' => $userDTO->created_at,
            'updated_at' => $userDTO->updated_at,
        ];
    }
    
    private function createUser(array $userData): void
    {
        $this->userRepository->create($userData);
    }

    private function handleRegistrationError(\RuntimeException $e): void
    {
        Log::error("Error al registrar el usuario: " . $e->getMessage());
        throw new AuthenticationException("No se pudo registrar el usuario, por favor intente nuevamente.", 0, $e);
    }

    private function handleUnexpectedError(\Exception $e): void
    {
        Log::critical("Error inesperado: " . $e->getMessage());
        throw new \RuntimeException("Ha ocurrido un error inesperado. Por favor, intente más tarde.");
    }
}
