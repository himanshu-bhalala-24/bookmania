<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'author'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderBooks()
    {
        return $this->hasMany(OrderBook::class);
    }
}
