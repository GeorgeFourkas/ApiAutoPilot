<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Feature\Relationships;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Post;
use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Tag;
use ApiAutoPilot\ApiAutoPilot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Many2ManyOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_attach_the_ids_sent_in_the_request_body_to_the_post_tag_pivot_table()
    {
        Tag::create(['name' => fake()->word]);
        Tag::create(['name' => fake()->word]);
        Tag::create(['name' => fake()->word]);

        $this->postJson('/api/aap/post/1/tags/attach', [
            'ids' => [1, 2, 3],
        ])->assertOk();

        $this
            ->assertDatabaseCount('post_tag', 3)
            ->assertDatabaseHas('post_tag', ['post_id' => 1, 'tag_id' => 1])
            ->assertDatabaseHas('post_tag', ['post_id' => 1, 'tag_id' => 2])
            ->assertDatabaseHas('post_tag', ['post_id' => 1, 'tag_id' => 3]);
    }

    public function test_it_can_detach_the_ids_sent_in_the_request_body_to_the_post_tag_pivot_table()
    {
        $post = Post::find(1);
        Tag::create(['name' => fake()->word]);
        Tag::create(['name' => fake()->word]);
        Tag::create(['name' => fake()->word]);
        $post->tags()->attach([1, 2, 3]);

        $this->delete('/api/aap/post/1/tags/detach', [
            'ids' => [1, 2, 3],
        ])->assertStatus(204);
        $this->assertDatabaseCount('post_tag', 0);
    }

    public function test_it_can_sync_the_ids_sent_in_the_request_body_to_the_post_tag_pivot_table()
    {
        $post = Post::find(1);
        Tag::create(['name' => fake()->word]);
        Tag::create(['name' => fake()->word]);
        Tag::create(['name' => fake()->word]);
        $post->tags()->attach([1, 2, 3]);

        Tag::create(['name' => fake()->word]);
        Tag::create(['name' => fake()->word]);
        Tag::create(['name' => fake()->word]);

        $this->post('/api/aap/post/1/tags/sync', [
            'ids' => [4, 5, 6],
        ])->assertStatus(200);

        $this
            ->assertDatabaseCount('post_tag', 3)
            ->assertDatabaseHas('post_tag', ['post_id' => 1, 'tag_id' => 4])
            ->assertDatabaseHas('post_tag', ['post_id' => 1, 'tag_id' => 5])
            ->assertDatabaseHas('post_tag', ['post_id' => 1, 'tag_id' => 6]);
    }
}
