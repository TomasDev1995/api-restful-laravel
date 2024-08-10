<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Database\MongoDBConnectionService;
use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;

class UserController extends Controller
{
    protected $client;
    protected $collection;

    public function __construct(MongoDBConnectionService $mongoDBConnectionService)
    {
        $this->collection = $mongoDBConnectionService->getCollection('users');
    }

    public function index()
    {
        $usersCursor = $this->collection->find();
        
        // Convertir el cursor a un array
        $users = $usersCursor->toArray();
        
        // Devolver la respuesta JSON
        return response()->json($users);
    }

    public function show($id)
    {
        $user = $this->collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this->collection->insertOne($data);
        return response()->json(['message' => 'User created successfully']);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->collection->updateOne(['_id' => new \MongoDB\BSON\ObjectId($id)], ['$set' => $data]);
        return response()->json(['message' => 'User updated successfully']);
    }

    public function destroy($id)
    {
        $this->collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        return response()->json(['message' => 'User deleted successfully']);
    }
}
