<?php

declare(strict_types=1);

namespace Energycalculator\Config;

class ProdConfig extends AbstractConfig
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        $cacheDir = $this->getCacheDir();

        return [
            'config.cleanDirectories' => $this->getDirectories(),
            'doctrine.dbal.db.options' => [
                'configuration' => [
                    'cache.result' => ['type' => 'apcu'],
                ],
                'connection' => [
                    'charset' => 'utf8mb4',
                    'dbname' => 'energycalculator',
                    'driver' => 'pdo_mysql',
                    'host' => 'localhost',
                    'password' => 'root',
                    'user' => 'root',
                ],
            ],
            'doctrine.orm.em.options' => [
                'cache.hydration' => ['type' => 'apcu'],
                'cache.metadata' => ['type' => 'apcu'],
                'cache.query' => ['type' => 'apcu'],
                'proxies.dir' => $cacheDir.'/doctrine/proxies',
            ],
            'cacheDir' => $cacheDir,
            'debug' => false,
            'localeFallback' => 'en',
            'locales' => ['de', 'en'],
            'monolog.name' => 'energycalculator',
            'monolog.level' => 'notice',
            'monolog.path' => $this->getLogDir().'/application-'.(new \DateTime())->format('Y-m-d').'.log',
            'session.expirationTime' => 1200,
            'session.privateRsaKey' => '6t332+EAscTgRQstgHjUOYvTeTbhk7CaW9AptDT9Fhw=', // https://github.com/AndrewCarterUK/CryptoKey
            'session.publicRsaKey' => '6t332+EAscTgRQstgHjUOYvTeTbhk7CaW9AptDT9Fhw=',
            'session.setCookieSecureOnly' => false,
        ];
    }

    /**
     * @return array
     */
    public function getSlimSettings(): array
    {
        return [
            'displayErrorDetails' => false,
            'routerCacheFile' => $this->getCacheDir().'/routes.php',
        ];
    }

    /**
     * @return string
     */
    protected function getEnv(): string
    {
        return 'prod';
    }
}
