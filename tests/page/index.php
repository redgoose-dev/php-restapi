<?php
use redgoose\RestAPI, redgoose\Console;

require '../../vendor/autoload.php';
require '../../src/RestAPI.php';

//header('Content-Type: text/plain');
error_reporting(E_ALL & ~E_NOTICE);


// create instance
try
{
  $restApi = new RestAPI((object)[
//    'url' => 'https://goose.redgoose.me',
    'url' => 'localhost:9001',
    'headers' => [],
    'outputType' => 'json',
    'timeout' => 3,
    'debug' => true,
  ]);
  $res = $restApi->request('get', '', null);
  Console::log($res);
}
catch(\Exception $e)
{
  print_r("PROGRAM ERROR: {$e->getMessage()}, {$e->getCode()}");
}
