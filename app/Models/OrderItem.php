<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $guarded = ['id'];

    //relation with order
    public function order(){
        return $this->belongsTo(Order::class);
    }

    //relation with product
    public function product(){
        return $this->belongsTo(Product::class);
    }

}
