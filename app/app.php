<?php

use Slim\App;

$container = require_once __DIR__.'/bootstrap.php';

$app = new App($container);

if ($container['debug']) {
    require_once __DIR__.'/profiler.php';
}

require_once __DIR__.'/middlewares.php';
require_once __DIR__.'/controllers.php';

return $app;
