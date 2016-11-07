<?php

/** @var Pimple\Container $container */

return [
    'settings' => [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => $container['cacheDir'].'/routes.php',
    ],
    'projectSettings' => [
        'db.options' => [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'energycalculator2',
            'user' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ],
        'debug' => false,
        'localeFallback' => 'en',
        'locales' => ['de', 'en'],
        'monolog.logfile' => $container['logDir'].'/application-'.(new \DateTime())->format('Y-m-d').'.log',
        'monolog.level' => 'notice',
        'security.authorization.rolehierarchy' => [
            'ADMIN' => ['USER'],
            'USER' => ['COMESTIBLE_LIST', 'COMESTIBLE_VIEW', 'COMESTIBLE_CREATE', 'COMESTIBLE_EDIT'],
        ],
        'session.expirationTime' => 1200,
        'session.privateRsaKey' => '6t332+EAscTgRQstgHjUOYvTeTbhk7CaW9AptDT9Fhw=', // https://github.com/AndrewCarterUK/CryptoKey
        'session.publicRsaKey' => '6t332+EAscTgRQstgHjUOYvTeTbhk7CaW9AptDT9Fhw=',
        'session.setCookieSecureOnly' => false,
    ],
];
