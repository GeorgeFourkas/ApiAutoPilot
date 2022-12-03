<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{

    protected $fillable = [
        'meta_text', 'meta_label'
    ];

    public function metaable()
    {
        return $this->morphTo();
    }


}
