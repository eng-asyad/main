<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use App\Models\User;

class SuggeorComp extends Model
{
    use Notifiable , HasApiTokens, HasFactory ;

    protected $fillable = [
        'user_id',
        'suggestionorcomplaint',            
    ];
    public $timestamps = false;

    public function user()
{
    return $this->belongsTo(User::class);
}


}
