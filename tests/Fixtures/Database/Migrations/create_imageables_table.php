<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\Migrations;

use ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Models\Image;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::create('imageables', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Image::class);
            $table->morphs('imageable');
            $table->timestamps();
        });
    }
};
