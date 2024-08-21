<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        try {
            $users = $this->userService->getAllUsers();

            if (!is_array($users)) {
                return $this->errorResponse('Formato de datos incorrecto.', 'La respuesta del servicio no es un array.');
            }
            
            return $this->successResponse(new UserCollection($users));
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener todos los usuarios.', $e->getMessage());

        }
    }

    public function create()
    {
        //
    }

    public function show($id)
    {
        try {
            $user = $this->userService->getUserById($id);

            if (!$user) {
                return $this->errorResponse('No existe el usuario.');
            }
            
            return $this->successResponse(new UserResource($user));
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener el usuario.', $e->getMessage());

        }
    }

    public function update(
        $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
