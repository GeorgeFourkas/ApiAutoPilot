<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models;

use ApiAutoPilot\ApiAutoPilot\Interfaces\FileManipulation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\UploadedFile;

class Image extends Model implements FileManipulation
{
    protected $fillable = ['image_url', 'size', 'extension', 'original_name'];

    public function setAdditionalFileData(UploadedFile $file):array
    {
        return [
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'imageable');
    }

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'imageable');
    }
}
