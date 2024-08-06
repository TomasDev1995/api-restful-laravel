<?php

namespace App\Http\Controllers\Authentication;

use App\DTO\User\UserDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\RegisterRequest;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\User\UserService;
use App\Http\Resources\User\UserResource;
use App\Services\Authentication\AuthenticationService;
use DateTime;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

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
            $data = $registerRequest->validated();

            $userDTO = new UserDTO(
                $data['name'] ?? null,
                $data['email'] ?? null,
                $data['password'] ?? null,
                $data['phone'] ?? null,
                $data['address'] ?? null,
                isset($data['date_of_birth']) ? new \DateTime($data['date_of_birth']) : null,
                $data['profile_picture'] ?? null,
                $data['bio'] ?? null,
                now()->format('d-m-Y H:i:s'),
                now()->format('d-m-Y H:i:s')
            );

            $this->authenticationService->registerUser($userDTO);
        
            return (new UserResource($userDTO))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            Log::error("Error en el registro de usuario: " . $e->getMessage() . " - Datos recibidos: " . json_encode($registerRequest->all()));
            return response()->json(['error' => 'Registro fallido, por favor intente nuevamente.'], 500);
        }

    }

    // // Inicio de sesión
    // public function login(LoginRequest $loginRequest)
    // {
    //     $loginRequest->validate([
    //         'email' => 'required|string|email',
    //         'password' => 'required|string',
    //     ]);

    //     $user = $this->collection->findOne(['email' => $loginRequest->email]);

    //     if (!$user || !Hash::check($loginRequest->password, $user['password'])) {
    //         return response()->json(['message' => 'Invalid credentials'], 401);
    //     }

    //     try {
    //         // Crear un token JWT
    //         $token = JWTAuth::fromUser($user);
    //         return response()->json([
    //             'message' => 'Login successful',
    //             'token' => $token
    //         ]);
    //     } catch (JWTException $e) {
    //         return response()->json(['message' => 'Could not create token'], 500);
    //     }
    // }

    // // Logout
    // public function logout(Request $request)
    // {
    //     $request->validate([
    //         'token' => 'required|string',
    //     ]);

    //     try {
    //         JWTAuth::invalidate($request->token);
    //         return response()->json(['message' => 'Logout successful']);
    //     } catch (JWTException $e) {
    //         return response()->json(['message' => 'Token invalidation failed'], 500);
    //     }
    // }
}
