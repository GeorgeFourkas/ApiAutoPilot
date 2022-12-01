<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Feature\Relationships;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Phone;
use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Post;
use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Tag;
use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\User;
use ApiAutoPilot\ApiAutoPilot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\assertDatabaseCount;

class CreateModelWithRelatedTest extends TestCase
{
    use RefreshDatabase;

    /*
     * One to Many
     * User <---->> Post
    */


    public function test_it_can_create_a_user_with_many_posts_containing_many_images_in_the_request()
    {
        config()->set('apiautopilot.settings.' . Post::class . '.database_file_url', 'featured_image_url');

        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->postJson('/api/aap/user/posts', [
            'name' => fake()->name,
            'email' => fake()->email,
            'password' => fake()->password,
            'posts' => [
                [
                    'title' => 'created post with title',
                    'body' => 'lorem ipsum dolor sit amet',
                    'featured_image_url' => $file,
                ],
                [
                    'title' => 'created post with title',
                    'body' => 'lorem ipsum dolor sit amet',
                    'featured_image_url' => $file,
                ]
            ]
        ]);
        $response->assertOk();
    }

    /*
     * One To One
     * User <---> Phone
     */
    public function test_it_can_create_a_user_with_a_phone_with_file_attached_to_phone()
    {
        config()->set('apiautopilot.settings', [
            Phone::class => [
                'database_file_url' => 'provider_logo'
            ]
        ]);
        Storage::fake('aap');
        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->postJson('/api/aap/user/phone', [
            'name' => 'george fourkas',
            'email' => 'email@somewhere.com',
            'password' => '123456',
            'phone' => [
                'number' => '9823642299384221',
                'provider_logo' => $file,
            ]
        ])->assertOk();
        $content = $response->json();
        Storage::disk('aap')->assertExists(Arr::get($content, 'provider_logo'));
    }

    public function test_it_can_create_a_user_with_with_phone_without_an_uploaded_image_in_the_request_json_body()
    {
        config()->set('apiautopilot.settings.' . Phone::class . '.database_file_url', 'provider_logo');
        $this->postJson('/api/aap/user/phone', [
            'name' => 'george fourkas',
            'email' => 'email@somewhere.com',
            'password' => '123456',
            'phone' => [
                'number' => '9823642299384221',
            ]
        ])->assertOk();
    }

    /*
     * Many To Many
     *
     * Post <<--->> Tag
     */
    public function it_can_create_post_with_many_tags_and_attach_it_to_the_table()
    {
        config()->set('apiautopilot.settings.' . Post::class . '.database_file_url', 'featured_image_url');
        Storage::fake('aap');
        $file = UploadedFile::fake()->image('avatar.jpg');

        $this->postJson('/api/aap/post/tags', [
            'title' => 'lorem Ipsum',
            'body' => 'post body',
            'user_id' => 2,
            'featured_image_url' => $file,
            'tags' => [
                ['name' => 'e-commerce'],
                ['name' => 'web dev'],
                ['name' => 'programming'],
            ]
        ]);
        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('post_tag', 3);
    }

    public function test_it_can_create_a_post_model_and_attach_related_tag_ids()
    {
        Tag::create(['name' => 'tag 1']);
        Tag::create(['name' => 'tag 2']);
        Tag::create(['name' => 'tag 3']);

        config()->set('apiautopilot.settings.' . Post::class . '.database_file_url', 'featured_image_url');
        Storage::fake('aap');
        $file = UploadedFile::fake()->image('avatar.jpg');

        $this->postJson('/api/aap/post/tags', [
            'title' => 'lorem Ipsum',
            'body' => 'post body',
            'user_id' => 2,
            'featured_image_url' => $file,
            'tags' => [1, 2, 3]
        ])->assertOk();
    }

    public function test_it_can_create_tag_and_assign_all_posts()
    {
        config()->set('apiautopilot.settings.' . Post::class . '.database_file_url', 'featured_image_url');
        Storage::fake('aap');
        $file1 = UploadedFile::fake()->image('avatar.jpg');
        $file2 = UploadedFile::fake()->image('avatar.jpg');
        $file3 = UploadedFile::fake()->image('avatar.jpg');
        User::factory(1);
        $this->postJson('/api/aap/tag/posts', [
            'name' => 'tag_name',
            'posts' => [
                [
                    'title' => 'post_title_1',
                    'body' => 'body of the post',
                    'featured_image_url' => $file1,
                    'user_id' => User::all()->first()->id
                ],
                [
                    'title' => 'post_title_2',
                    'body' => 'body of the post 2',
                    'featured_image_url' => $file2,
                    'user_id' => User::all()->first()->id

                ], [
                    'title' => 'post_title_3',
                    'body' => 'body of the post',
                    'featured_image_url' => $file3,
                    'user_id' => User::all()->first()->id
                ]
            ]
        ])->assertOk();

        $this
            ->assertDatabaseCount('posts', 16)
            ->assertDatabaseCount('tags', 1);
    }

    public function test_it_throws_exception_when_trying_to_many_to_many_operations_with_not_eliglble_models()
    {
        $response = $this->postJson('/api/user/post', [
           'name' => 'aname',
           'user'
        ]);
    }

}
