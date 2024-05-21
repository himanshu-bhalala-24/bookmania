<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('jS M, Y');
    }

    public function getTotalAttribute()
    {
        return number_format($this->orderBooks->sum(function ($orderBook) {
            return $orderBook->quantity * $orderBook->price;
        }), 2, '.', '');
    }

    public function orderBooks()
    {
        return $this->hasMany(OrderBook::class);
    }
}
