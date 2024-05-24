<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'image',
        'name',
        'description',
        'price',
        'author',
        'quantity'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function orderBooks()
    {
        return $this->hasMany(OrderBook::class);
    }
}
