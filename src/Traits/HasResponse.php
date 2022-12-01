<?php

namespace ApiAutoPilot\ApiAutoPilot\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;


trait HasResponse
{
    use HasDevOrTestingResponse;

    public function notFoundResponse(): JsonResponse
    {

        return response()->json([
            'error' => [
                'error_message' => 'Endpoint Not Found!',
                'error_code' => '404',
            ],
        ], 404);
    }

    public function endpointNotEnabledResponse(): jsonResponse
    {
        return response()->json([
                'error' => [
                    'error_message' => 'the endpoint is not enabled',
                    'code' => '403',
                ],
            ] + $this->endpointIsDisabledInConfig(), Response::HTTP_FORBIDDEN);
    }




    public function createAssociatedResponse(array|Collection $models): JsonResponse
    {
        return \response()->json($models);
    }
}
