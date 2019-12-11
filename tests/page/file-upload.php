<?php
use redgoose\RestAPI, redgoose\Console;

set_time_limit(300);

require '../../vendor/autoload.php';
require '../../src/RestAPI.php';

error_reporting(E_ALL & ~E_NOTICE);

// test post
if (isset($_GET['method']) && $_GET['method'] === 'post')
{
  try
  {
    header('Content-Type: text/plain');
    $restapi = new RestAPI((object)[
//      'url' => 'localhost:9001',
      'url' => 'https://lab.redgoose.me',
      'outputType' => 'text',
      'timeout' => 10,
      'debug' => false,
    ]);
    $res = $restapi->call('post', '/php-test/upload.php', $_POST, $_FILES);
    // TODO: 파일 업로드 하다가 타임아웃이 자꾸 걸려서 삽질하고 있었음.
    // TODO: set_time_limit() 함수를 사용하여 좀더 타임아웃이 안걸리도록 조치한듯함.
    // TODO: 하지만 아직 불안정함
    // TODO: 파일 한개 업로드로 테스트 하고 있지만 단일파일 업로드 끝나면 복수파일 업로드 작업해야함
    echo $res->response;
//    if (isset($res->response->files['upload']))
//    {
//      echo "<img src='{$res->response->files['upload']}'/>";
//    }
//    else
//    {
//      echo 'empty';
//    }
  }
  catch (Exception $e)
  {
    var_dump('ERROR: '.$e);
  }
  exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>RestAPI test area</title>
</head>
<body>
<form action="/file-upload.php?method=post" method="post" enctype="multipart/form-data">
  <fieldset>
    <legend>basic field</legend>
    <ul>
      <li>name: <input type="text" name="nameee" value="name-value"></li>
      <li>file: <input type="file" name="upload"></li>
      <li>files: <input type="file" name="uploads[]" multiple></li>
    </ul>
  </fieldset>
  <nav>
    <button type="submit">submit</button>
  </nav>
</form>
</body>
</html>
