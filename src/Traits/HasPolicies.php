<?php

namespace ApiAutoPilot\ApiAutoPilot\Traits;

use ApiAutoPilot\ApiAutoPilot\Policies\PolicyResolver;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

trait HasPolicies
{
    use AuthorizesRequests;

    /**
     * @throws \ReflectionException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function callPolicy($action, $modelNamespace, $modelName, $model = null): void
    {
        $policyResolver = new PolicyResolver($modelNamespace, $modelName);

        if (Auth::check() && $policyResolver->policyExists()) {
            if (!is_null($model)){
                $this->authorize($action, $model);
            }else{
                $this->authorize($action, $modelNamespace);
            }
        }
    }
}
