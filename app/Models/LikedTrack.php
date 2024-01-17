<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikedTrack extends Model
{
    use HasFactory;

    protected $table = 'liked_tracks';


    public function actors() {
        return $this->belongsTo(Actor::class, 'actor_id');
    }

    public function tracks() {
        return $this->belongsTo(Track::class, 'track_id');
    }
}
