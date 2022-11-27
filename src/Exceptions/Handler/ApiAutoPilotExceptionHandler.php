<?php

namespace ApiAutoPilot\ApiAutoPilot\Exceptions\Handler;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiAutoPilotExceptionHandler extends ExceptionHandler
{

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->get('isAutoPilotRequest') && $request->is('api/*')) {
                return response()->json([
                    'error' => [
                        'error_message' => 'record not found',
                        'http_response_code' => 404
                    ]
                ], 404);
            }
        });

        $this->renderable(function (MethodNotAllowedHttpException $e) {
            return response()->json(
                [
                    'error' => 'method not allowed'
                ], 404);
        });
    }
}
