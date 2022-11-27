<?php

namespace ApiAutoPilot\ApiAutoPilot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RelationshipResolver
{
    protected Request $request;

    protected array $relationships;

    protected Model $model;

    protected Relation $relation;

    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function setRelationshipsArray(array $relationships): static
    {
        $this->relationships = $relationships;

        return $this;
    }

    public function setModelClass(Model $model): void
    {
        $this->model = $model;
    }

    public function resolveRelation(string $urlQuery, Relation $relation): bool|Relation
    {
        $exists = (Arr::get($this->relationships, $urlQuery, false));
        if ($exists) {
            $relation
                ->setFunctionName($exists['name'])
                ->setRelatedModelClass($exists['model'])
                ->setType('type');

            return $relation;
        } else {
            return false;
        }
    }
}
