<?php

namespace Crwlr\CrwlExtensionUtils;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ExtensionPackageManager::class, function () {
            return new ExtensionPackageManager();
        });

        $this->app->singleton(RequestTracker::class, function () {
            return new RequestTracker();
        });
    }
}
