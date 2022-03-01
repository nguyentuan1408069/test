<?php

namespace App\Models;

use App\Services\Model\ModelAbstract as Model;

class Task extends Model
{
    protected $fillable = [
        'name',
        'status',
        'priority'
    ];

    protected function getTable(): string
    {
        return 'tasks';
    }
}