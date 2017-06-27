<?php

use Slim\App;

$container = require_once __DIR__.'/bootstrap.php';

$app = new App($container);

$container->extend('twig', function (\Twig_Environment $twig) use ($container) {
    return new DebugBar\Bridge\Twig\TraceableTwigEnvironment($twig);
});

$debugStack = new Doctrine\DBAL\Logging\DebugStack();
$container['db']->getConfiguration()->setSQLLogger($debugStack);

$collectors = [
    new DebugBar\DataCollector\MemoryCollector(),
    new Energycalculator\PhpDebugBar\TimeDataCollector(),
    new DebugBar\Bridge\DoctrineCollector($debugStack),
    new DebugBar\Bridge\Twig\TwigCollector($container['twig']),
    new DebugBar\Bridge\MonologCollector($container['logger']),
];

$debugbar = new \DebugBar\DebugBar();
$debugbar->setStorage(new DebugBar\Storage\FileStorage('/tmp/phpdebugbar_storage'));

$app->add(new \Energycalculator\PhpDebugBar\Psr7PhpDebugBarMiddleware($debugbar, $collectors));

require_once __DIR__.'/middlewares.php';
require_once __DIR__.'/controllers.php';

$app->get('/phpdebugbar-storage', function () use ($debugbar) {
    $openHandler = new DebugBar\OpenHandler($debugbar);
    $openHandler->handle();
})->setName('phpdebugbar_storage');

return $app;
