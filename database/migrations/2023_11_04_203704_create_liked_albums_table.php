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
        Schema::create('liked_albums', function (Blueprint $table) {
            $table->primary(['actor_id', 'album_id']);
            $table->foreignUuid('actor_id')->references('id')->on('actors');
            $table->foreignUuid('album_id')->references('id')->on('albums');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liked_albums');
    }
};
