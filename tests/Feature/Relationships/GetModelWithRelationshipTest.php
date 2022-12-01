<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Feature\Relationships;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Post;
use ApiAutoPilot\ApiAutoPilot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

class GetModelWithRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_bring_the_users_posts_only_containing_the_correct_user_id()
    {
        Post::create([
            'title' => fake()->title,
            'body' => fake()->paragraph,
            'user_id' => 1,
            'featured_image_url' => 'http://somewhere.com/',
            'extension' => '.jpg',
            'size' => 12342,
            'original_client_name' => 'official_name_of_client_upload',
        ]);
        Post::create(
            [
                'title' => fake()->title,
                'body' => fake()->paragraph,
                'user_id' => 1,
                'featured_image_url' => 'http://somewhere.com/',
                'extension' => '.jpg',
                'size' => 12342,
                'original_client_name' => 'official_name_of_client_upload',
            ]);

        Post::create(
            [
                'title' => fake()->title,
                'body' => fake()->paragraph,
                'user_id' => 1,
                'featured_image_url' => 'http://somewhere.com/',
                'extension' => '.jpg',
                'size' => 12342,
                'original_client_name' => 'official_name_of_client_upload',
            ]);
        $this->assertDatabaseCount('posts', 18);
        $response = $this->get('/api/aap/user/1/posts');
        $response->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->has('posts')
                    ->where('posts.0.user_id', 1)
                    ->where('posts.1.user_id', 1)
                    ->where('posts.2.user_id', 1)
                    ->etc();
            });
    }
}
