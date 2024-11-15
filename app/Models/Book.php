<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Book extends Model
{
    use  HasFactory ;

    protected $fillable = [
        'book_name',
        'cover_image', 
        'abstract',
        'pdf',
        'author_id',
        'category_id'
    ];

        public function favorites/* favoriteBooks*/()
        {
            return $this->hasMany(Favorite::class);
}
public function notes()
{
    return $this->hasMany(Note::class);
}
public function category(){
    return $this->belongsTo(Category::class);
}
public function author(){
    return $this->belongsTo(Author::class);
}
public function readingSessions()
{
    return $this->hasMany(ReadingTime::class);
}
public function Rate(){
    return $this->belongsTo(Rate::class);
}
public function question(){
    return $this->belongsTo(Question::class);
}

}
