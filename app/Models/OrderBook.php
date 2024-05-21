<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBook extends Model
{
    use HasFactory;

    public function getTotalAttribute()
    {
        return number_format(($this->quantity * $this->price), 2, '.', '');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
