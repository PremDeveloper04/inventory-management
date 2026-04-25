<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    protected $fillable = [
        'name',
        'price',
    ];

    public function slots(): BelongsToMany
    {
        return $this->belongsToMany(Slot::class, 'slot_material')
                    ->withPivot(['quantity', 'price', 'added_at'])
                    ->withTimestamps();
    }
}
