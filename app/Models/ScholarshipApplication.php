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
    ];

        // علاقة بالطالب
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
