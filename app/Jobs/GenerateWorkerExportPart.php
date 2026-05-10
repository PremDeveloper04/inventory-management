<?php

namespace App\Jobs;

use App\Models\Export;
use App\Models\ExportFile;
use App\Models\Worker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable as FoundationQueueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateWorkerExportPart implements ShouldQueue
{
    use FoundationQueueable, InteractsWithQueue, Queueable, SerializesModels;

    public $exportId;
    public $offset;
    public $limit;
    public $part;

    public $timeout = 1200;
    public $tries = 1;

    public function __construct($exportId, $offset, $limit, $part)
    {
        $this->exportId = $exportId;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->part = $part;
    }

    public function handle()
    {
        $export = Export::find($this->exportId);

        if (!$export) {
            return;
        }

        $filters = $export->filters;

        $query = Worker::query();

        // Filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date'].' 00:00:00');
        }

        if (!empty($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date'].' 23:59:59');
        }

        // Create Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'ID','Name','Email','Phone','City','State','Country',
            'Status','Experience','Salary','Joined At','Created At','Updated At'
        ];

        $sheet->fromArray([$headers], null, 'A1');

        $row = 2;

        // 🔥 IMPORTANT:
        // Process data in chunks instead of loading all rows

        $query
            ->orderBy('id')
            ->offset($this->offset)
            ->limit($this->limit)
            ->chunk(1000, function ($workers) use (&$sheet, &$row, $export) {

                foreach ($workers as $worker) {

                    $sheet->fromArray([[
                        $worker->id,
                        $worker->name,
                        $worker->email,
                        $worker->phone,
                        $worker->city,
                        $worker->state,
                        $worker->country,
                        $worker->status,
                        $worker->experience,
                        $worker->salary,
                        $worker->joined_at,
                        $worker->created_at,
                        $worker->updated_at
                    ]], null, "A$row");

                    $row++;
                }

                // Progress update per chunk
                $export->increment(
                    'processed_records',
                    $workers->count()
                );
            });

        // Directory
        $directory = storage_path('app/public/exports');

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // File name
        $fileName = 'workers_export_'.$export->id.'_part_'.$this->part.'.xlsx';

        $fullPath = $directory.'/'.$fileName;

        // Save file
        $writer = new Xlsx($spreadsheet);
        $writer->save($fullPath);

        // 🔥 VERY IMPORTANT MEMORY CLEANUP
        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet);

        gc_collect_cycles();

        // Save DB record
        ExportFile::create([
            'export_id' => $export->id,
            'file_name' => 'exports/'.$fileName,
            'part_number' => $this->part,
            'records_count' => $this->limit,
            'status' => 'completed'
        ]);

        // Completed parts
        $export->increment('completed_parts');

        // Refresh latest DB state
        $export->refresh();

        // Final completion check
        if ($export->completed_parts >= $export->total_parts) {

            $export->update([
                'status' => 'completed'
            ]);
        }
    }
}