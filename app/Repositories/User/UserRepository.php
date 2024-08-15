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
use MongoDB\Model\BSONDocument;

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
    
            if ($result->getInsertedCount() !== 1) {
                throw new \RuntimeException('El documento no se insertó correctamente.');
            }
    
            return $this->collection->findOne(['_id' => $result->getInsertedId()]);
    
        } catch (Exception $e) {
            Log::error('Error al insertar el usuario en MongoDB', [
                'error' => $e->getMessage(),
                'user_data' => $userArray
            ]);
            throw new \RuntimeException('Error en la operación de MongoDB.');
        } catch (\Exception $e) {
            Log::error('Error desconocido al insertar el usuario en MongoDB', [
                'error' => $e->getMessage(),
                'user_data' => $userArray
            ]);
            throw new \RuntimeException('Error inesperado al insertar el usuario.');
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
        $userDocument = $this->collection->findOne(['email' => $email]);
        
        return $userDocument ?: null;
    }
}
