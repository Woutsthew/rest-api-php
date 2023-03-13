<?php
require_once('./src/router/route/index.php');
require_once('./src/controllers/todos.php');

//Route::get('todos', [TodosController::class, 'todos']);
//Route::get('todos/:id', [TodosController::class, 'todosById']);
//Route::get('todos/:id/comments', [TodosController::class, 'todosByIdWithComments']);
Route::get('todos/:id/comments/:commentId', [TodosController::class, 'todosByIdWithCommentsById']);

Route::start();