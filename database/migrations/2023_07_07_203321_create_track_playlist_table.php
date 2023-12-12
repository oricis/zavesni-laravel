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
        Schema::create('track_playlist', function (Blueprint $table) {
            $table->primary(['track_id', 'playlist_id']);

            $table->foreignUuid('track_id')->references('id')->on('tracks');
            $table->foreignUuid('playlist_id')->references('id')->on('playlists');
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_playlist');
    }
};
