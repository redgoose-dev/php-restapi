<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>RestAPI test area</title>
</head>
<body>
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
      'url' => 'localhost:9001',
      'outputType' => 'json',
      'timeout' => 30,
      'debug' => true,
    ]);
    $res = $restapi->call('post', '/upload.php', $_POST, $_FILES);

    if (isset($res->response->files['upload']))
    {
      echo "<img src='{$res->response->files['upload']}'/>";
    }
    if (isset($res->response->files['uploads']))
    {
      foreach ($res->response->files['uploads'] as $key=>$item)
      {
        echo "<p><img src='{$item}' alt=''></p>";
      }
    }
  }
  catch (Exception $e)
  {
    var_dump('ERROR: '.$e);
  }
  exit;
}
?>

<form action="/upload.php?method=post" method="post" enctype="multipart/form-data">
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
