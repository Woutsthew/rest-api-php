<?php

class TodosController {
  public function __construct(private TodosModel $model) {}

  public function processRequest(string $method, ?string $resource) : array | object {
    if ($resource) return $this->processResourceRequest($method, $resource);
    else return $this->processCollectionRequest($method);
  }

  private function processResourceRequest(string $method, string $id) : array | object {
    switch ($method) {
      case 'GET': return $this->model->getById($id); break;
      case 'PUT': throw new Exception('Not Implemented', 501); break;
      case 'PATCH': return $this->updateResource($id); break;
      case 'DELETE': return $this->deleteResource($id); break;
      default:
        header('Allow: GET, PATCH, DELETE');
        throw new Exception('Method Not Allowed', 405);
    }
  }

  private function updateResource(string $id) : array {
    $data = json_decode(file_get_contents('php://input'), true) ?? array();
    $response['id'] = $this->model->update($id, $data);
    return $response;
  }

  private function deleteResource(string $id) : array {
    $isStrong = json_decode(file_get_contents('php://input'), true)['isStrong'] ?? false;
    $response['id'] = $this->model->delete($id, $isStrong);
    return $response;
  }

  private function processCollectionRequest(string $method) {
    switch ($method) {
      case 'GET': return $this->getCollection(); break;
      case "POST": return $this->createResourceCollection(); break;
      default:
        header('Allow: GET, POST');
        throw new Exception('Method Not Allowed', 405);
    }
  }

  private function getCollection() : array {
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 0;
    $size = isset($_GET['size']) ? (int) $_GET['size'] : 20;
    $filter = ['title' => '', 'body' => ''];
    $filter = array_intersect_key($_GET, $filter);

    $todos = $this->model->get($filter);
    $response['page'] = $page;
    $response['size'] = $size;
    $response['count'] = count($todos);
    $response['todos'] =  array_slice($todos, $page * $size, $size);
    return $response;
  }

  private function createResourceCollection() : array {
    $data = !empty($_POST) ? $_POST
      : json_decode(file_get_contents('php://input'), true);

    $response['id'] = $this->model->create($data);
    return $response;
  }
}