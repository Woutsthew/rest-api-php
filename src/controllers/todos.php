<?php

class TodosController {
  private array $fields = ['title' => '', 'image' => [], 'body' => '', 'isDelete' => ''];
  private string $UPLOADS_IMAGES = './uploads/images/';
  public function __construct(private TodosModel $model) {}

  public function request(string $method) : array {
    $resource = explode("/", explode("?", $_SERVER["REQUEST_URI"])[0])[2] ?? null;

    if ($resource) return $this->resourceRequest($method, $resource);
    else return $this->collectionRequest($method);
  }

  private function resourceRequest(string $method, string $id) : array {
    $routes = explode("/", explode("?", $_SERVER["REQUEST_URI"])[0])[3] ?? null;
    switch ($method) {
      case 'GET':
        $resource = $this->model->readById($id);
        if ($routes !== 'image') return $resource;

        File::download($this->UPLOADS_IMAGES, $resource['image']);
        break;
      case 'POST': if ($routes === 'image') return $this->updateImage($id); break;
      case 'PUT': throw new Exception('Not Implemented', 501); break;
      case 'PATCH': return $this->update($id); break;
      case 'DELETE': return $this->delete($id); break;
      default:
        header('Allow: GET, POST, PATCH, DELETE');
        throw new Exception('Method Not Allowed', 405);
    }
  }

  private function update(string $id) : array {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $data = array_intersect_key($data, $this->fields);
    if (empty($data)) throw new Exception('Fields not specified', 400);
    $response['id'] = $this->model->update($id, $data);
    return $response;
  }

  private function updateImage(string $id) : array {
    $fileImage = $_FILES['image'] ??= null;
    if ($fileImage === null) throw new Exception('Image not specified', 400);
    $data['image'] = File::upload($this->UPLOADS_IMAGES, $fileImage);
    $response['id'] = $this->model->update($id, $data);
    return $response;
  }

  private function delete(string $id) : array {
    $isStrong = json_decode(file_get_contents('php://input'), true)['isStrong'] ?? false;
    $response['id'] = $this->model->delete($id, $isStrong);
    return $response;
  }

  private function collectionRequest(string $method) {
    switch ($method) {
      case 'GET': return $this->read(); break;
      case 'POST': return $this->createResourceCollection(); break;
      default:
        header('Allow: GET, POST');
        throw new Exception('Method Not Allowed', 405);
    }
  }

  private function read() : array {
    $page = (int) $_GET['page'] ??= 0;
    $size = (int) $_GET['size'] ??= 20;
    $filter = array_intersect_key($_GET, $this->fields);

    $todos = $this->model->read($filter);
    $response['page'] = $page;
    $response['size'] = $size;
    $response['count'] = count($todos);
    $response['todos'] = array_slice($todos, $page * $size, $size);
    return $response;
  }

  private function create() : array {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $data = array_intersect_key($data, $this->fields);
    if (empty($data)) throw new Exception('Fields not specified', 400);

    $title = $data['title'] ?? '';
    if (strlen($title) < 3)
      throw new Exception('title must be at least 3 characters', 422);

    $fileImage = $_FILES['image'] ??= $data['image'] ??= null;
    $data['image'] = $fileImage === null ? 'default.png'
      : File::upload(self::$UPLOADS_IMAGES, $fileImage);

    $response['id'] = $this->model->create($data);
    http_response_code(201);
    return $response;
  }
}