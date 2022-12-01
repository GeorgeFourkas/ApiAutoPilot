<?php

namespace ApiAutoPilot\ApiAutoPilot;

use Illuminate\Database\Eloquent\Model;

class FileUrlResolver
{
    public static function findUrlTableColumn(Model $model): string
    {
        $column = config('apiautopilot.settings.'.$model::class.'.database_file_url');
        if (! is_null($column)) {
            return $column;
        } elseif (property_exists($model,
            'urlColumn') && ! is_null($model->urlColumn)) {
            return $model->urlColumn;
        }

        return 'file_url';
    }
}
