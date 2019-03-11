<?php

declare(strict_types=1);

namespace Energycalculator\Config;

use Chubbyphp\Config\ConfigInterface;
use Chubbyphp\Config\Slim\SlimSettingsInterface;

abstract class AbstractConfig implements ConfigInterface, SlimSettingsInterface
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @param string $rootDir
     *
     * @return self
     */
    public static function create(string $rootDir): ConfigInterface
    {
        $config = new static();
        $config->rootDir = $rootDir;

        return $config;
    }

    private function __construct()
    {
    }

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return [
            'cache' => $this->getCacheDir(),
            'log' => $this->getLogDir(),
        ];
    }

    /**
     * @return string
     */
    abstract protected function getEnv(): string;

    /**
     * @return string
     */
    protected function getCacheDir(): string
    {
        return $this->rootDir.'/var/cache/'.$this->getEnv();
    }

    /**
     * @return string
     */
    protected function getLogDir(): string
    {
        return $this->rootDir.'/var/log/'.$this->getEnv();
    }
}
