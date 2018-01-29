<?php
define('BASE_PATH', __DIR__);
define('CONFIG_PATH', BASE_PATH . '/../config');

// Autoload
require BASE_PATH . '/../vendor/autoload.php';

$app = new \Rest\Rest(CONFIG_PATH);
$app->initialize();

return $app;
