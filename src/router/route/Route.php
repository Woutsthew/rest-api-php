<?php

class Route {
  static private array $routesGet = [];
  static private array $routesPost = [];
  static private array $routesPatch = [];
  static private array $routesDelete = [];

  static public function get(string $route, array $callback) : void {
    self::route("GET", $route, $callback);
  }

  static public function post(string $route, array $callback) : void {
    self::route("POST", $route, $callback);
  }

  static public function patch(string $route, array $callback) : void {
    self::route("PATCH", $route, $callback);
  }

  static public function delete(string $route, array $callback) : void {
    self::route("DELETE", $route, $callback);
  }

  static private function route(string $method, string $route, array $callback) : void {
    switch ($method) {
      case 'GET': self::$routesGet[] = new RouteAction($route, $callback); break;
      case 'POST': self::$routesPost[] = new RouteAction($route, $callback); break;
      case 'PATCH': self::$routesPatch[] = new RouteAction($route, $callback); break;
      case 'DELETE': self::$routesDelete[] = new RouteAction($route, $callback); break;

      default: throw new Exception('Method Not Allowed', 405); break;
    }
  }

  static public function search() : void {
    $method = 'routes' . ucfirst(strtolower($_SERVER['REQUEST_METHOD']));

    foreach(self::$$method as $route) {
      $routeURI = preg_replace('/(^\/)|(\/$)/', '', $route->route);
      $requestURI = preg_replace('/(^\/)|(\/$)/', '', explode("?", $_SERVER["REQUEST_URI"])[0]);

      $requestURIArray = explode('/', $requestURI);
      $args = [];

      foreach(explode('/', $routeURI) as $key => $value) {
        if (preg_match('/^:/', $value)) {
          if (!isset($requestURIArray[$key])) continue;
          $args[preg_replace('/^:/', '', $value)] = $requestURIArray[$key];
          $requestURIArray[$key] = ':.*';
        }
      }

      $requestURI = str_replace('/', '\/', implode('/', $requestURIArray));

      if (preg_match("/$requestURI/", $routeURI)) {
        ($route->callback)(...$args);
        exit;
      }
    }

    throw new Exception('Not Found', 404);
  }
}