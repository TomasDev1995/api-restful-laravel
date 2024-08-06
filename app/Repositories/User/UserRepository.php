<?php

namespace App\Repositories\User;

use App\DTO\User\UserDTO;
use App\Services\Database\MongoDBConnectionService;
use DateTime;
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
            $result = $this->collection->insertOne($userData);
            return $result->getInsertedId();
        } catch (\Exception $e) {
            Log::error('Error al insertar el usuario', [
                'error' => $e->getMessage(),
                'user_data' => $userData
            ]);
            throw new \Exception("Error al insertar el usuario: " . $e->getMessage());
        }
    }

    public function getById(string $id)
    {
        try {
            $user = $this->collection->findOne(['_id' => new ObjectId($id)]);
            return $user;
        } catch (\Exception $e) {
            Log::error('Error al obtener el usuario', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new \Exception("Error al obtener el usuario: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        try {
            $users = $this->collection->find()->toArray();
            return $users;
        } catch (\Exception $e) {
            Log::error('Error al obtener los usuarios', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Error al obtener los usuarios: " . $e->getMessage());
        }
    }

    public function update(string $id, array $userData)
    {
        try {
            $result = $this->collection->updateOne(
                ['_id' => new ObjectId($id)],
                ['$set' => $userData]
            );
            return $result->getModifiedCount();
        } catch (\Exception $e) {
            Log::error('Error al actualizar el usuario', [
                'error' => $e->getMessage(),
                'id' => $id,
                'user_data' => $userData
            ]);
            throw new \Exception("Error al actualizar el usuario: " . $e->getMessage());
        }
    }

    public function delete(string $id)
    {
        try {
            $result = $this->collection->deleteOne(['_id' => new ObjectId($id)]);
            return $result->getDeletedCount();
        } catch (\Exception $e) {
            Log::error('Error al eliminar el usuario', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw new \Exception("Error al eliminar el usuario: " . $e->getMessage());
        }
    }
}
