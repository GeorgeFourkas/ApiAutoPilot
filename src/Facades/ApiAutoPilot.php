<?php

namespace ApiAutoPilot\ApiAutoPilot\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ApiAutoPilot\ApiAutoPilot\ApiAutoPilot
 */
class ApiAutoPilot extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \ApiAutoPilot\ApiAutoPilot\ApiAutoPilot::class;
    }
}
