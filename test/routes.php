<?php

use App\Controllers\ToDoController;
use App\Services\Router\Router;

Router::get('tasks', [ToDoController::class, 'index']);
Router::post(
    'tasks/create',
    [ToDoController::class, 'create']
);
Router::post(
    'tasks/finish',
    [ToDoController::class, 'finish']
);
Router::post(
    'tasks/update',
    [ToDoController::class, 'update']
);
Router::post(
    'tasks/delete',
    [ToDoController::class, 'delete']
);

