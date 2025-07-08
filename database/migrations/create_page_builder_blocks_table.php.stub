<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('page_builder_blocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('block_type');
            $table->unsignedTinyInteger('order')->index();
            $table->morphs('page_builder_blockable', indexName: 'page_builder_blockable_index');
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('page_builder_blocks');
    }
};
