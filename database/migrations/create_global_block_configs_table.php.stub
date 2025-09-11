<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('global_block_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('class_name')->unique();
            $table->json('configuration')->nullable();
            $table->timestamps();

            $table->index('class_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('global_block_configs');
    }
};