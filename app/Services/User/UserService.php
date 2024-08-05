<?php

// app/Services/UserService.php

namespace App\Services\User;

use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepository;
use App\DTO\User\UserDTO;
use MongoDB\BSON\UTCDateTime;

class UserService
{
    protected $client;
    protected $collection;
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser($data)
    {
        $userDTO = new UserDTO(
            $data['name'],
            $data['email'],
            Hash::make($data['password']),
            $data['phone'] ?? null,
            $data['address'] ?? null,
            isset($data['date_of_birth']) ? new \DateTime($data['date_of_birth']) : null,
            $data['profile_picture'] ?? null,
            $data['bio'] ?? null,
            new UTCDateTime(),
            new UTCDateTime()
        );

        $this->userRepository->create($userDTO);

        return $userDTO;
    }
}
