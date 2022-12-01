<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Feature;

use ApiAutoPilot\ApiAutoPilot\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_update_correctly_the_values_of_an_existing_model()
    {
        $response = $this->postJson('/api/aap/update/post/2/', [
            'title' => 'updated title',
            'body' => 'updated_body',
        ]);

        $response->assertJson([
            'title' => 'updated title',
            'body' => 'updated_body',
        ])->assertOk();
    }

    public function test_it_throws_a_404_response_when_the_model_that_trying_to_update_does_not_exist()
    {
        $response = $this->postJson('/api/aap/update/post/-1', [
            'title' => 'a title',
        ]);
        $response->assertNotFound();
    }
}
