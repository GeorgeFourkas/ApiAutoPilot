<?php

namespace ApiAutoPilot\ApiAutoPilot;

use ApiAutoPilot\ApiAutoPilot\Commands\InstallationCommand;
use ApiAutoPilot\ApiAutoPilot\Http\Middleware\ModelSearch;
use ApiAutoPilot\ApiAutoPilot\Http\Middleware\SetApplicationJsonHeader;
use Illuminate\Routing\Router;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ApiAutoPilotServiceProvider extends PackageServiceProvider
{
    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('apiautopilot')
            ->hasConfigFile()
            ->hasCommands([
                InstallationCommand::class
            ]);

        $this
            ->configureMiddleware()
            ->configureFacades();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function configureMiddleware(): static
    {
        $this->app->make(Router::class)->aliasMiddleware('ModelSearch', ModelSearch::class);
        $this->app->make(Router::class)->aliasMiddleware('enforceJson', SetApplicationJsonHeader::class);
        return $this;
    }

    protected function configureFacades(): void
    {
        $this->app->bind('ApiAutoPilot', function ($app) {
            return new ApiAutoPilot();
        });
    }
}
