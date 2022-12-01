<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests;

use ApiAutoPilot\ApiAutoPilot\ApiAutoPilotServiceProvider;
use ApiAutoPilot\ApiAutoPilot\Facades\ApiAutoPilot;
use ApiAutoPilot\ApiAutoPilot\Http\Controllers\ApiAutoPilotController;
use ApiAutoPilot\ApiAutoPilot\Http\Middleware\ModelSearch;
use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\seeders\AutoPilotSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AutoPilotSeeder::class);
        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'ApiAutoPilot\\ApiAutoPilot\\Tests\\Fixtures\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    public function refreshDatabase()
    {
        $this->artisan('migrate', ['--path' => __DIR__ . '/Fixtures/database/migrations', '--realpath' => true]);
        $this->app[Kernel::class]->setArtisan(null);

    }

    protected function getPackageProviders($app)
    {
        return [
            ApiAutoPilotServiceProvider::class,
        ];
    }

    protected function defineRoutes($router)
    {
        $router->middleware('modelSearch')->prefix('/api/aap')->group(function () use ($router) {
            $router->post('/update/{modelName}/{id}', [ApiAutoPilotController::class, 'edit'])
                ->name('update');
            $router->get('/{modelName}', [ApiAutoPilotController::class, 'index'])
                ->name('index');
            $router->get('/{modelName}/{id}', [ApiAutoPilotController::class, 'get'])
                ->name('show');
            $router->post('/{modelName}/{related?}', [ApiAutoPilotController::class, 'create'])
                ->name('create');
            $router->delete('{modelName}/{id}', [ApiAutoPilotController::class, 'delete'])
                ->name('delete');
            $router->get('/search/query/{modelName}', [ApiAutoPilotController::class, 'search'])
                ->name('search');
            $router->get('{modelName}/{id}/{relation}', [ApiAutoPilotController::class, 'getWithRelation'])
                ->name('show.relationship');
            $router->post('/{modelName}/{id}/{second}/attach', [ApiAutoPilotController::class, 'attach'])
                ->name('attach');
            $router->delete('/{modelName}/{id}/{second}/detach', [ApiAutoPilotController::class, 'detach'])
                ->name('detach');
            $router->patch('/{modelName}/{id}/{second}/sync', [ApiAutoPilotController::class, 'sync'])
                ->name('sync');
        });
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        Artisan::call('cache:clear');
        $migration = include __DIR__ . '/Fixtures/Database/Migrations/create_users_table.php';
        $migration->up();
        $migration = include __DIR__ . '/Fixtures/Database/Migrations/create_posts_table.php';
        $migration->up();
        $migration = include __DIR__ . '/Fixtures/Database/Migrations/create_phones_table.php';
        $migration->up();
        $migration = include __DIR__ . '/Fixtures/Database/Migrations/create_tags_table.php';
        $migration->up();
        $migration = include __DIR__ . '/Fixtures/Database/Migrations/create_post_tag_table.php';
        $migration->up();
    }
}
