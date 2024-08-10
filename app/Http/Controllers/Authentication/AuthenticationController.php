<?php

namespace App\Http\Controllers\Authentication;

use App\Exceptions\Authentication\LoginException;
use App\Exceptions\Authentication\RegistrationException;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Http\Resources\User\UserResource;

use App\Services\Authentication\AuthenticationService;

use App\DTO\User\UserDTO;

use Illuminate\Support\Facades\Log;

class AuthenticationController extends Controller
{
    protected $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->authenticationService = $authenticationService;
    }

    public function register(RegisterRequest $registerRequest)
    {
        try {
            return $this->authenticationService->registerUser($this->createUserDTO($registerRequest->validated()));
        } catch (RegistrationException $e) {
            return $this->handleRegistrationException($e, $registerRequest);
        }
    }
    
    public function login(LoginRequest $loginRequest)
    {
        try {
            return $this->authenticationService->loginUser($this->setDataLoginUserDTO($loginRequest->validated()));
        } catch (LoginException $e) {
            return $this->handleLoginException($e, $loginRequest);
        }
    }

    private function createUserDTO(array $validated): UserDTO
    {
        return new UserDTO(
            $validated['name'] ?? null,
            $validated['email'] ?? null,
            $validated['password'] ?? null,
            $validated['phone'] ?? null,
            $validated['address'] ?? null,
            $validated['date_of_birth'] ?? null,
            $validated['profile_picture'] ?? null,
            $validated['bio'] ?? null,
            now()->format('d-m-Y H:i:s'),
            now()->format('d-m-Y H:i:s')
        );
    }

    private function setDataLoginUserDTO(array $validated): UserDTO
    {
        return UserDTO::setLoginData(
            $validated['email'] ?? null,
            $validated['password'] ?? null,
        );
    }

    private function handleRegistrationException(RegistrationException $e)
    {
        Log::error("Error en el registro de usuario: " . $e->getMessage());
        return response()->json(['error' => 'Registro fallido, por favor intente nuevamente.'], 500);
    }

    private function handleLoginException(LoginException $e)
    {
        Log::error("Error en el inicio de sesion del usuario: " . $e->getMessage());
        return response()->json(['error' => 'Inicio de sesion fallido, por favor intente nuevamente.'], 500);
    }
}
