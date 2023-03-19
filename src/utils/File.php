<?php

class File {

  static public function upload(string $pathToDirectory, array $file) : string {
    if (is_dir($pathToDirectory) === false) mkdir($pathToDirectory, 0777, true);

    $fileName = time() . '_' . basename($file['name']);
    $pathToFile = $pathToDirectory . $fileName;

    if (empty($file['tmp_name']) === false)
      if (move_uploaded_file($file['tmp_name'], $pathToFile) === false)
        throw new Exception('Failed to upload the file', 500);

    if (empty($file['base64']) === false)
      self::upload_from_base64($file['base64'], $pathToFile);

    return $fileName;
  }

  static private function upload_from_base64(string $base64, string $pathToFile) : void {
    $base64 = base64_decode($base64);
    $fileOpen = fopen($pathToFile, 'w+');
    fwrite($fileOpen, $base64);
    fclose($fileOpen);
  }

  static public function download(string $pathToDirectory, string $fileName) : void {
    $pathToFile = $pathToDirectory . $fileName;
    if (file_exists($pathToFile) === false || is_file($pathToFile) === false)
      throw new Exception('Failed to download the file, file is not exists', 500);

      if (ob_get_level()) ob_end_clean();

      header('Content-Type: application/octet-stream');
      header('Content-Transfer-Encoding: binary');
      header('Content-Description: File Transfer');
      header('Content-Disposition: attachment; filename=' . basename($pathToFile));
      header('Content-Length: ' . filesize($pathToFile));

      header('Pragma: no-cache');
      header('Cache-Control: public, max-age=0, must-revalidate');

      readfile($pathToFile);
  }
}