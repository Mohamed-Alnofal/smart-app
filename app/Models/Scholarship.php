<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'type_of_financing',
        'funding_agency',
        'achieved_certificate',
        'required_documents',
        'advantages',
        'required_certificates',
        'university',
        'country',
        'specialization',
        'image',
    ];

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
