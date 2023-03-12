<?php
require_once('./src/utils/index.php');
require_once('./src/routes/RouteConf.php');
require_once('./src/routes/Route.php');
require_once('./src/routes/index.php');
require_once('./src/routes/RouteDispatcher.php');

$method = ucfirst(strtolower($_SERVER['REQUEST_METHOD']));
$methodName = 'routes' . $method;

foreach(Route::$methodName() as $r) {
  $qwe = new RouteDispatcher($r);
  $qwe->process();
}