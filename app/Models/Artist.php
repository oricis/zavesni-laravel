<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artist extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    public function albums() : HasMany {
        return $this->hasMany(Album::class);
    }
    public function ownTracks() : HasMany {
        return  $this->hasMany(Track::class, 'owner_id', 'id');
    }
    public function singles() : HasMany {
        return $this->hasMany(Track::class, 'owner_id', 'id')->whereDoesntHave('features');
    }
    public function featureTracks() : BelongsToMany {
        return $this->belongsToMany(Track::class, 'features');
    }
    public function popular() : HasMany {
        return $this->hasMany(Track::class);
    }
    public function followedBy() : BelongsToMany {
        return $this->belongsToMany(Actor::class,'following', 'artist_id', 'actor_id');
    }
}
