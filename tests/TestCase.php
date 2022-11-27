<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests;

use ApiAutoPilot\ApiAutoPilot\ApiAutoPilotServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'ApiAutoPilot\\ApiAutoPilot\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ApiAutoPilotServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_apiautopilot_table.php.stub';
        $migration->up();
        */
    }
}
