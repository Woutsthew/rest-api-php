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

function uploadImage(string $pathToSave, ?array $fileImage) : string {
  if ($fileImage === null) return 'default.png';

  if (is_dir($pathToSave) === false) mkdir($pathToSave, 0777, true);
  $image = time() . '-' . $fileImage['name'];
  if (move_uploaded_file($fileImage['tmp_name'], $pathToSave . $image) === false)
    throw new Exception('Failed to upload the file', 500);

  return $image;
}

function downloadFile(string $fileName) {
  if (file_exists($fileName) === false)
    throw new Exception('Failed to download the file, file is not exists', 500);

  if (ob_get_level()) ob_end_clean();

  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Transfer-Encoding: binary');
  header('Content-Disposition: attachment; filename=' . basename($fileName));
  header('Content-Length: ' . filesize($fileName));
  header('Expires: 0');
  header('Pragma: public');
  header('Cache-Control: must-revalidate');

  readfile($fileName);
}