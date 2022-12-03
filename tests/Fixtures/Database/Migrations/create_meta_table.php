<?php

namespace ApiAutoPilot\ApiAutoPilot\Tests\Fixtures\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::create('metas', function (Blueprint $table) {
            $table->id();
            $table->string('meta_text');
            $table->text('meta_label');
            $table->morphs('metaable');
            $table->timestamps();
        });
    }
};
