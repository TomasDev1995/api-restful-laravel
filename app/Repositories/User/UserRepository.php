<?php

namespace App\Repositories\User;

use App\Services\Database\MongoDBConnectionService;
use Illuminate\Support\Facades\Log;
use MongoDB\Model\BSONDocument;
use MongoDB\BSON\ObjectId;
use MongoDB\Exception\RuntimeException as MongoDBRuntimeException;

class UserRepository
{
    protected $collection;

    public function __construct(MongoDBConnectionService $mongoDBConnectionService)
    {
        $this->collection = $mongoDBConnectionService->getCollection('users');
    }

    public function create(array $userArray): BSONDocument
    {
        try {
            $result = $this->collection->insertOne($userArray);
            return $this->collection->findOne(['_id' => $result->getInsertedId()]) ?: null;
        } catch (MongoDBRuntimeException $e) {
            Log::error('Error al insertar el usuario en MongoDB', [
                'error' => $e->getMessage(),
                'user_data' => $userArray
            ]);
            throw new MongoDBRuntimeException('Error al insertar el usuario en MongoDB.', 0, $e->getMessage());
        }
    }

    /**
     * Encuentra un usuario por su email.
     *
     * @param string $email
     * @return \MongoDB\Model\BSONDocument|null
     */
    public function findByEmail(string $email): ?BSONDocument
    {
        try {
            return $this->collection->findOne(['email' => $email]) ?: null;
        } catch (MongoDBRuntimeException $e) {
            Log::error('Error al buscar el usuario por email en MongoDB', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);
            throw new \RuntimeException('Error al buscar el usuario por email en MongoDB.', 0, $e->getMessage());
        }
    }

    /**
     * Obtiene todos los usuarios de la colección.
     *
     * @return array
     */
    public function getAllUsers()
    {
        try {
            $cursor = $this->collection->find();
            $usersArray = $cursor->toArray();
            
            foreach ($usersArray as $user) {
                if (!$user instanceof \MongoDB\Model\BSONDocument) {
                    throw new \RuntimeException('Documento de usuario en formato inesperado.');
                }
            }

            return $usersArray;
        } catch (MongoDBRuntimeException $e) {
            Log::error('Error al obtener todos los usuarios.', [
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException('Error al obtener los usuarios.', 0, $e);
        }
    }

    public function getUserById($id)
    {
        try {
            $userId = new ObjectId($id);
            return $this->collection->findOne(['_id' => $userId]) ?: null;
        } catch (MongoDBRuntimeException $e) {
            Log::error('Error al obtener el usuario.', [
                'error' => $e->getMessage()
            ]);
            throw new \RuntimeException('Error al obtener el usuario.', 0, $e);
        }
    }

}
