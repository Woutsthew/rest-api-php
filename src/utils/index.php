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