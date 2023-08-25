<?php

namespace ApiAutoPilot\ApiAutoPilot\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use ReflectionClass;
use ReflectionMethod;

trait HasModelRelationships
{
    public function getRelationships(Model $model): array
    {
        $relationships = [];

        foreach ((new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class != get_class($model) || ! empty($method->getParameters()) || $method->getName() == __FUNCTION__) {
                continue;
            }
            try {
                $return = $method->invoke($model);
                if ($return instanceof Relation) {
                    $relationships[$method->getName()] = [
                        'name' => $method->getName(),
                        'type' => (new ReflectionClass($return))->getShortName(),
                        'model' => (new ReflectionClass($return->getRelated()))->getName(),
                    ];
                }
            } catch (\ReflectionException $e) {
                echo $e->getMessage();
            }
        }

        return $relationships;
    }
}
