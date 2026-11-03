<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class Customer extends Model
{
    protected $fillable = [ // Fillable attributes for mass assignment
        'name',
        'email',
        'phone',
        'address',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class); // Define the relationship with the Order model
    }
}
