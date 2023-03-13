<?php
require_once('./src/router/route/index.php');
require_once('./src/controllers/todos.php');

//Route::get('todos', [TodosController::class, 'todos']);
Route::get('todos/:id/comment/:qwe', [TodosController::class, 'todosById']);

Route::start();