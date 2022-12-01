<?php

namespace ApiAutoPilot\ApiAutoPilot;

use ApiAutoPilot\ApiAutoPilot\Traits\HasPolicies;
use ApiAutoPilot\ApiAutoPilot\Traits\HasResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class ModelResolver
{
    use HasResponse;
    use HasPolicies;

    protected array $relations;

    protected string|null $relatedModelRouteParam;

    protected Request $request;

    protected Model $model;

    protected int $id;

    public function setRelatedFunctionNameFromSecondaryParam(string|null $relatedModelRouteParam): static
    {
        $this->relatedModelRouteParam = $relatedModelRouteParam;

        return $this;
    }

    public function setId($modelId): static
    {
        $this->id = $modelId;

        return $this;
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;
        $this->relations = $this->request->get('relationships');
        $this->model = $this->request->get('modelClass');

        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws AuthorizationException
     */
    public function resolveAndCreate(): JsonResponse
    {
        $this->validateRequest('create');
        if (! is_null($this->relatedModelRouteParam)) {
            if (! method_exists($this->model, $this->relatedModelRouteParam)) {
                return $this->notFoundResponse();
            }

            $response = (new CreateMany())->setModel($this->model)
                ->setRelated(
                    (new Relation())
                        ->setRelatedModelClass($this->getRelatedIndex('model'))
                        ->setFunctionName($this->getRelatedIndex('name'))
                        ->setType($this->getRelatedIndex('type'))
                )
                ->setRequest($this->request)
                ->create();
        } else {
            $response = (new CreateOne())
                ->setRequest($this->request)
                ->setModel($this->model)
                ->create();
        }

        return $this->createAssociatedResponse($response);
    }

    public function resolveAndUpdate(): JsonResponse
    {
        try {
            $this->validateRequest('update');
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => 'unauthorized'], 401);
        }
        $model = ($this->model)::findOrFail($this->id);
        $this->callPolicy('update', $model::class, $this->request->route('modelName'), $model);

        $urlColumn = FileUrlResolver::findUrlTableColumn($this->model);
        $handler = new FileHandler($this->model, $urlColumn);
        $values = $handler->replaceUploadedFileToUrl($this->request->all());
        if ($handler->foundFile()) {
            $values = array_merge($values, $values[$urlColumn]);
        }
        $model->update($values);

        return response()->json(($this->model)::findOrFail($this->id));
    }

    public function resolveAndDelete(): JsonResponse
    {
        $model = ($this->model)::findOrFail($this->id);
        $this->callPolicy('delete', $model::class, $this->request->route('modelName'), $model);
        $model->delete();

        return response()->json(['success' => ['message' => "deleted record with id: $this->id"]]);
    }

    protected function getRelatedIndex(string $index)
    {
        return $this->relations[$this->request->route('related')][$index];
    }

    /**+
     * @return void
     * @throws UnauthorizedException if the authorize method of FormRequest returns false
     */
    protected function validateRequest($operation): void
    {
        if (isset($this->model->requestValidations[$operation])) {
            $validationClass = (new $this->model->requestValidations[$operation]);
            if (! $validationClass->authorize()) {
                throw new UnauthorizedException();
            }
            $this->request->validate($validationClass->rules());
        }
    }
}
