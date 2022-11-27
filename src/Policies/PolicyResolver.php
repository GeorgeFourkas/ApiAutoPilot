<?php

namespace ApiAutoPilot\ApiAutoPilot\Policies;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PolicyResolver
{

    protected array $policies = [];
    protected string $modelName;
    protected string $modelNamespace;

    /**
     * @throws \ReflectionException
     */
    public function __construct(string $modelNamespace, string $modelName,)
    {
        $this->modelName = ucfirst($modelName);
        $this->modelNamespace = $modelNamespace;
        $this->policies = $this->getAppPolicies();

    }


    public function policyExists()
    {

        return $this->modelHasGuessedPolicy() || $this->modelHasRegisteredPolicy();
    }


    protected function modelHasRegisteredPolicy()
    {
        return Arr::get($this->policies, $this->modelNamespace);
    }

    protected function modelHasGuessedPolicy(): array
    {
        $policyNamespace = "App\\Policies\\" . $this->modelName . "Policy";
        if (class_exists($policyNamespace)) {
            return [
                "App\\Models\\" . $this->modelName => $policyNamespace
            ];
        }
        return [];
    }

    /**
     * @throws \ReflectionException
     */
    protected function getAppPolicies()
    {
        if (class_exists('App\\Providers\\AuthServiceProvider')) {
            $reflection = new \ReflectionClass('App\\Providers\\AuthServiceProvider');
            $dynamicClass = $reflection->newInstanceArgs([app()]);
            return ($reflection->getProperty('policies')->getValue($dynamicClass));
        }
        return [];
    }


}
