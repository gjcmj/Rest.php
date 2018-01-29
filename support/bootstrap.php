<?php
define('BASE_PATH', __DIR__);

// Autoload
require BASE_PATH . '/../vendor/autoload.php';

$app = new \Rest\Rest(BASE_PATH . '/../app/Config');
$app->initialize();

return $app;