<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipApplication extends Model
{
    use HasFactory;

      protected $fillable = [
        'user_id',
        'academic_stage',
        'school_name',
        'field_of_study',
        'academic_year',
        'average',
        'placement_test',
        'language_level',
        'status',
        'scholarship_id',
    ];

        // علاقة بالطالب
    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

}
