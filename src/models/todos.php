<?php

class TodosModel {
  private PDO $db;

  public function __construct(string $host, string $name, string $user, string $password) {
    //$dsn = 'sqlsrv:Server='.$host.', 1433; Database='.$name;
    $dsn = 'mysql:host='.$host.'; port=3306; dbname='.$name.';charset=utf8';
    $this->db = new PDO($dsn, $user, $password);
  }

  public function get(array $filter) : array {
    $query = "SELECT id, title, image, body FROM todos" . conditionsWhere($filter);
    $stmt = $this->db->prepare($query);
    if ($stmt->execute($filter) === false)
      throw new Exception($stmt->errorInfo()[2], $stmt->errorInfo()[1]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getById(string $id) : array | object {
    $query = "SELECT id, title, image, body FROM todos WHERE id = :id";
    $stmt = $this->db->prepare($query);
    if ($stmt->execute(['id' => $id]) === false)
      throw new Exception($stmt->errorInfo()[2], $stmt->errorInfo()[1]);

    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    return $item === false ? (object)[] : $item;
  }

  public function create(array $data) : string {
    $query = "INSERT INTO todos " . fieldsCreate($data);
    $stmt = $this->db->prepare($query);
    if ($stmt->execute($data) === false)
      throw new Exception($stmt->errorInfo()[2], $stmt->errorInfo()[1]);
  
    return $this->db->lastInsertId();
  }

  public function update(string $id, array $data) : string {
    $query = "UPDATE todos SET " . fieldsUpdate($data) . " WHERE id = :id";
    $stmt = $this->db->prepare($query);
    $data['id'] = $id;
    if ($stmt->execute($data) === false)
      throw new Exception($stmt->errorInfo()[2], $stmt->errorInfo()[1]);

    return $id;
  }

  public function delete(string $id, bool $isStrong) : string {
    $query = $isStrong ? "DELETE FROM todos WHERE id = :id"
      : "UPDATE todos SET isDelete = true WHERE id = :id";
    $stmt = $this->db->prepare($query);
    if ($stmt->execute(['id' => $id]) === false)
      throw new Exception($stmt->errorInfo()[2], $stmt->errorInfo()[1]);

    return $id;
  }
}