<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\Migrations;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->foreignIdFor(User::class);
            $table->morphs('commentable');
            $table->timestamps();
        });
    }
};
