<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_url',
        'title',
        'description',
        'likes_count',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getScoreAttribute()
    {
        $created = Carbon::parse($this->created_at);
        $days = $created->diffInDays(now());

        $points = 0;
        if ($days < 3) {
            $points = 75;
        } elseif ($days < 7) {
            $points = 50;
        } elseif ($days < 30) {
            $points = 25;
        }

        return $points + $this->likes_count;
    }
}
