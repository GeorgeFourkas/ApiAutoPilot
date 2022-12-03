<?php

namespace ApiAutoPilot\ApiAutoPilot\Http\Controllers;

use ApiAutoPilot\ApiAutoPilot\Exceptions\Handler\ApiAutoPilotExceptionHandler;
use ApiAutoPilot\ApiAutoPilot\Http\Requests\AttachDetachSyncRequest;
use ApiAutoPilot\ApiAutoPilot\ManyToManyRelationshipHandler;
use ApiAutoPilot\ApiAutoPilot\ModelResolver;
use ApiAutoPilot\ApiAutoPilot\SearchResolver;
use ApiAutoPilot\ApiAutoPilot\Traits\HasPolicies;
use ApiAutoPilot\ApiAutoPilot\Traits\HasResponse;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class ApiAutoPilotController extends Controller
{
    use HasResponse;
    use HasPolicies;

    public function __construct()
    {
        \App::singleton(
            ExceptionHandler::class,
            ApiAutoPilotExceptionHandler::class
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $modelName): JsonResponse
    {
        $model = $request->get('modelClass');
        $this->callPolicy('viewAny', $model::class, $modelName);
        $pagination = config('apiautopilot.settings.'.$model::class.'.pagination');
        if ($pagination) {
            return response()->json($model->paginate($pagination));
        }

        return response()->json($model->all());
    }

    /**
     * @throws \ReflectionException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function get(Request $request, $modelName, $id): JsonResponse
    {
        $model = $request->get('modelClass');
        $loaded = $model::findOrFail($id);
        $this->callPolicy('view', $model::class, $modelName, $loaded);

        return response()->json($loaded);
    }

    /**
     * @throws \ReflectionException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Request $request, $modelName, ModelResolver $resolver, $related = null): JsonResponse
    {
        return $resolver
            ->setRelatedFunctionNameFromSecondaryParam($related)
            ->setRequest($request)
            ->resolveAndCreate();
    }

    public function edit(Request $request, $modelName, $id, ModelResolver $resolver): JsonResponse
    {
        return $resolver
            ->setRequest($request)
            ->setId($id)
            ->resolveAndUpdate();
    }

    public function delete(Request $request, $modelName, $id, ModelResolver $resolver): JsonResponse
    {
        return $resolver
            ->setRequest($request)
            ->setId($id)
            ->resolveAndDelete();
    }

    /**
     * @throws \ApiAutoPilot\ApiAutoPilot\Exceptions\MalformedQueryException
     */
    public function search(Request $request, SearchResolver $resolver): JsonResponse
    {
        return $resolver
            ->setModel($request->get('modelClass'))
            ->setRequestQueries($request->query())
            ->validateQuery()
            ->reformOperators()
            ->runQuery();
    }

    /**
     * @throws \ApiAutoPilot\ApiAutoPilot\Exceptions\NotAbleForPivotTableOperationsException
     */
    public function attach(Request $request, $modelName, $id, $related, ManyToManyRelationshipHandler $handler): JsonResponse
    {
        return $handler
            ->setRequest($request)
            ->setModelID($id)
            ->verifyManyToManyExists($related)
            ->attachTo()
            ->responseToAttach();
    }

    /**
     * @throws \ApiAutoPilot\ApiAutoPilot\Exceptions\NotAbleForPivotTableOperationsException
     */
    public function detach(AttachDetachSyncRequest $request, $modelName, $id, $related, ManyToManyRelationshipHandler $handler): JsonResponse
    {
        $modelClass = $request->get('modelClass');

        return $handler
            ->setRequest($request)
            ->setModelClass($modelClass)
            ->setModelID($id)
            ->verifyManyToManyExists($related)
            ->detachFrom(($modelClass)::findOrFail($id))
            ->responseToDetach();
    }

    /**
     * @throws \ApiAutoPilot\ApiAutoPilot\Exceptions\NotAbleForPivotTableOperationsException
     */
    public function sync(AttachDetachSyncRequest $request, $modelName, $id, $related, ManyToManyRelationshipHandler $handler): JsonResponse
    {
        $modelClass = $request->get('modelClass');

        return $handler
            ->setRequest($request)
            ->setModelClass($modelClass)
            ->setModelID($id)
            ->verifyManyToManyExists($related)
            ->sync(($modelClass)::findOrFail($id))
            ->responseToAttach();
    }

    public function getWithRelation(Request $request, $modelName, $id, $related): JsonResponse
    {
        $modelClass = $request->get('modelClass');
        $model = $modelClass::findOrFail($id);
        $this->callPolicy('view', $modelClass::class, $modelName, $model);
        if (Arr::exists($request->get('relationships'), $related)) {
            return response()->json($model->load($related));
        }
        return $this->notFoundResponse();
    }
}
