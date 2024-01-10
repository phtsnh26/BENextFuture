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
        Schema::create('comment_groups', function (Blueprint $table) {
            $table->id();
            $table->string('content');
            $table->string('id_tag')->nullable();
            $table->integer('id_client');
            $table->integer('id_replier')->nullable();
            $table->integer('id_post');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_groups');
    }
};
