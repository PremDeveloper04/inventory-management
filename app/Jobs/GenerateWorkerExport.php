<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Queueable as BusQueueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Export;
use App\Models\Worker;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateWorkerExport implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, BusQueueable;

    public $exportId;

    /**
     * Create a new job instance.
     */
    public function __construct($exportId)
    {
        $this->exportId = $exportId;
    }

    /**
     * Execute the job.
     */
    // public function handle()
    // {
    //     $export = Export::find($this->exportId);

    //     if (!$export) {
    //         return;
    //     }

    //     $export->update(['status' => 'processing']);

    //     $filters = json_decode($export->filters, true);

    //     $query = Worker::query();

    //     // Filters
    //     if (!empty($filters['status'])) {
    //         $query->where('status', $filters['status']);
    //     }

    //     if (!empty($filters['city'])) {
    //         $query->where('city', $filters['city']);
    //     }

    //     if (!empty($filters['from_date'])) {
    //         $query->where('created_at', '>=', $filters['from_date'].' 00:00:00');
    //     }

    //     if ($filters['to_date']) {
    //         $query->where('created_at', '<=', $filters['to_date'].' 23:59:59');
    //     }

    //     // Create Excel
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     // 🔥 FULL HEADER (ALL COLUMNS)
    //     $headers = [
    //         'ID','Name','Email','Phone','City','State','Country',
    //         'Status','Experience','Salary','Joined At','Created At','Updated At'
    //     ];

    //     $sheet->fromArray([$headers], null, 'A1');

    //     // Style header
    //     $sheet->getStyle('A1:M1')->getFont()->setBold(true);

    //     $row = 2;

    //     // 🔥 Chunking (important)
    //     $query->orderBy('id')->chunk(1000, function ($workers) use (&$sheet, &$row) {
    //         foreach ($workers as $worker) {
    //             $sheet->setCellValue("A$row", $worker->id);
    //             $sheet->setCellValue("B$row", $worker->name);
    //             $sheet->setCellValue("C$row", $worker->email);
    //             $sheet->setCellValue("D$row", $worker->phone);
    //             $sheet->setCellValue("E$row", $worker->city);
    //             $sheet->setCellValue("F$row", $worker->state);
    //             $sheet->setCellValue("G$row", $worker->country);
    //             $sheet->setCellValue("H$row", $worker->status);
    //             $sheet->setCellValue("I$row", $worker->experience);
    //             $sheet->setCellValue("J$row", $worker->salary);
    //             $sheet->setCellValue("K$row", $worker->joined_at);
    //             $sheet->setCellValue("L$row", $worker->created_at);
    //             $sheet->setCellValue("M$row", $worker->updated_at);
    //             $row++;
    //         }
    //     });

    //     // Create folder
    //     $directory = storage_path('app/public/exports');

    //     if (!file_exists($directory)) {
    //         mkdir($directory, 0777, true);
    //     }

    //     // File name
    //     $fileName = 'workers_'.$export->id.'.xlsx';

    //     // Full path
    //     $fullPath = $directory.'/'.$fileName;

    //     // Save file
    //     $writer = new Xlsx($spreadsheet);
    //     $writer->save($fullPath);

    //     // Save relative path (for download)
    //     $export->update([
    //         'file_name' => 'exports/'.$fileName,
    //         'status' => 'completed'
    //     ]);
    // }

    
    public function handle()
    {
        $export = Export::find($this->exportId);

        if (!$export) {
            return;
        }

        $export->update(['status' => 'processing']);

        $filters = json_decode($export->filters, true);

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

        // 🔥 STEP 1: COUNT RECORDS
        $totalRecords = $query->count();

        // Folder
        $directory = storage_path('app/public/exports');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Headers
        $headers = [
            'ID','Name','Email','Phone','City','State','Country',
            'Status','Experience','Salary','Joined At','Created At','Updated At'
        ];

        // 🔥 STEP 2: DECIDE FORMAT
        if ($totalRecords < 30000) {

            // =========================
            // ✅ EXCEL EXPORT
            // =========================

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->fromArray([$headers], null, 'A1');
            $sheet->getStyle('A1:M1')->getFont()->setBold(true);

            $row = 2;

            $query->orderBy('id')->chunk(1000, function ($workers) use (&$sheet, &$row) {
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
            });

            $fileName = 'workers_'.$export->id.'.xlsx';
            $fullPath = $directory.'/'.$fileName;

            $writer = new Xlsx($spreadsheet);
            $writer->save($fullPath);

        } else {

            // =========================
            // ✅ CSV EXPORT (STREAMING)
            // =========================

            $fileName = 'workers_'.$export->id.'.csv';
            $fullPath = $directory.'/'.$fileName;

            $file = fopen($fullPath, 'w');

            // Header
            fputcsv($file, $headers);

            $query->orderBy('id')->chunk(1000, function ($workers) use ($file) {
                foreach ($workers as $worker) {
                    fputcsv($file, [
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
                    ]);
                }
            });

            fclose($file);
        }

        // 🔥 FINAL UPDATE
        $export->update([
            'file_name' => 'exports/'.$fileName,
            'status' => 'completed'
        ]);
    }
    
}