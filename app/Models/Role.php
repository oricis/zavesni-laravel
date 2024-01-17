<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'roles';

    public function actors() {
        return $this->hasMany(Actor::class);
    }
}
