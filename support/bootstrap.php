<?php
define('BASE_PATH', __DIR__);
define('CONFIG_PATH', BASE_PATH . '/../app/Config');

// Autoload
require BASE_PATH . '/../vendor/autoload.php';

$app = new \Rest\Rest(CONFIG_PATH . '/app.php');
$app->initialize();

return $app;
