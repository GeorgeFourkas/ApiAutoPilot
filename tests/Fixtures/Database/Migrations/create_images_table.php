<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {

    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('image_url');
            $table->string('size');
            $table->string('extension');
            $table->string('original_name');
            $table->timestamps();
        });
    }
};
