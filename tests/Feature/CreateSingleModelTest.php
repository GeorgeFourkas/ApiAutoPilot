<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Feature;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Post;
use ApiAutoPilot\ApiAutoPilot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CreateSingleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_invokes_the_set_additional_file_data_method_in_model_class()
    {
        config()->set('apiautopilot.settings.'.Post::class.'.database_file_url', 'featured_image_url');
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->post('/api/aap/post/', [
            'title' => 'lorem ipsum',
            'body' => 'ipsum lorem',
            'featured_image_url' => $file,
            'user_id' => 1,
        ]);
        $response->assertOk();

        $this->assertTrue(isset($response['size']));
        $this->assertTrue(isset($response['extension']));
        $this->assertTrue(isset($response['original_client_name']));
        $this->assertTrue(isset($response['featured_image_url']));
    }

    public function test_it_can_create_post_with_uploaded_file()
    {
        config()->set('apiautopilot.settings.'.Post::class.'.database_file_url', 'featured_image_url');
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->post('/api/aap/post/', [
            'title' => 'lorem ipsum',
            'body' => 'ipsum lorem',
            'featured_image_url' => $file,
            'user_id' => 1,
        ]);
        $response->assertOk()->assertJsonFragment([

        ]);
    }

    public function test_it_throws_error_message_when_database_file_url_index_is_not_set_in_the_settings_config_array_index()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->post('/api/aap/post/', [
            'title' => 'lorem ipsum',
            'body' => 'ipsum lorem',
            'featured_image_url' => $file,
            'user_id' => 1,
        ]);
        $response->assertUnprocessable();
    }

}
