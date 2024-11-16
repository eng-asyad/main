<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use App\Models\User;
use App\Models\Book;
use App\Models\Question;


class Replay extends Model
{
    use Notifiable , HasApiTokens, HasFactory ;
    public $fillable = ['user_id', 'question_id','replay'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
