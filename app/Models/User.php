<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

// use Laravel\Sanctum\HasApiTokens;
 
 
class User extends Authenticatable
{
    // use HasApiTokens, HasFactory, Notifiable;
        use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];
protected $fillable = [
    'email',
    'password',
    'first_name',
    'last_name',
    'phone_number',
    'gender',
    'age',
    'role_id',
];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

//belongsto
 public function student()
{
    return $this->hasMany(Student::class);
}

public function admin()
{
    return $this->hasOne(Admin::class);
}

public function manager()
{
    return $this->hasMany(Manager::class);
}
public function role()
{
    return $this->belongsTo(Role::class);
}

public function courses()
{
    return $this->hasMany(Course::class);
}

public function enrollments()
{
    return $this->hasMany(Enrollment::class);
}
// public function sendPasswordResetNotification($token)
// {
//     $this->notify(new ResetPassword($token));
// }
}
