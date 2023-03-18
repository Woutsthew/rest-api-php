<?php
require_once('./src/models/todos.php');

class TodosController {
  private static function Model() : TodosModel { return new TodosModel("localhost", "todos", "todos", "todos"); }

  private static array $fields = ['title' => '', 'image' => [], 'body' => '', 'isDelete' => ''];
  private static string $UPLOADS_IMAGES = './uploads/images/';


  public static function read() {
    $page = (int) $_GET['page'] ??= 0;
    $size = (int) $_GET['size'] ??= 20;
    $filter = array_intersect_key($_GET, self::$fields);

    $todos = self::Model()->read($filter);
    $response['page'] = $page;
    $response['size'] = $size;
    $response['count'] = count($todos);
    $response['todos'] = array_slice($todos, $page * $size, $size);
    die(json_encode($response));
  }

  public static function readById(string $id) {
    $response = self::Model()->readById($id);
    die(json_encode($response));
  }

  public static function readImageById(string $id) {
    $resource = self::Model()->readById($id);

    File::download(self::$UPLOADS_IMAGES, $resource['image']);

    exit;
  }

  public static function create() {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $data = array_intersect_key($data, self::$fields);
    if (empty($data)) throw new Exception('Fields not specified', 400);

    $title = $data['title'] ?? '';
    if (strlen($title) < 3)
      throw new Exception('title must be at least 3 characters', 422);

    $fileImage = $_FILES['image'] ??= $data['image'] ??= null;
    $data['image'] = $fileImage === null ? 'default.png'
      : File::upload(self::$UPLOADS_IMAGES, $fileImage);

    $response['id'] = self::Model()->create($data);
    http_response_code(201);
    die(json_encode($response));
  }

  public static function updateImageById(string $id) {
    $fileImage = $_FILES['image'] ??= null;
    if ($fileImage === null) throw new Exception('Image not specified', 400);
    $data['image'] = File::upload(self::$UPLOADS_IMAGES, $fileImage);
    $response['id'] = self::Model()->update($id, $data);
    die(json_encode($response));
  }

  public static function updateById(string $id) {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $data = array_intersect_key($data, self::$fields);

    if (empty($data)) throw new Exception('Fields not specified', 400);
    if (empty($data['image']) === false) $data['image'] = File::upload(self::$UPLOADS_IMAGES, $data['image']);

    $response['id'] = self::Model()->update($id, $data);
    die(json_encode($response));
  }

  public static function deleteById(string $id) {
    $isStrong = json_decode(file_get_contents('php://input'), true)['isStrong'] ?? false;
    $response['id'] = self::Model()->delete($id, $isStrong);
    die(json_encode($response));
  }
}