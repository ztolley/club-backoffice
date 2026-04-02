<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = ['name', 'email', 'phone'];

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'contact_player');
    }
}
