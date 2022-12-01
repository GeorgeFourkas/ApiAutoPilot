<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function phone()
    {
        return $this->hasOne(Phone::class);
    }

    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable');
    }
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
