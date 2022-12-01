<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Feature;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Post;
use ApiAutoPilot\ApiAutoPilot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetSingleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_return_not_found_when_model_does_not_exist()
    {
        $this->get('/api/aap/something_that_doesnt_exist')->assertNotFound()->assertJson([
            'error' => [
                'error_message' => 'Endpoint Not Found!',
                'error_code' => '404',
            ], ]);
    }

    public function test_it_can_get_posts()
    {
        $response = $this->get('/api/aap/post/1');
        $response->assertOk();
    }

    public function test_it_cannot_get_post_when_post_model_class_is_set_in_config_file_exclude_index_array()
    {
        config(['apiautopilot.show.exclude' => [Post::class]]);
        $response = $this->get('/api/aap/post/1');
        $response->assertStatus(403);
    }

    public function test_it_returns_record_does_not_exist_when_provided_id_of_model_that_is_not_present_in_the_database()
    {
        $this
            ->get('/api/aap/post/'.Post::max('id') + 10)
            ->assertNotFound()
            ->assertJson(['error' => []]);
    }
}
