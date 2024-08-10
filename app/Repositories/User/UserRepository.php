<?php

namespace App\Repositories\User;

use App\DTO\User\UserDTO;
use App\Exceptions\Authentication\UserNotFoundException;
use App\Models\User;
use App\Services\Database\MongoDBConnectionService;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\ObjectId;


class UserRepository
{
    protected $collection;

    public function __construct(MongoDBConnectionService $mongoDBConnectionService)
    {
        $this->collection = $mongoDBConnectionService->getCollection('users');
    }

    public function create(array $userData)
    {
        try {
            $this->collection->insertOne($userData);
        } catch (\Exception $e) {
            Log::error('Error al insertar el usuario', [
                'error' => $e->getMessage(),
                'user_data' => $userData
            ]);
            throw new \Exception("Error al insertar el usuario: " . $e->getMessage());
        }
    }

    /**
     * Encuentra un usuario por su email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): User|null
    {
        $user = json_decode(json_encode($this->collection->findOne(['email' => $email])), true);
        return new User($user);
    }
}
