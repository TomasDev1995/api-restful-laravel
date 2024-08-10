<?php

namespace App\DTO\User;

use MongoDB\BSON\UTCDateTime;

class UserDTO
{
    public ?string $name;
    public ?string $email;
    public ?string $password;
    public ?string $phone;
    public ?array $address;
    public ?\DateTime $date_of_birth;
    public ?string $profile_picture;
    public ?string $bio;
    public  $created_at;
    public  $updated_at;

    public function __construct(
        ?string $name = null,
        ?string $email = null,
        ?string $password = null,
        ?string $phone = null,
        ?array $address = null,
        ?\DateTime $date_of_birth = null,
        ?string $profile_picture = null,
        ?string $bio = null,
        $created_at = null,
        $updated_at = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->address = $address;
        $this->date_of_birth = $date_of_birth;
        $this->profile_picture = $profile_picture;
        $this->bio = $bio;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public static function setLoginData(string $email, string $password): self
    {
        return new self(
            null,
            $email,
            $password
        );
    }
}
