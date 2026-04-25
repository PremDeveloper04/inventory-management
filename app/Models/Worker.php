<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'city',
        'state',
        'status',
        'experience',
        'salary',
        'joined_at',
    ];

    public function slots(): BelongsToMany
    {
        return $this->belongsToMany(Slot::class, 'slot_worker')
                    ->withPivot(['start_time', 'end_time', 'amount'])
                    ->withTimestamps();
    }
}
