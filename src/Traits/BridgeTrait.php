<?php

namespace SimpleSlim\Traits;

use SimpleSlim\CallableResolver;
use Invoker\CallableResolver as InvokerCallableResolver;
use SimpleSlim\App;
use SimpleSlim\AppFactory;
use SimpleSlim\ControllerInvoker;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\CallableResolverInterface;

trait BridgeTrait
{
    protected function bridge(): App
    {
        $this->container->set(CallableResolverInterface::class, new CallableResolver(new InvokerCallableResolver($this->container)));
        $app = AppFactory::createFromContainer($this->container);
        $app->getRouteCollector()->setDefaultInvocationStrategy($this->createControllerInvoker($this->container));

        return $app;
    }

    private function createControllerInvoker(ContainerInterface $container): ControllerInvoker
    {
        $invoker = new Invoker(new ResolverChain([
            new AssociativeArrayResolver(), new TypeHintContainerResolver($container), new DefaultValueResolver()
        ]), $container);

        return new ControllerInvoker($invoker);
    }
}
