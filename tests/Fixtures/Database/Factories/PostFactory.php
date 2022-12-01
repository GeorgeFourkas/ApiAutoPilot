<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\Factories;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Post;
use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{

    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'body' => $this->faker->sentence,
            'user_id' => $this->faker->randomElement(User::pluck('id')),
            'featured_image_url' => 'http://somewhere.com/',
            'extension' => '.jpg',
            'size' => 12342,
            'original_client_name' => 'official_name_of_client_upload'
        ];
    }
}
