<?php

namespace SimpleSlim;

use DI\Container;

abstract class Facade implements FacadeInterface
{
    protected static Container $container;

    public static function setFacadeApp(Container $container)
    {
        self::$container = $container;
    }

    public static function __callStatic($method, $args): mixed
    {
        return self::getFacadeInstance()->$method(...$args);
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    protected static function getFacadeInstance(): mixed
    {
        return self::$container->get(static::getFacadeAccessor());
    }

    public static function getFacadeAccessor(): string
    {
        return '';
    }
}
