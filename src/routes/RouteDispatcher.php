<?php

class RouteDispatcher {
  private RouteConf $rc;
  private string $requestURI = '';
  private array $routeParams = [];
  private array $routeParamsRequest = [];

  public function __construct(RouteConf $rc) {
    $this->rc = $rc;
  }

  public function process() {
    $this->save();
    $this->setParam();
    $this->makeRegexRequest();
    $this->run();
  }

  private function clean(string $str) {
    return preg_replace('/(^\/)|(\/$)/', '', $str);
  }

  private function save() {
    if ($_SERVER['REQUEST_URI'] !== '/') {
      $this->requestURI = $this->clean($_SERVER['REQUEST_URI']);
      $this->rc->route = $this->clean($this->rc->route);
    }
  }

  private function setParam() {
    $routeArr = explode('/', $this->rc->route);

    foreach($routeArr as $key => $value) {
      if (preg_match('/^:/', $value))
        $this->routeParams[$key] = preg_replace('/^:/', '', $value);
    }
  }

  private function makeRegexRequest() {
    $URIArr = explode('/', $this->requestURI);

    foreach($this->routeParams as $key => $value) {
      if (!isset($URIArr[$key])) return;
      $this->routeParamsRequest[$value] = $URIArr[$key];
      $URIArr[$key] = ':.*';
    }

    $this->requestURI = str_replace('/', '\/', implode('/', $URIArr));
  }

  private function run() {
    if (preg_match("/$this->requestURI/", $this->rc->route)) {
      $this->render();
    }
  }

  private function render() {

    $className = $this->rc->callback[0];
    $action = $this->rc->callback[1];
    (new $className)->$action(...$this->routeParamsRequest);

    die();
  }

}