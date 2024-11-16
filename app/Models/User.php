<?php

namespace App\Models;

 use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


     public function favorites/* favoriteBooks*/()
{
  return $this->hasMany(Favorite::class);
}
public function notes()
{
    return $this->hasMany(Note::class);
}

public function readingSessions()
{
    return $this->hasMany(ReadingTime::class);
}
public function Rate(){
    return $this->hasMany(Rate::class);
}
public function question(){
    return $this->hasMany(Question::class);
}
public function SuggestionOrComplaint(){
    return $this->hasMany(SuggeorComp::class);
}
}
