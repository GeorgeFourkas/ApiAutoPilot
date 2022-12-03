<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Feature;

use ApiAutoPilot\ApiAutoPilot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_404_not_found_when_model_trying_to_delete_is_not_found()
    {
        $this->delete('/api/aap/post/-1')
            ->assertNotFound();
    }

    public function test_it_deletes_the_model_when_id_is_valid()
    {
        $this->delete('/api/aap/post/2')->assertOk();
        $this->assertDatabaseMissing('posts', ['id' => 2]);
    }

    public function test_it_returns_404_when_model_is_not_resolved()
    {
        $this->delete('/api/aap/something_not_existing/2')
            ->assertNotFound();
    }
}
