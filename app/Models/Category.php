<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

use App\Models\Book;


class Category extends Model
{
    use Notifiable , HasApiTokens, HasFactory ;

    public $fillable = ['category_name','category_name_en'];
    public $timestamps = false;


    public function books(){
        return $this->hasMany(Book::class,'category_id');
    }


}
