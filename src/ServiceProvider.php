<?php

namespace Crwlr\CrwlExtensionUtils;

use Illuminate\Contracts\Foundation\Application;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ExtensionPackageManager::class, function (Application $app) {
            return new ExtensionPackageManager();
        });
    }
}
