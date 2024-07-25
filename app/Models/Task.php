<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Task extends Eloquent
{

    protected $connection = 'mongodb';
    protected $collection = 'tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 'title', 'description', 'status', 'priority', 'due_date', 'tags', 'comments'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'comments' => 'array',
        'tags' => 'array',
    ];
}
