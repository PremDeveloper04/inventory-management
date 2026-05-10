<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportFile extends Model
{
    protected $fillable = [
        'export_id',
        'file_name',
        'part_number',
        'records_count',
        'status'
    ];

    public function export()
    {
        return $this->belongsTo(Export::class);
    }
}