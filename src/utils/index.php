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

function uploadFile(string $pathToSave, array $file) : string {
  if (is_dir($pathToSave) === false) mkdir($pathToSave, 0777, true);

  $imageName = time() . '_' . $file['name'];
  if (move_uploaded_file($file['tmp_name'], $pathToSave . $imageName) === false)
    throw new Exception('Failed to upload the file', 500);

  return $imageName;
}

function downloadFile(string $pathToDirectory, string $fileName) : void {
  $pathToFile = $pathToDirectory . $fileName;
  die($pathToFile);
  if (file_exists($pathToFile) === false)
    throw new Exception('Failed to download the file, file is not exists', 500);

  if (ob_get_level()) ob_end_clean();

  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Transfer-Encoding: binary');
  header('Content-Disposition: attachment; filename=' . basename($pathToFile));
  header('Content-Length: ' . filesize($pathToFile));
  header('Expires: 0');
  header('Pragma: public');
  header('Cache-Control: must-revalidate');

  readfile($pathToFile);
}