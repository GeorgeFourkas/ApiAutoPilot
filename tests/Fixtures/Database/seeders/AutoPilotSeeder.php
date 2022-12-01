<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\seeders;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Post;
use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\User;
use Illuminate\Database\Seeder;

class AutoPilotSeeder extends Seeder
{
    public function run()
    {
        User::factory(5)->create();
        Post::factory(15)->create();
    }
}
