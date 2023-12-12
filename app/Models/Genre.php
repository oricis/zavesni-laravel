<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    public function tracks() : HasMany {
        return $this->hasMany(Track::class);
    }
    public function playlists() : HasMany {
        return $this->hasMany(Playlist::class);
    }
    public function actors() {
        return $this->belongsToMany(Actor::class, 'actor_genre_preferences');
    }
}
