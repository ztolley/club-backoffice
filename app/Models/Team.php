<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasUuid;

class Team extends Model
{
    use HasFactory;
    use HasUuid;

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }
}
