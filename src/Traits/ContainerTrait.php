<?php

namespace SimpleSlim\Traits;

use SimpleSlim\ProviderInterface;
use DI\Container;
use DI\ContainerBuilder;
use Illuminate\Support\Collection;

trait ContainerTrait
{
    protected Container $container;
    protected Collection $providersCollection;

    /** @noinspection PhpUnhandledExceptionInspection */
    protected function containerBuilder(): Container
    {
        $containerBuilder = new ContainerBuilder();

        $this->providersCollection = Collection::wrap($this->providers)
            ->map(fn($provider) => new $provider)
            ->each(fn(ProviderInterface $provider) => $containerBuilder->addDefinitions($provider->register()));

        return $containerBuilder->build();
    }
}
