<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Feature;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Post;
use ApiAutoPilot\ApiAutoPilot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExcludedEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_throws_exception_when_trying_to_access_get_all_models_and_endpoint_is_disabled()
    {
        config(['apiautopilot.index.exclude' => [Post::class]]);
        $this->get('/api/aap/post')->assertStatus(403);
    }

    public function test_it_throws_exception_when_trying_to_access_get_single_models_and_endpoint_is_disabled()
    {
        config(['apiautopilot.show.exclude' => [Post::class]]);
        $this->get('/api/aap/post/1')->assertStatus(403);
    }

    public function test_it_throws_exception_when_trying_to_access_create_single_models_and_endpoint_is_disabled()
    {
        config(['apiautopilot.create.exclude' => [Post::class]]);
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $this->post('/api/aap/post/', [
            'title' => 'lorem ipsum',
            'body' => 'ipsum lorem',
            'featured_image_url' => $file,
            'user_id' => 1,
        ])->assertStatus(403);
    }

    public function test_it_throws_exception_when_trying_to_access_delete_single_models_and_endpoint_is_disabled()
    {
        config(['apiautopilot.delete.exclude' => [Post::class]]);
        $this->delete('/api/aap/post/1')->assertStatus(403);
    }

    public function test_it_throws_exception_when_trying_to_access_update_single_models_and_endpoint_is_disabled()
    {
        config(['apiautopilot.update.exclude' => [Post::class]]);
        $this->postJson('/api/aap/update/post/1', [
            'title' => 'updated title'
        ])->assertStatus(403);
    }

    public function test_it_throws_exception_when_trying_to_access_attach_models_and_main_model_endpoint_is_disabled()
    {
        config(['apiautopilot.attach.exclude' => [Post::class]]);
        $this->postJson('/api/aap/post/1/comments/attach', [
            'ids' => [1, 2]
        ])->assertStatus(403);
    }

    public function test_it_throws_exception_when_trying_to_access_detach_models_and_main_model_endpoint_is_disabled()
    {
        config(['apiautopilot.detach.exclude' => [Post::class]]);
        $this->delete('/api/aap/post/1/comments/detach', [
            'ids' => [1, 2]
        ])->assertStatus(403);
    }

    public function test_it_throws_exception_when_trying_to_access_sync_models_and_main_model_endpoint_is_disabled()
    {
        config(['apiautopilot.sync.exclude' => [Post::class]]);
        $this->postJson('/api/aap/post/1/comments/sync', [
            'ids' => [1, 2]
        ])->assertStatus(403);
    }

}
