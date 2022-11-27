<?php

namespace ApiAutoPilot\ApiAutoPilot;

use ApiAutoPilot\ApiAutoPilot\Interfaces\CreateModel;
use ApiAutoPilot\ApiAutoPilot\Traits\HasPolicies;
use ApiAutoPilot\ApiAutoPilot\Traits\HasResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CreateMany implements CreateModel
{
    use HasResponse;
    use HasPolicies;

    /*
     * This class is used to create Models and relating them at the same time.
     * E.G. creating Post with Tag and associating them or syncing
     */

    protected Relation $related;

    protected Model $model;

    protected Request $request;

    public function setRelated(Relation $related): static
    {
        $this->related = $related;

        return $this;
    }

    public function setModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(): array
    {
        $urlColumn = FileUrlResolver::findUrlTableColumn($this->model);
        if ($this->isArrayOfIds()) {
            if (! $this->isEligibleForAttaching()) {
                return ['error' => ['error_message' => 'the endpoint doesnt exist']];
            }
            $created = $this->createMainModel();
            $created->{$this->related->getFunctionName()}()->attach($this->request->{$this->related->getFunctionName()});

            return $created->load($this->related->getFunctionName())->toArray();
        } else {
            $requestData = $this->getRequestData();
            $relatedModel = (new ($this->related->getRelatedModelClass()));
            //If cant create related model don't create the even the first model
            $this->callPolicy('create', $relatedModel::class, $this->request->route('related'));
            $created = $this->createMainModel();
            $urlColumn = FileUrlResolver::findUrlTableColumn(new ($this->related->getRelatedModelClass()));
            $handler = new FileHandler($relatedModel, $urlColumn);
            $values = $handler->replaceUploadedFileToUrl($requestData);
            if ($handler->requestHasFile()) {
                foreach ($values as $key => $value) {
                    $values[$key] = array_merge($value, $value[$urlColumn] ?? []);
                }
            }
            if (is_string(current($values))) {
                $created->{$this->related
                    ->getFunctionName()}()
                    ->create($values);
            } else {
                $created->{$this->related
                    ->getFunctionName()}()
                    ->createMany($values);
            }

            return $created->load($this->related->getFunctionName())->toArray();
        }
    }

    protected function isArrayOfIds(): bool
    {
        if (! is_array($this->request->{$this->related->getFunctionName()})) {
            return false;
        }
        foreach ($this->request->{$this->related->getFunctionName()} ?? [] as $item) {
            if (! is_int($item)) {
                return false;
            }
        }

        return true;
    }

    protected function isEligibleForAttaching(): bool
    {
        return in_array($this->related->getType(), Constants::IS_ELIGIBLE_FOR_ATTACH);
    }

    /**
     * @throws \ReflectionException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function createMainModel()
    {
        $this->callPolicy('create', $this->model::class, $this->request->route('modelName'));
        $urlColumn = FileUrlResolver::findUrlTableColumn($this->model);
        $handler = new FileHandler($this->model, $urlColumn);
        $modelValues = $handler->replaceUploadedFileToUrl($this->request->except($this->related->getFunctionName()));

        return ($this->model)::create($modelValues);
    }

    protected function getRequestData()
    {
        return is_array($this->request->{$this->related->getFunctionName()})
            ? $this->request->{$this->related->getFunctionName()}
            : [$this->request->{$this->related->getFunctionName()}];
    }
}
