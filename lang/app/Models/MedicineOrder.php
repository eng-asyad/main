<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineOrder extends Model
{
    use HasFactory;

    protected $fillable = ['medicine_id', 'order_id'];

    public function orders()
    {
        return $this->hasMany(Medicine::class);
    }
    public function medicines()
    {
        return $this->hasMany(Order::class);
    }
}