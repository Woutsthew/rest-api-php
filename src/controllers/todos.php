<?php

class TodosController {

  public static function todos() {
    die('all todos');
  }

  public static function todosById(string $id) {
    die('todo ' . $id);
  }

  public static function todosByIdWithComments(string $id) {
    die('todo ' . $id . ' with comments');
  }

  public static function todosByIdWithCommentsById(string $id, string $commentId) {
    die('todo ' . $id . ' with comment ' . $commentId);
  }
}