<?php

namespace ApiAutoPilot\ApiAutoPilot;

use ApiAutoPilot\ApiAutoPilot\Exceptions\ModelNotEligibleForAttach;
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

    protected bool $isEligibleForAttaching;

    protected bool $isArrayOfIds;

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
     * @throws ModelNotEligibleForAttach
     */
    public function create(): array
    {
        if ($this->isArrayOfIds() && ! $this->isEligibleForAttaching()) {
            throw new ModelNotEligibleForAttach();
        }
        $mainModel = $this->createMainModel();
        if ($this->isArrayOfIds()) {
            $mainModel->{$this->related->getFunctionName()}()->attach($this->request->{$this->related->getFunctionName()});

            return $mainModel->load($this->related->getFunctionName())->toArray();
        } else {
            $requestData = $this->getRequestData();
            $relatedModel = (new ($this->related->getRelatedModelClass()));
            $this->createRelatedModel($mainModel, $relatedModel, $requestData);
        }

        return $mainModel->load($this->related->getFunctionName())->toArray();
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
        $requestValues = $this->request->except($this->related->getFunctionName());
        $modelValues = $handler->replaceUploadedFileToUrl($requestValues);

        return ($this->model)::create($this->flattenArray($modelValues, $urlColumn));
    }

    protected function createRelatedModel(Model $mainModel, Model $relatedModel, $requestData)
    {
        $this->callPolicy('create', $relatedModel::class, $this->request->route('related'));
        $urlColumn = FileUrlResolver::findUrlTableColumn(new $relatedModel);
        $handler = new FileHandler($relatedModel, $urlColumn);
        $values = $handler->replaceUploadedFileToUrl($requestData);
        if ($handler->requestHasFile()) {
            $values = $this->flattenArray($values, $urlColumn);
        }
        $this->saveRelatedModel($mainModel, $values);
    }

    protected function saveRelatedModel(Model $created, array $values)
    {
        if (is_string(current($values))) {
            $created->{$this->related
                ->getFunctionName()}()
                ->create($values);
        } else {
            $created->{$this->related
                ->getFunctionName()}()
                ->createMany($values);
        }
    }

    protected function getRequestData()
    {
        return is_array($this->request->{$this->related->getFunctionName()})
            ? $this->request->{$this->related->getFunctionName()}
            : [$this->request->{$this->related->getFunctionName()}];
    }

    protected function flattenArray(array $multiDimentionalArray, string $urlColumn): array
    {
        if (! $this->request->file()) {
            return $multiDimentionalArray;
        }

        $values = $multiDimentionalArray;
        if (is_array(current($values))) {
            foreach ($values as $key => $value) {
                if (isset($value[$urlColumn])) {
                    $fileData = $value[$urlColumn];
                    unset($value[$urlColumn]);
                    $values = array_merge($fileData, $value);
                }
            }
        } else {
            if (isset($values[$urlColumn])) {
                $fileData = $values[$urlColumn];
                unset($values[$urlColumn]);
                $values = array_merge($fileData, $values);
            }
        }

        return $values;
    }
}
