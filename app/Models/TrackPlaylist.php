<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackPlaylist extends Model
{
    use HasFactory;
    use HasUuids;
    use HasTimestamps;
    protected $fillable = ['track_id', 'playlist_id'];

    protected $table = 'track_playlist';

    public function track () {
        return $this->belongsTo(Track::class, 'track_id');
    }

    public function playlist() {
        return $this->belongsTo(Playlist::class, 'playlist_id');
    }
}
