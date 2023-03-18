<?php

class RouteAction {
  public function __construct(public string $route, public array $callback) {}
}