<?php

function conditionsWhere(array $filter) : string {
  $where = null;
  foreach (array_keys($filter) as $key)
    $where[] = $key . " LIKE CONCAT('%', :" . $key . ", '%')";
  return isset($where) ? " WHERE " . implode(" AND ", $where) : '';
}

function fieldsUpdate(array $fields) : string {
  $set = null;
  foreach (array_keys($fields) as $key)
    $set[] = $key . " = :" . $key;
  return implode(", ", $set);
}