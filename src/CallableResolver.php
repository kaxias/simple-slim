<?php

namespace SimpleSlim;

use Invoker\Exception\NotCallableException;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\AdvancedCallableResolverInterface;

class CallableResolver implements AdvancedCallableResolverInterface
{
    private \Invoker\CallableResolver $callableResolver;

    public function __construct(\Invoker\CallableResolver $callableResolver)
    {
        $this->callableResolver = $callableResolver;
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function resolve($toResolve): callable
    {
        return $this->callableResolver->resolve($this->translateNotation($toResolve));
    }

    public function resolveRoute($toResolve): callable
    {
        return $this->resolvePossibleSignature($toResolve, 'handle', RequestHandlerInterface::class);
    }

    public function resolveMiddleware($toResolve): callable
    {
        return $this->resolvePossibleSignature($toResolve, 'process', MiddlewareInterface::class);
    }

    private function translateNotation(mixed $toResolve)
    {
        if (is_string($toResolve) && preg_match(\Slim\CallableResolver::$callablePattern, $toResolve)) {
            $toResolve = str_replace(':', '::', $toResolve);
        }

        return $toResolve;
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    private function resolvePossibleSignature(mixed $toResolve, string $method, string $typeName): callable
    {
        if (is_string($toResolve)) {
            $toResolve = $this->translateNotation($toResolve);

            try {
                $callable = $this->callableResolver->resolve([$toResolve, $method]);

                if (is_array($callable) && $callable[0] instanceof $typeName) {
                    return $callable;
                }
            } catch (NotCallableException $e) {}
        }

        return $this->callableResolver->resolve($toResolve);
    }
}
