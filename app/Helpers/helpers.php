<?php

use App\Models\Channel;
use App\Models\User;
use App\Services\CacheService;
use App\Services\MultiTenancyService;

if (!function_exists('tenancy')) {
    function tenancy(): MultiTenancyService
    {
        return app(MultiTenancyService::class);
    }
}

if (!function_exists('active_tenant')) {
    function activeTenant(): ?Channel
    {
        return app(MultiTenancyService::class)->getActiveTenant();
    }
}

if (!function_exists('contracts_path')) {
    function contracts_path($path = ''): string
    {
        $dir = base_path() == '/var/www/html' ? '/monorepo/contracts' : '../../contracts';
        return $dir . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('cache_service')) {
    function cache_service(): CacheService
    {
        return app(CacheService::class);
    }
}

if (!function_exists('is_api')) {
    function is_api(): bool
    {
        return session('is_api') ?? false;
    }
}

if (!function_exists('user')) {
    function user(): ?User
    {
        return tenancy()->getUser();
    }
}
