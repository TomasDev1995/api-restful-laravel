<?php

namespace App\Services\Database;

use MongoDB\Client as MongoClient;
use MongoDB\Collection;
use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;
use MongoDB\DeleteResult;

class MongoDBConnectionService
{

    protected $client;
    protected $database;

    public function __construct()
    {
        $host = config('database.connections.mongodb.host');
        $port = config('database.connections.mongodb.port');
        $database = config('database.connections.mongodb.database');
        $username = config('database.connections.mongodb.username');
        $password = config('database.connections.mongodb.password');
        $authDatabase = config('database.connections.mongodb.options.database');

        $dsn = "mongodb://{$username}:{$password}@{$host}:{$port}/{$database}";

        $this->client = new MongoClient($dsn);
        $this->database = $this->client->$database;
    }

    /**
     * Obtiene una colección de MongoDB.
     *
     * @param string $collectionName
     * @return Collection
     */
    public function getCollection($collectionName): Collection
    {
        return $this->database->selectCollection($collectionName);
    }

    /**
     * Inserta un documento en una colección.
     *
     * @param string $collectionName
     * @param array $document
     * @return InsertOneResult
     */
    public function insertDocument($collectionName, array $document): InsertOneResult
    {
        $collection = $this->getCollection($collectionName);
        return $collection->insertOne($document);
    }

    /**
     * Encuentra un documento en una colección.
     *
     * @param string $collectionName
     * @param array $filter
     * @return array|null
     */
    public function findDocument($collectionName, array $filter): ?array
    {
        $collection = $this->getCollection($collectionName);
        return $collection->findOne($filter);
    }

    /**
     * Actualiza un documento en una colección.
     *
     * @param string $collectionName
     * @param array $filter
     * @param array $update
     * @param array $options
     * @return UpdateResult
     */
    public function updateDocument($collectionName, array $filter, array $update, array $options = []): UpdateResult
    {
        $collection = $this->getCollection($collectionName);
        return $collection->updateOne($filter, $update, $options);
    }

    /**
     * Elimina un documento de una colección.
     *
     * @param string $collectionName
     * @param array $filter
     * @return DeleteResult
     */
    public function deleteDocument($collectionName, array $filter): DeleteResult
    {
        $collection = $this->getCollection($collectionName);
        return $collection->deleteOne($filter);
    }

    /**
     * Cuenta los documentos en una colección que coinciden con el filtro.
     *
     * @param string $collectionName
     * @param array $filter
     * @return int
     */
    public function countDocuments($collectionName, array $filter = []): int
    {
        $collection = $this->getCollection($collectionName);
        return $collection->countDocuments($filter);
    }

    /**
     * Encuentra múltiples documentos en una colección.
     *
     * @param string $collectionName
     * @param array $filter
     * @param array $options
     * @return Cursor
     */
    public function findDocuments($collectionName, array $filter = [], array $options = [])
    {
        $collection = $this->getCollection($collectionName);
        return $collection->find($filter, $options);
    }

    /**
     * Inserta múltiples documentos en una colección.
     *
     * @param string $collectionName
     * @param array $documents
     * @return MongoDB\InsertManyResult
     */
    public function insertDocuments($collectionName, array $documents): \MongoDB\InsertManyResult
    {
        $collection = $this->getCollection($collectionName);
        return $collection->insertMany($documents);
    }

    /**
     * Actualiza múltiples documentos en una colección.
     *
     * @param string $collectionName
     * @param array $filter
     * @param array $update
     * @param array $options
     * @return UpdateResult
     */
    public function updateDocuments($collectionName, array $filter, array $update, array $options = []): UpdateResult
    {
        $collection = $this->getCollection($collectionName);
        return $collection->updateMany($filter, $update, $options);
    }

    /**
     * Elimina múltiples documentos de una colección.
     *
     * @param string $collectionName
     * @param array $filter
     * @return DeleteResult
     */
    public function deleteDocuments($collectionName, array $filter): DeleteResult
    {
        $collection = $this->getCollection($collectionName);
        return $collection->deleteMany($filter);
    }
}
