#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Energycalculator;

use Energycalculator\ServiceProvider\ConsoleServiceProvider;
use Slim\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

require __DIR__.'/bootstrap.php';

$input = new ArgvInput();

$env = $input->getParameterOption(['--env', '-e'], 'dev');

/** @var Container $container */
$container = require __DIR__.'/../app/container.php';
$container->register(new ConsoleServiceProvider());

$console = new Application();
$console->getDefinition()->addOption(
    new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev')
);
$console->addCommands($container['console.commands']);
$console->run($input);
