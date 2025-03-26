<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Applicant extends Model
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
        'address',
        'postal_code',
        'email',
        'phone',
        'dob',
        'school',
        'saturday_club',
        'sunday_club',
        'previous_clubs',
        'playing_experience',
        'preferred_position',
        'other_positions',
        'age_groups',
        'how_hear',
        'medical_conditions',
        'injuries',
        'additional_info',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'dob' => 'date',
    ];

    public function player(): HasOne
    {
        return $this->hasOne(Player::class);
    }
}
