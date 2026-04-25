<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Slot extends Model
{
    protected $fillable = [
        'total_bricks',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Materials added to this slot.
     */
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'slot_material')
                    ->withPivot(['quantity', 'price', 'added_at'])
                    ->withTimestamps();
    }

    /**
     * Workers assigned to this slot.
     */
    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Worker::class, 'slot_worker')
                    ->withPivot(['start_time', 'end_time', 'amount'])
                    ->withTimestamps();
    }
}
