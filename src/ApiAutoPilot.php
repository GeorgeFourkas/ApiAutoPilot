<?php

namespace ApiAutoPilot\ApiAutoPilot;

use ApiAutoPilot\ApiAutoPilot\Http\Controllers\ApiAutoPilotController;
use Illuminate\Support\Facades\Route;

class ApiAutoPilot
{
    public function routes(): void
    {
        Route::middleware(['enforceJson', 'modelSearch'])
            ->group(function () {
                /*
                 * Because of Incompatibility with form-data requests using the PATCH Method,
                 *  we will use POST using a prefix of update until it is fixed.
                 */
                Route::post('/update/{modelName}/{id}', [ApiAutoPilotController::class, 'edit'])
                    ->name('update');
                Route::get('/{modelName}', [ApiAutoPilotController::class, 'index'])
                    ->name('index');
                Route::get('/{modelName}/{id}', [ApiAutoPilotController::class, 'get'])
                    ->name('show');
                Route::post('/{modelName}/{related?}', [ApiAutoPilotController::class, 'create'])
                    ->name('create');
                Route::delete('{modelName}/{id}', [ApiAutoPilotController::class, 'delete'])
                    ->name('delete');


                Route::get('/search/query/{modelName}', [ApiAutoPilotController::class, 'search'])
                    ->name('search');
                Route::get('{modelName}/{id}/{relation}', [ApiAutoPilotController::class, 'getWithRelation'])
                    ->name('show.relationship');
                Route::post('/{modelName}/{id}/{second}/attach', [ApiAutoPilotController::class, 'attach'])
                    ->name('attach');
                Route::delete('/{modelName}/{id}/{second}/detach', [ApiAutoPilotController::class, 'detach'])
                    ->name('detach');
                Route::patch('/{modelName}/{id}/{second}/sync', [ApiAutoPilotController::class, 'sync'])
                    ->name('sync');
            });
    }
}
