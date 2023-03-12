<?php
require_once('./src/controllers/todos.php');

Route::get('todos', [TodosController::class, 'todos']);
Route::get('todos/:id', [TodosController::class, 'todosById']);