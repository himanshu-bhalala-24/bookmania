<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderBook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'book_id',
        'quantity',
        'price'
    ];

    public function getTotalAttribute()
    {
        return number_format(($this->quantity * $this->price), 2, '.', '');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class)->withTrashed();
    }
}
