<?php

namespace SimpleSlim;

use Invoker\InvokerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\InvocationStrategyInterface;

class ControllerInvoker implements InvocationStrategyInterface
{
    private InvokerInterface $invoker;

    public function __construct(InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function __invoke(callable $callable, Request $request, Response $response, array $routeArguments): Response
    {
        $parameters = ['request' => $request, 'response' => $response];
        $parameters += $routeArguments;
        $parameters += $request->getAttributes();

        return $this->invoker->call($callable, $parameters);
    }
}
