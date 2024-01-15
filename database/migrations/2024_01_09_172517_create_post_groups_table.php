<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_groups', function (Blueprint $table) {
            $table->id();
            $table->text('caption')->nullable();
            $table->text('images')->nullable();
            $table->integer('privacy');
            $table->tinyInteger('status');
            $table->integer('id_client');
            $table->integer('id_group');
            $table->integer('id_tag')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_groups');
    }
};
