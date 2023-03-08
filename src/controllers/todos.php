<?php

class TodosController {
  private array $fields = ['title' => '', 'body' => '', 'isDelete' => ''];
  public function __construct(private TodosModel $model) {}

  public function processRequest(string $method) : array | object {
    $resource = explode("/", explode("?", $_SERVER["REQUEST_URI"])[0])[2] ?? null;
    if ($resource) return $this->processResourceRequest($method, $resource);
    else return $this->processCollectionRequest($method);
  }

  private function processResourceRequest(string $method, string $id) : array | object {
    $routes = explode("/", explode("?", $_SERVER["REQUEST_URI"])[0])[3] ?? null;
    switch ($method) {
      case 'GET': return $this->model->getById($id); break;
      case 'POST': if ($routes === 'image') return $this->updateImageResource($id); break;
      case 'PUT': throw new Exception('Not Implemented', 501); break;
      case 'PATCH': return $this->updateResource($id); break;
      case 'DELETE': return $this->deleteResource($id); break;
      default:
        header('Allow: GET, POST, PATCH, DELETE');
        throw new Exception('Method Not Allowed', 405);
    }
  }

  private function updateResource(string $id) : array {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $data = array_intersect_key($data, $this->fields);
    if (empty($data)) throw new Exception('Fields not specified', 400);
    $response['id'] = $this->model->update($id, $data);
    return $response;
  }

  private function updateImageResource(string $id) : array {
    $fileImage = $_FILES['image'] ??= null;
    if ($fileImage === null) throw new Exception('Image not specified', 400);
    $data['image'] = uploadImage($fileImage);
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
      case 'POST': return $this->createResourceCollection(); break;
      default:
        header('Allow: GET, POST');
        throw new Exception('Method Not Allowed', 405);
    }
  }

  private function getCollection() : array {
    $page = (int) $_GET['page'] ??= 0;
    $size = (int) $_GET['size'] ??= 20;
    $filter = array_intersect_key($_GET, $this->fields);

    $todos = $this->model->get($filter);
    $response['page'] = $page;
    $response['size'] = $size;
    $response['count'] = count($todos);
    $response['todos'] = array_slice($todos, $page * $size, $size);
    return $response;
  }

  private function createResourceCollection() : array {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $data = array_intersect_key($data, $this->fields);
    if (empty($data)) throw new Exception('Fields not specified', 400);

    $title = $data['title'] ?? '';
    if (strlen($title) < 3) throw new Exception('title must be at least 3 characters', 422);
    $fileImage = $_FILES['image'] ??= null;
    $data['image'] = uploadImage($fileImage);

    $response['id'] = $this->model->create($data);
    http_response_code(201);
    return $response;
  }
}