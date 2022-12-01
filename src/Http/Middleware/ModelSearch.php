<?php

namespace ApiAutoPilot\ApiAutoPilot\Http\Middleware;

use ApiAutoPilot\ApiAutoPilot\Traits\HasModelRelationships;
use ApiAutoPilot\ApiAutoPilot\Traits\HasResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class ModelSearch
{
    use HasModelRelationships;
    use HasResponse;

    /**
     * @throws \ReflectionException
     */
    public function handle(Request $request, Closure $next)
    {
        $namespace = 'App\\Models\\'.ucfirst($request->route('modelName'));
        if (App::runningUnitTests()) {
            $namespace = "ApiAutoPilot\\ApiAutoPilot\\Tests\\Fixtures\Models\\".ucfirst($request->route('modelName'));
        }

        if (! class_exists($namespace)) {
            return $this->notFoundResponse();
        }
        if ($this->endpointIsExcluded($namespace)) {
            return $this->endpointNotEnabledResponse();
        }

        $modelClass = new ($namespace);

        $relations = $this->getRelationships($modelClass);
        foreach ($relations as $relation) {
            $relation['return_type'] = $this->getRelationshipsReturnType(app($namespace), $relation['name']);
        }

        $request->attributes->add(['relationships' => $relations]);
        $request->attributes->add(['modelClass' => app($namespace)]);
        $request->attributes->add(['isAutoPilotRequest' => true]);

        return $next($request);
    }

    /**
     * @throws \ReflectionException
     */
    protected function getRelationshipsReturnType($class, $method): string|null
    {
        $reflection = new \ReflectionMethod($class, $method);

        return $reflection->getReturnType();
    }

    protected function endpointIsExcluded($namespace): bool
    {
        $routeConfigIndex = 'apiautopilot.'.Route::currentRouteName().'.exclude';
        $routeSettings = config($routeConfigIndex);

        return in_array($namespace, $routeSettings ?? []);
    }
}
