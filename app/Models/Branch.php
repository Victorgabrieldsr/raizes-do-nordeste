<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'city',
        'state',
        'address',
        'is_active',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}