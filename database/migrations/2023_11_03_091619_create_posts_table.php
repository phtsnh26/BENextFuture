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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->text('caption')->nullable();
            $table->text('images')->nullable();
            $table->integer('react')->default(0);
            $table->integer('is_view_like')->default(1);
            $table->integer('is_view_comment')->default(1);
            $table->integer('privacy');
            $table->integer('id_client');
            $table->integer('id_tag')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
