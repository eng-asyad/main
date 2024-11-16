<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    public $table = 'medicines';
    protected $fillable = ['name', 'scientific_name', 'classification', 'company', 'quantity', 'expiration_date', 'price'];

    public function scopeFilter($query, array $filters)
    {
        if ($filters['classification'] ?? false) {
            $query->where('classification', 'like', '%' . request('classification') . '%');
        }

        if ($filters['search'] ?? false) {
            $query->where('name', 'like', '%' . request('search') . '%')
                ->orWhere('classification', 'like', '%' . request('search') . '%');
        }
    }
    public function Order()
    {
        return $this->belongsToMany(Order::class);
    }
}