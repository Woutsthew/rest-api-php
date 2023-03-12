<?php

class TodosController {

  public static function todos() {
    echo 'all todos';
  }

  public static function todosById(string $id) {
    echo 'todo' . $id;
  }

}