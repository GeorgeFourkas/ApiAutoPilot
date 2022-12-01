<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\Migrations;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->foreignIdFor(User::class);
            $table->string('featured_image_url');
            $table->string('extension');
            $table->string('size');
            $table->string('original_client_name');
            $table->timestamps();
        });
    }
};
