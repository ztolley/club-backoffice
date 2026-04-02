<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'dob' => 'date',
        'application_date' => 'date',
    ];

    public function getEstimatedAgeGroupAttribute(): ?string
    {
        if (!$this->dob) {
            return null; // Return null if no date of birth is provided
        }


        // Ensure the dob is parsed as a Carbon instance and set to the start of the day
        $dob = Carbon::parse($this->dob)->startOfDay();

        // Determine the cutoff date: August 31st of the current or next year, set to the start of the day
        $cutoffYear = now()->month >= 9 ? now()->year + 1 : now()->year;
        $cutoffDate = Carbon::create($cutoffYear, 8, 31)->startOfDay();

        // Calculate the age as of the cutoff date using the 'diff' method
        $age = $dob->diff($cutoffDate)->y;

        // Return the age group as 'U' followed by the calculated age
        return 'U' . $age + 1;
    }
}
