<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use App\Models\User;
use App\Models\Book;

class Note extends Model
{
    use Notifiable , HasApiTokens, HasFactory ;
    
    protected $fillable = [
        'user_id',
        'book_id',
        'note',            
    ];
    public $timestamps = false;


    public function user()
{
    return $this->belongsTo(User::class);
}
public function book()
{
    return $this->belongsTo(Book::class);
}

}
