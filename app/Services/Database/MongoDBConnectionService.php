<?php

namespace App\Services\Database;

use MongoDB\Client as MongoClient;

class MongoDBConnectionService {
    
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

    public function getCollection($collectionName)
    {
        return $this->database->selectCollection($collectionName);
    }
}