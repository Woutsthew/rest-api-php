<?php

class SQLBuilder {
  static public function conditionsWhere(array $filter) : string {
    $where = null;
    foreach (array_keys($filter) as $key)
      $where[] = $key . " LIKE CONCAT('%', :" . $key . ", '%')";
    return isset($where) ? " WHERE " . implode(" AND ", $where) : '';
  }

  static private function twoDotAdd ($key) { return ":" . $key; }
  public static function fieldsCreate(array $data) : string {
    $keys = array_keys($data);
    $fields = "(" . implode(", ", $keys) . ")";
    $value = "(" . implode(", ", array_map('twoDotAdd', $keys)) . ")";
    return $fields . " VALUES " . $value;
  }

  static public function fieldsUpdate(array $data) : string {
    $set = null;
    foreach (array_keys($data) as $key)
      $set[] = $key . " = :" . $key;
    return implode(", ", $set);
  }
}