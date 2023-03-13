<?php

class Route {
  private static array $routesGet = [];

  static public function get(string $route, array $callback) : void {
    self::$routesGet[] = new _Route($route, $callback);
  }

  static public function start() : void {
    $method = 'routes' . ucfirst(strtolower($_SERVER['REQUEST_METHOD']));

    foreach(self::$$method as $route) {
      $r = self::clean($route->route);
      $q = self::clean($_SERVER['REQUEST_URI']);

      $rParam = [];

      foreach(explode('/', $r) as $key => $value) {
        $regex = '/^:/';
        if (preg_match($regex, $value))
          $rParam[$key] = preg_replace($regex, '', $value);
      }

      $qArr = explode('/', $q);
      $rParamsRequest = [];

      foreach($rParam as $key => $value) {
        if (!isset($qArr[$key])) return;
        $rParamsRequest[$value] = $qArr[$key];
        $qArr[$key] = ':.*';
      }

      $q = str_replace('/', '\/', implode('/', $qArr));

      if (preg_match("/$q/", $r))
        ($route->callback)(...$rParamsRequest);
    }

    die();
  }

  static private function clean(string $string) {
    return preg_replace('/(^\/)|(\/$)/', '', $string);
  }
}