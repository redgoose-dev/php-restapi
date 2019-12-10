<?php

$result = null;

$result = (object)[
  'apple' => 'red',
  'banana' => 'yellow',
];

echo json_encode($result, JSON_PRETTY_PRINT);
