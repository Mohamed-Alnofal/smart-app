<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

protected $fillable = [
        'course_id',
        'name',
        'teacher',
        'seats_number',
        'status',
        'day',
        'start_time',
        'start_date',
        'description',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments()
{
    return $this->hasMany(Enrollment::class);
}
}
