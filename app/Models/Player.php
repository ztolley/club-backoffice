<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


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
        'agreed_player_code' => 'boolean',
        'agreed_parent_code' => 'boolean',
        'signed_date' => 'date',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function alternateTeam()
    {
        return $this->belongsTo(Team::class, 'alternate_team_id');
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_player')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function contractSignatures(): HasMany
    {
        return $this->hasMany(PlayerContractSignature::class);
    }
}
