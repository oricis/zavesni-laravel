<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Actor extends Authenticatable
{
    use HasFactory;
    use HasUuids;
    use HasApiTokens;
    use SoftDeletes;
    protected $hidden = ['password'];
    protected $casts = [
        'first_login' => 'boolean',
        'active' => 'boolean'
    ];
    public function playlists() : HasMany {
        return $this->hasMany(Playlist::class)->withCount('tracks')->orderByDesc('created_at');
    }
    public function canAddGenre() : bool {
        return false;
    }
    public function likedTracks() : BelongsToMany
    {
        return $this->belongsToMany(Track::class, 'liked_tracks')->withPivot('created_at')
            ->orderByDesc('liked_tracks.created_at')
            ->with('owner')
            ->with('album')
            ->with('features')
            ->wherePivot('deleted_at', '=', null);
    }
    public function settings(): HasOne {
        return $this->hasOne(UserSettings::class);
    }
    public function following() : BelongsToMany {
        return $this->belongsToMany(Artist::class, 'following', 'actor_id', 'artist_id');
    }
    public function preferredGenres() {
        return $this->belongsToMany(Genre::class, 'actor_genre_preferences');
    }
    public function likedAlbums() : BelongsToMany {
        return $this->belongsToMany(Album::class, 'liked_albums');
    }
}
