<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'address',
        'payment_method',
        'total_price',
        'discount',
        'status',
    ];

    // public function details()
    // {
    //     return $this->hasMany(OrderDetail::class, 'order_id');
    // }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
