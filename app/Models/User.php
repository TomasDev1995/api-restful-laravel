<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    use \Illuminate\Foundation\Auth\Access\Authorizable, \Illuminate\Contracts\Auth\MustVerifyEmail;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'address', 'date_of_birth', 'profile_picture', 'bio'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'datetime',
    ];

    /**
     * Get the user's password attribute.
     *
     * @param string $password
     * @return string
     */
    public function getPasswordAttribute($password)
    {
        return $password;
    }
}
