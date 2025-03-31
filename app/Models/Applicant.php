<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Traits\HasUuid;

class Applicant extends Model
{
    use HasFactory;
    use HasUuid;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'dob' => 'date'
    ];

    public function player(): HasOne
    {
        return $this->hasOne(Player::class);
    }
}
