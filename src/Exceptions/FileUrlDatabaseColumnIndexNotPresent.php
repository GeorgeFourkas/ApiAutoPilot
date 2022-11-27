<?php

namespace ApiAutoPilot\ApiAutoPilot\Exceptions;

use Throwable;

class FileUrlDatabaseColumnIndexNotPresent extends \Exception
{
    protected string $columnUrl;

    public function __construct(string $columnUrl, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $this->columnUrl = $columnUrl;
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'error' => [
                'error_message' => $this->columnUrl.' is required to upload a file',
                'http_response_code' => 422,
            ],
        ], 422);
    }
}
