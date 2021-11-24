<?php

namespace SimpleSlim;

use SimpleSlim\Traits\BridgeTrait;
use SimpleSlim\Traits\ContainerTrait;
use Dotenv\Dotenv;

class Kernel
{
    use ContainerTrait, BridgeTrait;

    protected App $app;
    protected array $providers = [];

    public static function start(string $basePath): App
    {
        return (new static($basePath))->slimApp();
    }

    /** @noinspection PhpIncludeInspection */
    public function __construct(string $basePath)
    {
        Dotenv::createImmutable($basePath . DIRECTORY_SEPARATOR . 'storage')->safeLoad();
        $this->container = $this->containerBuilder();
        Facade::setFacadeApp($this->container);
        $this->bindPathsInContainer(rtrim($basePath, '\/'));
        $this->app = $this->bridge();
        $this->providersCollection->each(fn(ProviderInterface $provider) => $provider->boot($this->app));
        (require $this->container->get('path.routes') . 'web.php')($this->app);
    }

    protected function slimApp(): App
    {
        return $this->app;
    }

    protected function bindPathsInContainer(string $basePath): void
    {
        $this->container->set('path.base', $basePath);
        $this->container->set('path.config', $basePath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
        $this->container->set('path.database', $basePath . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR);
        $this->container->set('path.public', $basePath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
        $this->container->set('path.resources', $basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR);
        $this->container->set('path.routes', $basePath . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR);
        $this->container->set('path.storage', $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR);
    }
}
