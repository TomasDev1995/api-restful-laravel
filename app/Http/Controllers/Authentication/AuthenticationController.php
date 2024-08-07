<?php

namespace App\Http\Controllers\Authentication;

use App\Contracts\ValidatesRequestInterface;
use App\DTO\User\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Authentication\AuthenticationService;
use Illuminate\Support\Facades\Log;

class AuthenticationController extends Controller
{
    protected $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    // Registro de usuario
    public function register(RegisterRequest $registerRequest)
    {
        try {
            $data = $this->extractValidatedData($registerRequest);
            $userDTO = $this->createUserDTO($data);
            $this->authenticationService->registerUser($userDTO);
            return $this->successResponse($userDTO);
        } catch (\Exception $e) {
            return $this->handleAuthenticationException($e, $registerRequest);
        }
    }
    
    public function login(LoginRequest $loginRequest)
    {
        try {
            $data = $loginRequest->validated();
            $userDTO = UserDTO::fromLoginData($data['email'], $data['password']);
            $this->authenticationService->login($userDTO);
        } catch (\Exception $e) {
            return $this->handleRegistrationException($e, $loginRequest);
        }
    }

    private function extractValidatedData(ValidatesRequestInterface $request): array
    {
        return $request->validated();
    }

    private function createUserDTO(array $data): UserDTO
    {
        return new UserDTO(
            $data['name'] ?? null,
            $data['email'] ?? null,
            $data['password'] ?? null,
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['date_of_birth'] ?? null,
            $data['profile_picture'] ?? null,
            $data['bio'] ?? null,
            now()->format('d-m-Y H:i:s'),
            now()->format('d-m-Y H:i:s')
        );
    }

    private function handleAuthenticationException(\Exception $e, RegisterRequest $registerRequest)
    {
        Log::error("Error en el registro de usuario: " . $e->getMessage() . " - Datos recibidos: " . json_encode($registerRequest->all()));
        return response()->json(['error' => 'Registro fallido, por favor intente nuevamente.'], 500);
    }

    private function successResponse(UserDTO $userDTO)
    {
        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'data' => new UserResource($userDTO)
        ], 201);
    }
}
