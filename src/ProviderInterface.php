<?php

namespace SimpleSlim;

interface ProviderInterface
{
    public function register(): array;
    public function boot(App $app): void;
}
