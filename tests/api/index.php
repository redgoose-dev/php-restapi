<?php

$result = null;

$result = (object)[
  'apple' => 'red',
  'banana' => 'yellow',
  'get' => $_GET,
  'post' => $_POST,
];

echo json_encode($result, JSON_PRETTY_PRINT);
