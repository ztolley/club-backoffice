<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * PlayerContractSignature
 *
 * Represents a contract signed by a player containing their name, fan number, agreed to statement, signature and the date they signed
 */
class PlayerContractSignature extends Model
{
    use HasUuids;

    protected $casts = [
        'submitted_at' => 'datetime',
    ];
}
