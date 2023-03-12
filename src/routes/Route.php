<?php

class Route {
  private static array $routesGet = [];

  static public function routesGet() {
    return self::$routesGet;
  }

  static public function get(string $route, array $callback) : void {
    self::$routesGet[] = new RouteConf($route, $callback);
  }
}