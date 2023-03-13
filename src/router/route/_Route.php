<?php

class _Route {
  public string $route;
  public array $callback;

  public function __construct(string $route, array $callback) {
    $this->route = $route;
    $this->callback = $callback;
  }
}