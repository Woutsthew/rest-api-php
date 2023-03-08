<?php

function conditionsWhere(array $filter) : string {
  $where = null;
  foreach (array_keys($filter) as $key)
    $where[] = $key . " LIKE CONCAT('%', :" . $key . ", '%')";
  return isset($where) ? " WHERE " . implode(" AND ", $where) : '';
}

function twoDotAdd ($key) { return ":" . $key; }
function fieldsCreate(array $data) : string {
  $keys = array_keys($data);
  $fields = "(" . implode(", ", $keys) . ")";
  $value = "(" . implode(", ", array_map('twoDotAdd', $keys)) . ")";
  return $fields . " VALUES " . $value;
}

function fieldsUpdate(array $data) : string {
  $set = null;
  foreach (array_keys($data) as $key)
    $set[] = $key . " = :" . $key;
  return implode(", ", $set);
}

function uploadImage(?array $fileImage) : string {
  if ($fileImage === null) return 'default.png';

  $path = './uploads/images/';
  if (is_dir($path) === false) mkdir($path, 0777, true);
  $image = time() . '-' . $fileImage['name'];
  if (move_uploaded_file($fileImage['tmp_name'], $path . $image) === false)
    throw new Exception('Failed to download the file', 500);

  return $image;
}