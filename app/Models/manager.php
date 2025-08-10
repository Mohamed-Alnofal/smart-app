<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class manager extends Model
{
    use HasFactory;
        //have many

    protected $fillable = ['user_id' , 'department'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
