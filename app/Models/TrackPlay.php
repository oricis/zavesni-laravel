<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackPlay extends Model
{
    use HasFactory;
    use HasUuids;
    protected $table = 'track_plays';
    protected $fillable = [
        'actor_id',
        'track_id'
    ];

    public function track()
    {
        return $this->belongsTo(Track::class, 'track_id');
    }
    public function actors() {
        return $this->belongsTo(Actor::class);
    }
}
