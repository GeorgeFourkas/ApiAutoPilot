<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['text', 'user_id'];

    public function commentable()
    {
        $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
