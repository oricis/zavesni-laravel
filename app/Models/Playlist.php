<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Playlist extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'image_url'
    ];
    public function owner(){
        return $this->belongsTo(Actor::class);
    }
    public function tracks() : BelongsToMany {
        return $this->belongsToMany(Track::class, 'track_playlist')->withPivot('created_at', 'updated_at', 'id')->orderByPivot('created_at', 'asc');
    }
    public function genre(): BelongsTo {
        return $this->belongsTo(Genre::class);
    }
}
