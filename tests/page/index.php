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
  $res = $restApi->call('post', '', (object)[
    'fooo' => 'barrr',
    'fooo222' => 'barrr222222',
  ]);
  Console::log($res);

//  $res2 = RestAPI::request('get', 'http://localhost:9001', null, (object)[
//    'outputType' => 'json',
//  ]);
//  Console::log($res2);

//  $res3 = RestAPI::request(
//    'get',
//    'http://localhost:9001',
//    (object)[
//      'foo' => 'bar',
//      'foo22' => 'bar22',
//    ],
//    (object)[
//      'outputType' => 'json',
//      'debug' => true,
//    ]
//  );
//  Console::log($res3);
}
catch(\Exception $e)
{
  print_r("PROGRAM ERROR: {$e->getMessage()}, {$e->getCode()}");
}
