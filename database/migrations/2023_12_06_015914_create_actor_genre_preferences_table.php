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
        Schema::create('actor_genre_preferences', function (Blueprint $table) {
            $table->primary(['actor_id', 'genre_id']);
            $table->foreignUuid('actor_id')->references('id')->on('actors');
            $table->foreignUuid('genre_id')->references('id')->on('genres');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actor_genre_preferences');
    }
};
