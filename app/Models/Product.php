<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
        'is_seasonal',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}