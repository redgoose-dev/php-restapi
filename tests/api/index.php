<?php
$result = null;

$result = (object)[
  'get' => $_GET,
  'post' => $_POST,
  'files' => $_FILES,
];

echo json_encode($result, JSON_PRETTY_PRINT);
