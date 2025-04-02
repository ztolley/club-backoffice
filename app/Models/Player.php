<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Traits\HasUuid;

class Player extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'dob' => 'date',
        'allowed_marketing' => 'boolean',
        'allowed_photography' => 'boolean',
        'agreed_player_code' => 'boolean',
        'agreed_parent_code' => 'boolean',
        'signed_date' => 'date',
    ];

    public function applicant(): HasOne
    {
        return $this->hasOne(Applicant::class, 'player_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
