<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Http\Resources\Authentication\RegistratedUserResource ;
use App\Http\Resources\Authentication\AuthenticatedUserResource ;
use App\Exceptions\Authentication\LoginException;
use App\Exceptions\Authentication\RegistrationException;

use App\Services\Authentication\AuthenticationService;

use App\DTO\User\UserDTO;
use App\Exceptions\Authentication\AuthenticationException;
use App\Exceptions\Authentication\UserCreationException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Controlador responsable de la autenticación de usuarios.
 * Maneja el registro y el inicio de sesión.
 */
class AuthenticationController extends Controller
{
    /**
     * Servicio de autenticación que contiene la lógica principal.
     *
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * Constructor de la clase.
     * Aplica middleware de autenticación, excepto para los métodos de login y registro.
     *
     * @param AuthenticationService $authenticationService
     * @return void
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->authenticationService = $authenticationService;
    }

    /**
     * Registra un nuevo usuario en el sistema.
     * 
     * @param RegisterRequest $registerRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $registerRequest)
    {
        try {
            $validatedData = $registerRequest->validated();
            $validatedData['date_of_birth'] = $this->validateAndConvertDate($validatedData['date_of_birth'] ?? null);
            $userDTO = $this->setUserDTO($validatedData);
            $userDocument = $this->authenticationService->registerUser($userDTO);

            return response()->json([
                "message" => "Usuario registrado",
                "code" => 201,
                "resource" => new RegistratedUserResource($userDocument),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al registrar el usuario',
                'details' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * Inicia sesión un usuario existente.
     * 
     * @param LoginRequest $loginRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $loginRequest)
    {
        try {
            $userDTO = $this->setUserDTO($loginRequest->validated());
            $authenticatedUser = $this->authenticationService->authenticateUser($userDTO);

            return response()->json([
                "message" => "Usuario autenticado",
                "code" => 200,
                "resource" => new AuthenticatedUserResource($authenticatedUser["user"]),
                "access_token" => $authenticatedUser["accessToken"]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Valida y convierte la fecha de nacimiento en un objeto DateTime.
     * 
     * @param string|null $dateOfBirth
     * @return \DateTime|null
     */
    private function validateAndConvertDate(?string $dateOfBirth): ?\DateTime
    {
        if (is_null($dateOfBirth)) {
            return null;
        }
        return new \DateTime($dateOfBirth);
    }

    /**
     * Crea un DTO de usuario a partir de los datos validados del registro/login.
     * 
     * @param array $validated Datos validados del registro.
     * @return UserDTO
     */
    private function setUserDTO(array $validated): UserDTO
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
}
