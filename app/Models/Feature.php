<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;
    protected $table = 'features';
    protected $fillable = ['track_id', 'artist_id'];

    public function track() {
        return $this->belongsTo(Track::class, 'track_id', 'id');
    }
    public function artist() {
        return $this->belongsTo(Artist::class, 'artist_id', 'id');
    }
}
