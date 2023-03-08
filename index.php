<?php
require_once('./src/utils/index.php');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=UTF-8');

function exceptionHandler(Throwable $exception) : void {
  http_response_code((int)$exception->getCode());
  die(json_encode([
    'code' => $exception->getCode(),
    'message' => $exception->getMessage(),
    'file' => $exception->getFile(),
    'line' => $exception->getLine()
  ]));
}
set_exception_handler('exceptionHandler');

$routes = explode("/", explode("?", $_SERVER["REQUEST_URI"])[0])[1] ?? null;
$method = $_SERVER['REQUEST_METHOD'];
$response = null;

switch ($routes) {
  case 'todos':
    require_once('./src/models/todos.php');
    require_once('./src/controllers/todos.php');
    $model = new TodosModel("localhost", "todos", "todos", "todos");
    $controller = new TodosController($model);
    $response = $controller->processRequest($method);
    break;
  case 'foo':
    throw new Exception('Not Implemented', 501);
    break;
  default:
    throw new Exception('Not Found', 404);
}

die(json_encode($response));