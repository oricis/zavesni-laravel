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
        Schema::table('track_playlist', function (Blueprint $table) {
            $table->dropPrimary(['track_id', 'playlist_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('track_playlist', function (Blueprint $table) {
            $table->primary(['track_id', 'playlist_id']);
        });
    }
};
