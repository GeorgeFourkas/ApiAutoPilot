<?php

namespace ApiAutoPilot\ApiAutoPilot;

use ApiAutoPilot\ApiAutoPilot\Exceptions\NotAbleForPivotTableOperationsException;
use ApiAutoPilot\ApiAutoPilot\Traits\HasPivotKeys;
use ApiAutoPilot\ApiAutoPilot\Traits\HasResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ManyToManyRelationshipHandler
{
    protected array|bool $relationExists;

    protected array $relations;

    protected Request $request;

    protected Model $model;

    private int $modelId;

    use HasResponse;
    use HasPivotKeys;

    /**
     * @return $this
     */
    public function setRequest(Request $aRequest): static
    {
        $this->request = $aRequest;
        $this->setModelRelations($this->request->get('relationships'));
        $this->model = $this->request->get('modelClass');

        return $this;
    }

    /**
     * @return $this
     */
    public function setModelClass(Model $aModel): static
    {
        $this->model = $aModel;

        return $this;
    }

    /**
     * @return $this
     */
    public function setModelID($id): static
    {
        $this->modelId = $id;

        return $this;
    }

    protected function setModelRelations(array $someRelations): void
    {
        $this->relations = $someRelations;
    }

    /**
     * @throws NotAbleForPivotTableOperationsException
     */
    public function verifyManyToManyExists($finalUrlKeyword): static
    {
        $this->relationExists = Arr::get($this->relations, $finalUrlKeyword, false);

        //Checks if there are no relationships in the model AND if the found relationship supports a pivot table
        if (empty($this->relationExists) || ! in_array($this->relationExists['type'], Constants::IS_ELIGIBLE_FOR_ATTACH)) {
            throw new NotAbleForPivotTableOperationsException();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function attachTo(): static
    {
        $loadedModel = ($this->model)::findOrFail($this->modelId);
        $loadedModel->{$this->relationExists['name']}()->attach($this->flatenForPivot($this->request->ids));

        return $this;
    }

    /**
     * @return $this
     */
    public function detachFrom(Model $model): static
    {
        $model->{$this->relationExists['name']}()->detach($this->flatenForPivot($this->request->ids));

        return $this;
    }

    public function sync(Model $model): static
    {
        $model->{$this->relationExists['name']}()->sync($this->flatenForPivot($this->request->ids));

        return $this;
    }

    public function responseToAttach(): JsonResponse
    {
        $attached = ($this->model)::with($this->relationExists['name'])
            ->findOrFail($this->modelId);

        return response()->json($attached);
    }

    public function responseToDetach(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
