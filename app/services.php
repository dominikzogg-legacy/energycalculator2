<?php

use Chubbyphp\Csrf\CsrfProvider;
use Chubbyphp\Deserialize\Deserializer;
use Chubbyphp\Deserialize\Registry\ObjectMappingRegistry as DeserializeObjectMappingRegistry;
use Chubbyphp\ErrorHandler\SimpleErrorHandlerProvider;
use Chubbyphp\Model\StorageCache\ArrayStorageCache;
use Chubbyphp\Model\Resolver;
use Chubbyphp\Security\Authentication\AuthenticationProvider;
use Chubbyphp\Security\Authentication\FormAuthentication;
use Chubbyphp\Security\Authorization\AuthorizationProvider;
use Chubbyphp\Security\Authorization\RoleAuthorization;
use Chubbyphp\Session\SessionProvider;
use Chubbyphp\Translation\LocaleTranslationProvider;
use Chubbyphp\Translation\TranslationProvider;
use Chubbyphp\Translation\TranslationTwigExtension;
use Chubbyphp\Validation\ValidationProvider;
use Energycalculator\Deserialize\ComestibleMapping as DeserializeComestibleMapping;
use Energycalculator\Deserialize\ComestibleWithinDayMapping as DeserializeComestibleWithinDayMapping;
use Energycalculator\Deserialize\DayMapping as DeserializeDayMapping;
use Energycalculator\Deserialize\UserMapping as DeserializeUserMapping;
use Energycalculator\ErrorHandler\HtmlErrorResponseProvider;
use Energycalculator\Provider\TwigProvider;
use Energycalculator\Repository\DayRepository;
use Energycalculator\Repository\ComestibleRepository;
use Energycalculator\Repository\ComestibleWithinDayRepository;
use Energycalculator\Repository\UserRepository;
use Energycalculator\Service\RedirectForPath;
use Energycalculator\Service\TemplateData;
use Energycalculator\Service\TwigRender;
use Energycalculator\Twig\NumericExtension;
use Energycalculator\Twig\RouterExtension;
use Energycalculator\Validation\ComestibleMapping as ValidationComestibleMapping;
use Energycalculator\Validation\ComestibleWithinDayMapping as ValidationComestibleWithinDayMapping;
use Energycalculator\Validation\DayMapping as ValidationDayMapping;
use Energycalculator\Validation\UserMapping as ValidationUserMapping;
use Negotiation\LanguageNegotiator;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Slim\Container;

/* @var Container $container */
$container->register(new AuthenticationProvider());
$container->register(new AuthorizationProvider());
$container->register(new CsrfProvider());
$container->register(new DoctrineServiceProvider());
$container->register(new SimpleErrorHandlerProvider());
$container->register(new MonologServiceProvider());
$container->register(new SessionProvider());
$container->register(new TranslationProvider());
$container->register(new TwigProvider());
$container->register(new ValidationProvider());

// extend providers
$container['errorHandler.defaultProvider'] = function () use ($container) {
    return $container[HtmlErrorResponseProvider::class];
};

$container->extend('security.authentication.authentications', function (array $authentications) use ($container) {
    $authentications[] = $container[FormAuthentication::class];

    return $authentications;
});

$container->extend('security.authorization.authorizations', function (array $authorizations) use ($container) {
    $authorizations[] = $container[RoleAuthorization::class];

    return $authorizations;
});

$container->extend('security.authorization.rolehierarchy', function (array $rolehierarchy) use ($container) {
    $rolehierarchy['ADMIN'] = ['USER'];
    $rolehierarchy['USER'] = ['COMESTIBLE', 'DAY'];
    $rolehierarchy['COMESTIBLE'] = [
        'COMESTIBLE_LIST',
        'COMESTIBLE_VIEW',
        'COMESTIBLE_CREATE',
        'COMESTIBLE_EDIT',
        'COMESTIBLE_DELETE',
    ];
    $rolehierarchy['DAY'] = [
        'DAY_LIST',
        'DAY_VIEW',
        'DAY_CREATE',
        'DAY_EDIT',
        'DAY_DELETE',
    ];

    return $rolehierarchy;
});

$container->extend('translator.providers', function (array $providers) use ($container) {
    $providers[] = new LocaleTranslationProvider('de', require $container['translationDir'].'/de.php');
    $providers[] = new LocaleTranslationProvider('en', require $container['translationDir'].'/en.php');

    return $providers;
});

$container->extend('twig.namespaces', function (array $namespaces) use ($container) {
    $namespaces['Energycalculator'] = $container['viewDir'];

    return $namespaces;
});

$container->extend('twig.extensions', function (array $extensions) use ($container) {
    $extensions[] = new NumericExtension();
    $extensions[] = new RouterExtension($container['router']);
    $extensions[] = new TranslationTwigExtension($container['translator']);
    if ($container['debug']) {
        $extensions[] = new \Twig_Extension_Debug();
    }

    return $extensions;
});


$container->extend('validator.objectmappings', function (array $objectMappings) use ($container) {
    $objectMappings[] = new ValidationComestibleMapping();
    $objectMappings[] = new ValidationComestibleWithinDayMapping();
    $objectMappings[] = new ValidationDayMapping($container[Resolver::class]);
    $objectMappings[] = new ValidationUserMapping($container[Resolver::class]);

    return $objectMappings;
});

// deserializer
$container[Deserializer::class] = function () use ($container) {
    return new Deserializer($container[DeserializeObjectMappingRegistry::class]);
};

$container[DeserializeObjectMappingRegistry::class] = function () use ($container) {
    return new DeserializeObjectMappingRegistry([
        $container[DeserializeComestibleMapping::class],
        $container[DeserializeComestibleWithinDayMapping::class],
        $container[DeserializeDayMapping::class],
        $container[DeserializeUserMapping::class]
    ]);
};

$container[DeserializeComestibleMapping::class] = function () use ($container) {
    return new DeserializeComestibleMapping();
};

$container[DeserializeComestibleWithinDayMapping::class] = function () use ($container) {
    return new DeserializeComestibleWithinDayMapping($container[Resolver::class]);
};

$container[DeserializeDayMapping::class] = function () use ($container) {
    return new DeserializeDayMapping();
};

$container[DeserializeUserMapping::class] = function () use ($container) {
    return new DeserializeUserMapping(
        $container['security.authentication.passwordmanager'],
        $container['security.authorization.rolehierarchyresolver']
    );
};

// repositories
$container[ArrayStorageCache::class] = function () {
    return new ArrayStorageCache();
};

$container[ComestibleRepository::class] = function () use ($container) {
    return new ComestibleRepository(
        $container['db'],
        $container[Resolver::class],
        $container[ArrayStorageCache::class],
        $container['logger']
    );
};

$container[ComestibleWithinDayRepository::class] = function () use ($container) {
    return new ComestibleWithinDayRepository(
        $container['db'],
        $container[Resolver::class],
        $container[ArrayStorageCache::class],
        $container['logger']
    );
};

$container[DayRepository::class] = function () use ($container) {
    return new DayRepository(
        $container['db'],
        $container[Resolver::class],
        $container[ArrayStorageCache::class],
        $container['logger']
    );
};

$container[UserRepository::class] = function () use ($container) {
    return new UserRepository(
        $container['db'],
        $container[Resolver::class],
        $container[ArrayStorageCache::class],
        $container['logger']
    );
};

$container[Resolver::class] = function () use ($container) {
    return new Resolver($container, [
        ComestibleRepository::class,
        ComestibleWithinDayRepository::class,
        DayRepository::class,
        UserRepository::class,
    ]);
};

// services
$container[FormAuthentication::class] = function ($container) {
    return new FormAuthentication(
        $container['security.authentication.passwordmanager'],
        $container['session'],
        $container[UserRepository::class],
        $container['logger']
    );
};

$container[HtmlErrorResponseProvider::class] = function () use ($container) {
    return new HtmlErrorResponseProvider(
        $container['errorHandler'],
        $container[TemplateData::class],
        $container[TwigRender::class]
    );
};

$container[LanguageNegotiator::class] = function () use ($container) {
    return new LanguageNegotiator();
};

$container[RedirectForPath::class] = function () use ($container) {
    return new RedirectForPath($container['router']);
};

$container[RoleAuthorization::class] = function ($container) {
    return new RoleAuthorization($container['security.authorization.rolehierarchyresolver'], $container['logger']);
};

$container[TemplateData::class] = function () use ($container) {
    return new TemplateData(
        $container['security.authentication'],
        $container['debug'],
        $container['session'],
        [
            'comestible_create' => ['comestible_list'],
            'comestible_delete' => ['comestible_list'],
            'comestible_edit' => ['comestible_list'],
            'comestible_list' => [],
            'comestible_view' => ['comestible_list'],
            'day_create' => ['day_list'],
            'day_delete' => ['day_list'],
            'day_edit' => ['day_list'],
            'day_list' => [],
            'day_view' => ['day_list'],
            'user_create' => ['user_list'],
            'user_delete' => ['user_list'],
            'user_edit' => ['user_list'],
            'user_list' => [],
            'user_view' => ['user_list'],
        ],
        $container['translator']
    );
};

$container[TwigRender::class] = function () use ($container) {
    return new TwigRender($container['twig']);
};
