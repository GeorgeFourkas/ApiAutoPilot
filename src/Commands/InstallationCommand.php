<?php

namespace ApiAutoPilot\ApiAutoPilot\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class InstallationCommand extends Command
{
    protected const ROUTE_MIDDLEWARE_ARRAY = 'protected $routeMiddleware = [';

    protected const ROUTE_CODE = "Route::prefix('/aap')->group(function () {".PHP_EOL.
    "   ApiAutoPilot\ApiAutoPilot\Facades\ApiAutoPilot::routes();".PHP_EOL.
    '});'.PHP_EOL;

    public $signature = 'apiautopilot:install';

    public $description = 'this command installs the package.';

    public function handle(): int
    {
        $this->installMiddlewareToKernel()
            ->appendRoutesToApiFile()
            ->publishConfig();

        return self::SUCCESS;
    }

    protected function installMiddlewareToKernel(): static
    {
        $kernelContent = file_get_contents(app_path('Http/Kernel.php'));
        if (! Str::contains($kernelContent, "'modelSearch' =>")) {
            $this->replaceInFile(self::ROUTE_MIDDLEWARE_ARRAY,
                self::ROUTE_MIDDLEWARE_ARRAY.PHP_EOL.
                '        //The ModelSearch Middleware is Part of the ApiAutoPilot Package. Deleting it from the array will result in non-working endpoints'.PHP_EOL.
                "        'modelSearch' =>  \ApiAutoPilot\ApiAutoPilot\Http\Middleware\ModelSearch::class,", app_path('Http/Kernel.php'));
        }

        return $this;
    }

    protected function appendRoutesToApiFile(): static
    {
        $routePath = base_path('routes/api.php');
        $apiRoutesFile = file_get_contents($routePath);
        if (! Str::contains($apiRoutesFile, self::ROUTE_CODE)) {
            (new Filesystem())->append($routePath, self::ROUTE_CODE);
        }

        return $this;
    }

    protected function publishConfig(): static
    {
        $this->callSilent('vendor:publish', ['--tag' => 'autopilot-api-config', '--force' => true]);

        return $this;
    }

    protected function replaceInFile(string $search, string $replace, string $path): void
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}
