<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Track extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $casts = [
        'explicit' => 'boolean',
    ];

    public function playlists() : BelongsToMany {
        return $this->belongsToMany(Playlist::class);
    }
    public function owner() : HasOne {
        return $this->hasOne(Artist::class, 'id', 'owner_id');
    }
    public function features() : BelongsToMany {
        return $this->belongsToMany(Artist::class, 'features');
    }
    public function album() : HasOne {
        return $this->hasOne(Album::class, 'id', 'album_id');
    }
    public function likes() : BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'liked_tracks');
    }
    public function genre() : HasOne {
        return $this->hasOne(Genre::class, 'id', 'genre_id');
    }
    public function trackPlays()
    {
        return $this->hasMany(TrackPlay::class);
    }
 }
