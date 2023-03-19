<?php
require_once('./src/utils/SQLBuilder.php');
require_once('./src/utils/File.php');

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