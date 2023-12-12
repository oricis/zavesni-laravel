<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSettings extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    protected $fillable = [
        'explicit', 'history'
    ];
    public function user() : BelongsTo {
        return $this->belongsTo(Actor::class);
    }

}
