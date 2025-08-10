<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_name', 'course_details', 'certificate', 'image'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function levels()
    {
        return $this->hasMany(Level::class);
    }
}
