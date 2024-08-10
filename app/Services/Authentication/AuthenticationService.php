<?php

// app/Services/UserService.php

namespace App\Services\Authentication;

use App\Repositories\User\UserRepository;
use App\Services\Security\PasswordHasher;
use App\DTO\User\UserDTO;
use App\Exceptions\Authentication\RegistrationException;
use App\Exceptions\Authentication\LoginException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Claims\Collection as ClaimsCollection;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Payload;
use Tymon\JWTAuth\Validators\PayloadValidator;

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
        } catch (RegistrationException $e) {
            $this->handleRegistrationError($e);
        }
    }

    public function loginUser(UserDTO $userDTO)
    {
        try {
            $user = $this->findUserByEmail($userDTO->email);
            $this->verifyPassword($userDTO->password, $user->password);
            $token = $this->generateToken($user);

            return [
                'message' => 'Login exitoso',
                'token' => $token
            ];
        } catch (LoginException $e) {
           return $this->handleLoginError($e);
        }
    }

    private function findUserByEmail(string $email): User|null
    {
        $user = $this->userRepository->findByEmail($email);
        if(!$user){
            Log::error("Usuario no encontrado: $email");
            throw new LoginException('Usuario no encontrado');
        }

        return $user;
    }

    private function verifyPassword(string $password, string $hashedPassword): bool
    {
        $validated = $this->passwordHasher->check($password, $hashedPassword);
        if(!$validated){
            Log::error("Contraseña incorrecta.");
            throw new LoginException('Contraseña incorrecta');
        }
        
        return $validated;
    }

    private function generateToken(User $user): string
    {
        $credentials = ["email"=>$user->email, "password"=>$user->password];

        if (!$token = JWTAuth::attempt($credentials)) {
            Log::error("Error al generar token de acceso");
            throw new LoginException("Error al generar token de acceso");
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]); 
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

    private function handleRegistrationError(RegistrationException $e): void
    {
        Log::error("Error al registrar el usuario: " . $e->getMessage());
        throw new RegistrationException("No se pudo registrar el usuario, por favor intente nuevamente.", 0, $e);
    }

    private function handleLoginError(LoginException $e): array
    {
        Log::error("Error de autenticacion: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}
