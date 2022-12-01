<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['url'];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

}
