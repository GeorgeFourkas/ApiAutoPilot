<?php

namespace ApiAutoPilot\ApiAutoPilot\Exceptions;

use Exception;

class NotAbleForPivotTableOperationsException extends Exception
{


    public function render($request)
    {
        return response()->json(
            [
                'error' => [
                    'error_message' => 'Invalid Association Request',
                    'http_response_code' => 400
                ]
            ],
            400
        );
    }


}
