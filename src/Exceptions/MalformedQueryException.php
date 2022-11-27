<?php

namespace ApiAutoPilot\ApiAutoPilot\Exceptions;

use Exception;

class MalformedQueryException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'error' => [
                'error_message' => 'Malformed Query',
                'http_response_code' => 422,
            ],
        ], 422);
    }
}
