<?php
require_once('./src/router/route/index.php');
require_once('./src/controllers/todos.php');

Route::get('todos', [TodosController::class, 'read']);
Route::get('todos/:id', [TodosController::class, 'readById']);
Route::get('todos/:id/image', [TodosController::class, 'readImageById']);

Route::post('todos', [TodosController::class, 'create']);
Route::post('todos/:id/image', [TodosController::class, 'updateImageById']);

Route::patch('todos/:id', [TodosController::class, 'updateById']);

Route::delete('todos/:id', [TodosController::class, 'deleteById']);

Route::search();