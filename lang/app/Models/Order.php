<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'medicine', 'quantity', 'price', 'phone_number', 'payment_status', 'prepare_status'];

    public function scopeFilter($query, array $filters)
    {
        if ($filters['name'] ?? false) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }
    }

    public function Medicine()
    {
        return $this->belongsToMany(Medicine::class);
    }
    public function Pharmasist()
    {
        return $this->belongsTo(User::class);
    }

}