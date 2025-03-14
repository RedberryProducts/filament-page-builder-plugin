<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('page_builder_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('block_type');
            $table->morphs('page_builder_blockable');
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }
};
