<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $fillable = [
        'file_name',
        'filters',
        'status',
        'export_name',
        'total_records',
        'processed_records',
        'total_parts',
        'completed_parts',
    ];

    protected $casts = [
        'filters' => 'array'
    ];

    public function files()
    {
        return $this->hasMany(ExportFile::class);
    }
}
