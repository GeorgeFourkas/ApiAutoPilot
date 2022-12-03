<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models;

use ApiAutoPilot\ApiAutoPilot\Interfaces\FileManipulation;
use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\Factories\PostFactory;
use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Http\Requests\CreatePostRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\UploadedFile;

class Post extends Model implements FileManipulation
{
    use HasFactory;

    protected $fillable = ['body', 'title', 'user_id', 'featured_image_url', 'original_client_name', 'extension', 'size'];

    public array $requestValidations = [
        'create' => CreatePostRequest::class,
    ];

    public function setAdditionalFileData(UploadedFile $file): array
    {
        return [
            'original_client_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'size' => $file->getSize(),
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function meta()
    {
        return $this->morphOne(Meta::class, 'metaable');
    }

    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    protected static function newFactory()
    {
        return PostFactory::new();
    }
}
