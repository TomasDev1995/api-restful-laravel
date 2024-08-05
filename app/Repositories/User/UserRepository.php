<?php

namespace App\Repositories\User;

use App\DTO\User\UserDTO;
use App\Services\Database\MongoDBConnectionService;

class UserRepository
{
    protected $collection;

    public function __construct(MongoDBConnectionService $mongoDBConnectionService)
    {
        $this->collection = $mongoDBConnectionService->getCollection('users');
    }

    public function create(UserDTO $userDTO)
    {
        $user = [
            'name' => $userDTO->name,
            'email' => $userDTO->email,
            'password' => $userDTO->password,
            'created_at' => $userDTO->created_at,
            'updated_at' => $userDTO->updated_at,
        ];

        $this->collection->insertOne($user);
    }
}
