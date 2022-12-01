<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Feature;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Post;
use ApiAutoPilot\ApiAutoPilot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetAllOfAModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_all_the_posts()
    {
        $response = $this->get('/api/aap/post');
        $response->assertOk();
    }

    public function test_it_paginates_when_set_in_config()
    {
        config()->set('apiautopilot.settings.'.Post::class, ['pagination' => 1]);
        $response = $this->get('/api/aap/post');
        $response->assertSee('Next');
    }

    public function test_it_should_throw_404_when_not_found()
    {
        $this->get('/api/aap/something_not_existing')
            ->assertNotFound();
    }
}
