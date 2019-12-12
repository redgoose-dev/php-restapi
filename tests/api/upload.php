<?php
/**
 * 파일 업로드 스크립트
 * `$_FILES` 값을 통하여 파일들을 업로드하고 그 주소를 출력합니다.
 */

$result = (object)[
  'post' => $_POST,
  'files' => (object)[],
];
$path = './files/';
$url = 'http://localhost:9001/files/';

foreach ($_FILES as $key=>$file)
{
  if (is_string($file['tmp_name']))
  {
    if (move_uploaded_file($file['tmp_name'], $path.$file['name']))
    {
      $result->files->{$key} = $url.$file['name'];
    }
  }
  else if (is_array($file['tmp_name']))
  {
    $result->files->{$key} = [];
    for ($i=0; $i<count($file['tmp_name']); $i++)
    {
      if (move_uploaded_file($file['tmp_name'][$i], $path.$file['name'][$i]))
      {
        $result->files->{$key}[] = $url.$file['name'][$i];
      }
    }
  }
}

echo json_encode($result, JSON_PRETTY_PRINT);
