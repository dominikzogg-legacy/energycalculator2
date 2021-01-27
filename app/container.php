<?php

declare(strict_types=1);

namespace Energycalculator;

use Chubbyphp\Config\ConfigMapping;
use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\Pimple\ConfigServiceProvider;
use Chubbyphp\Config\Slim\SlimSettingsServiceProvider;
use Chubbyphp\Csrf\CsrfProvider;
use Chubbyphp\Deserialization\Provider\DeserializationProvider;
use Chubbyphp\DoctrineDbServiceProvider\ServiceProvider\DoctrineDbalServiceProvider;
use Chubbyphp\DoctrineDbServiceProvider\ServiceProvider\DoctrineOrmServiceProvider;
use Chubbyphp\Negotiation\Provider\NegotiationProvider;
use Chubbyphp\Security\Authentication\AuthenticationProvider;
use Chubbyphp\Security\Authorization\AuthorizationProvider;
use Chubbyphp\Session\SessionProvider;
use Chubbyphp\Translation\TranslationProvider;
use Chubbyphp\Validation\Provider\ValidationProvider;
use Energycalculator\Config\DevConfig;
use Energycalculator\Config\PhpunitConfig;
use Energycalculator\Config\ProdConfig;
use Energycalculator\ServiceProvider\DeserializationServiceProvider;
use Energycalculator\ServiceProvider\DoctrineServiceProvider;
use Energycalculator\ServiceProvider\FactoryServiceProvider;
use Energycalculator\ServiceProvider\MonologServiceProvider;
use Energycalculator\ServiceProvider\NegotiationServiceProvider;
use Energycalculator\ServiceProvider\ProxyManagerServiceProvider;
use Energycalculator\ServiceProvider\RepositoryServiceProvider;
use Energycalculator\ServiceProvider\SecurityServiceProvider;
use Energycalculator\ServiceProvider\TwigServiceProvider;
use Energycalculator\ServiceProvider\ValidationServiceProvider;
use Slim\Container;
use Energycalculator\ServiceProvider\GenericServiceProvider;

$configProvider = new ConfigProvider(realpath(__DIR__.'/..'), [
    new ConfigMapping('dev', DevConfig::class),
    new ConfigMapping('phpunit', PhpunitConfig::class),
    new ConfigMapping('prod', ProdConfig::class),
]);

$container = new Container(['env' => $env ?? 'dev']);

$container['rootDir'] = realpath(__DIR__.'/../');

$container->register(new AuthenticationProvider());
$container->register(new AuthorizationProvider());
$container->register(new CsrfProvider());
$container->register(new DeserializationProvider());
$container->register(new DoctrineDbalServiceProvider());
$container->register(new DoctrineOrmServiceProvider());
$container->register(new NegotiationProvider());
$container->register(new SessionProvider());
$container->register(new TranslationProvider());
$container->register(new ValidationProvider());

$container->register(new DeserializationServiceProvider());
$container->register(new DoctrineServiceProvider());
$container->register(new FactoryServiceProvider());
$container->register(new GenericServiceProvider());
$container->register(new MonologServiceProvider());
$container->register(new NegotiationServiceProvider());
$container->register(new ProxyManagerServiceProvider());
$container->register(new RepositoryServiceProvider());
$container->register(new SecurityServiceProvider());
$container->register(new TwigServiceProvider());
$container->register(new ValidationServiceProvider());

$container->register(new ConfigServiceProvider($configProvider));
$container->register(new SlimSettingsServiceProvider($configProvider));

return $container;
