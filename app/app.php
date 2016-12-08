<?php

use Slim\App;

$container = require_once __DIR__.'/bootstrap.php';

$app = new App($container);

require_once __DIR__.'/middlewares.php';
require_once __DIR__.'/controllers.php';


$debugbar = new \DebugBar\DebugBar();
$debugbar->addCollector(new \DebugBar\DataCollector\PhpInfoCollector());
$debugbar->addCollector(new \DebugBar\DataCollector\MemoryCollector());




$debugStack = new Doctrine\DBAL\Logging\DebugStack();
$container['db']->getConfiguration()->setSQLLogger($debugStack);
$debugbar->addCollector(new DebugBar\Bridge\DoctrineCollector($debugStack));

$debugbar->addCollector(new DebugBar\Bridge\MonologCollector($container['logger']));

$container->extend('twig', function (\Twig_Environment $twig) use ($container) {
    return new DebugBar\Bridge\Twig\TraceableTwigEnvironment($twig);
});


$debugbar->addCollector(new DebugBar\Bridge\Twig\TwigCollector($container['twig']));

$debugbar->setStorage(new DebugBar\Storage\FileStorage('/tmp/phpdebugbar_storage'));

$app->add(new \Energycalculator\PhpDebugBar\Psr7PhpDebugBarMiddleware($debugbar));


$app->get('/phpdebugbar-storage', function () use ($debugbar) {
    $openHandler = new DebugBar\OpenHandler($debugbar);
    $openHandler->handle();
})->setName('phpdebugbar_storage');


return $app;
