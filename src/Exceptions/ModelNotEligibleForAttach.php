<?php

namespace ApiAutoPilot\ApiAutoPilot\Exceptions;

use Exception;

class ModelNotEligibleForAttach extends Exception
{
    public function render()
    {
        return response()->json([
            'error' => [
                'error_message' => 'The endpoint does not exist',
                'http_response_code' => 400,
            ],
        ], 400);
    }
}
