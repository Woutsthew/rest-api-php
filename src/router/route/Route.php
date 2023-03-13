<?php

class Route {
  private static array $routesGet = [];

  static public function get(string $route, array $callback) : void {
    self::$routesGet[] = new _Route($route, $callback);
  }

  static public function start() : void {
    $method = 'routes' . ucfirst(strtolower($_SERVER['REQUEST_METHOD']));

    foreach(self::$$method as $route) {
      $routeURI = self::clean($route->route);
      $requestURI = self::clean($_SERVER['REQUEST_URI']);

      $requestURIArray = explode('/', $requestURI);
      $args = [];
      $regex = '/^:/';

      foreach(explode('/', $routeURI) as $key => $value)
        if (preg_match($regex, $value)) {
          if (!isset($requestURIArray[$key])) return;
          $args[preg_replace($regex, '', $value)] = $requestURIArray[$key];
          $requestURIArray[$key] = ':.*';
        }

      $requestURI = str_replace('/', '\/', implode('/', $requestURIArray));

      if (preg_match("/$requestURI/", $routeURI)) {
        ($route->callback)(...$args);
        exit;
      }
    }

    die('route not found');
  }

  static private function clean(string $string) {
    return preg_replace('/(^\/)|(\/$)/', '', $string);
  }
}