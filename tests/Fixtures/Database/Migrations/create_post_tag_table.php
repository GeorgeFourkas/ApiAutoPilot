<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\Migrations;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Post;
use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Tag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignIdFor(Post::class);
            $table->foreignIdFor(Tag::class);
        });
    }
};
