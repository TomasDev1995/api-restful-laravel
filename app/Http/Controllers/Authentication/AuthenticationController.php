<?php

namespace App\Http\Controllers\Authentication;

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


class AuthenticationController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // Registro de usuario
    public function register(RegisterRequest $registerRequest)
    {
        $data = $registerRequest->validated();
       
        $user = $this->userService->registerUser($data);

        return (new UserResource($user))->response()->setStatusCode(201);
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
