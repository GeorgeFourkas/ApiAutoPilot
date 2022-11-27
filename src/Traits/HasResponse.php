<?php

namespace ApiAutoPilot\ApiAutoPilot\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

trait HasResponse
{
    public function notFoundResponse(): JsonResponse
    {
        return response()->json([
            'error' => [
                'error_message' => 'Endpoint Not Found!',
                'error_code' => '404',
            ],
        ], 404);
    }

    public function endpointNotEnabled(): jsonResponse
    {
        App::environment('local')
            ? $tip = 'to enable this endpoint, add a $discoverable property equal to true in your model'
            : $tip = '';

        return response()->json([
            'error' => [
                'error_message' => 'the endpoint is not enabled',
                'code' => '403',
                'tip' => $tip,
            ],
        ], Response::HTTP_FORBIDDEN);
    }

    public function attachResponse($modelClass, $modelId, $associatedModelType, $aassociatedIds): JsonResponse
    {
        return response()->json([
            'model' => [
                'type' => get_class($modelClass),
                'id' => $modelId,
            ],
            'associated' => [
                'type' => $associatedModelType,
                'ids' => $aassociatedIds,
            ],
        ]);
    }

    public function syncResponse($modelClass, $modelId, $associatedModelType, $aassociatedIds): JsonResponse
    {
        return response()->json([
            'model' => [
                'type' => get_class($modelClass),
                'id' => $modelId,
            ],
            'synced' => [
                'type' => $associatedModelType,
                'ids' => $aassociatedIds,
            ],
        ]);
    }

    public function detachResponse($modelClass, $modelId, $associatedModelType, $aassociatedIds): JsonResponse
    {
        return response()->json([
            'model' => [
                'type' => get_class($modelClass),
                'id' => $modelId,
            ],
            'dissociated' => [
                'type' => $associatedModelType,
                'ids' => $aassociatedIds,
            ],
        ]);
    }

    public function relationNotFoundResponse(): JsonResponse
    {
        return response()->json([
            'error' => [
                'message' => 'the relationship you seek does not exist',
                'error_code' => 404,
            ],
        ], Response::HTTP_NOT_FOUND);
    }

    public function recordNotFound(): JsonResponse
    {
        return \response()->json([
            'error' => [
                'error_message' => 'record does not exist',
                'http_response_code' => 404,
            ],
        ], 404);
    }

    public function createAssociatedResponse(array|Collection $models): JsonResponse
    {
        return \response()->json($models);
    }
}
