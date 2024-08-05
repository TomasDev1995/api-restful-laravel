<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;

class UserController extends Controller
{
    protected $client;
    protected $collection;

    public function __construct()
    {
        $this->client = new MongoClient('mongodb://dicromo:adminpassword@localhost:27017');
        $this->collection = $this->client->dicromo_Db->users;
    }

    public function index()
    {
        $users = $this->collection->find()->toArray();
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
