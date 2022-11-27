<?php

namespace ApiAutoPilot\ApiAutoPilot\Traits;

trait HasPivotKeys
{
    protected function flatenForPivot($data)
    {
        $vals = [];
        foreach ($data as $index => $id) {
            if (is_array($id)) {
                foreach ($id as $key => $indiv) {
                    $vals[$key] = $indiv;
                }
            }else{
                $vals[] = $id;
            }
        }
        return $vals;
    }
}
