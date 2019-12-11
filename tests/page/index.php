<?php
use redgoose\RestAPI, redgoose\Console;

require '../../vendor/autoload.php';
require '../../src/RestAPI.php';

//header('Content-Type: text/plain');
error_reporting(E_ALL & ~E_NOTICE);

// create instance
try
{
  $res2 = RestAPI::request('get', 'http://localhost:9001', null, (object)[
    'outputType' => 'json',
  ]);
  Console::log($res2);

  // test localhost:9001
//  $restApi = new RestAPI((object)[
//    'url' => 'localhost:9001',
//    'headers' => ['foo: bar'],
//    'outputType' => 'json',
//    'timeout' => 3,
//    'debug' => true,
//  ]);
//  $res = $restApi->call('delete', '', (object)[
//    'fooo' => 'barrr',
//    'fooo222' => 'barrr222222',
//  ]);
//  Console::log($res);

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
