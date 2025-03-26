<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Player extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'parent_carer_names',
        'address',
        'postal_code',
        'primary_email',
        'primary_phone',
        'dob',
        'preferred_position',
        'other_positions',
        'medical_conditions',
        'injuries',
        'additional_info',
        'allowed_marketing',
        'allowed_photography',
        'agreed_player_code',
        'agreed_parent_code',
        'signed_date',
        'team_id',
        'applicant_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'dob' => 'date',
        'allowed_marketing' => 'boolean',
        'allowed_photography' => 'boolean',
        'agreed_player_code' => 'boolean',
        'agreed_parent_code' => 'boolean',
        'signed_date' => 'date',
        'team_id' => 'integer',
    ];

    public function applicant(): HasOne
    {
        return $this->hasOne(Applicant::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
