<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models;

use ApiAutoPilot\ApiAutoPilot\Database\Factories\PhoneFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $fillable = [
        'number', 'provider_logo', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    protected static function newFactory()
    {
        return PhoneFactory::new();
    }
}
