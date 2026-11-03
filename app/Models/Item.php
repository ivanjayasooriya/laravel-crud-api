<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;   

class Item extends Model
{
    protected $fillable = [ // Fillable attributes for mass assignment
        'name',
        'description',
        'price',
        'stock',
    ];

    protected $casts = [ // Cast attributes to specific types
        'price' => 'decimal:2',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items') // Define the many-to-many relationship with the Order model
                    ->withPivot('quantity', 'unit_price') // Include additional pivot table attributes
                    ->withTimestamps();
    }
}
