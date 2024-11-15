<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use App\Models\User;
use App\Models\Book;
use App\Models\Replay;


class Question extends Model
{
    use Notifiable , HasApiTokens, HasFactory ;

    public $fillable = ['user_id', 'book_id','question'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    
    public function replay(){
        return $this->hasMany(Replay::class);
    }
}
