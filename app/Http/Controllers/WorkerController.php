<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkerRequest;
use App\Models\Worker;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Export;
use App\Jobs\GenerateWorkerExport;

class WorkerController extends Controller
{
    // public function index(): Response
    // {
    //     $workers = Worker::paginate(20);
    //     return Inertia::render('Workers/Index', compact('workers'));
    // }

    // public function index(Request $request)
    // {
    //     $query = Worker::query();

    //     // Filters
    //     if ($request->filled('name')) {
    //         $query->where('name', 'like', $request->name . '%');
    //     }

    //     if ($request->filled('email')) {
    //         $query->where('email', $request->email);
    //     }

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->filled('city')) {
    //         $query->where('city', $request->city);
    //     }

    //     if ($request->filled('from_date')) {
    //         $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
    //     }

    //     if ($request->filled('to_date')) {
    //         $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
    //     }

    //     // Pagination (optimized)
    //     $workers = $query
    //         ->select('name', 'email', 'status', 'city', 'created_at')
    //         ->orderBy('id', 'desc')
    //         ->simplePaginate(50)
    //         ->withQueryString();

    //     return view('workers.index', compact('workers'));
    // }

    public function index(Request $request)
    {
        $query = Worker::query();

        // Filters
        if ($request->filled('name')) {
            $query->where('name', 'like', $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', $request->email);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        // 🔥 Build cache key (VERY IMPORTANT)
        $page = $request->get('page', 1);

        $cacheKey = 'workers:' . md5(json_encode([
            'filters' => $request->except('page'),
            'page' => $page
        ]));

        // 🔥 Cache result
        $workers = Cache::remember($cacheKey, now()->addSeconds(60), function () use ($query) {
            return $query
                ->select('name', 'email', 'status', 'city', 'created_at')
                ->orderBy('id', 'desc')
                ->simplePaginate(50)
                ->withQueryString();
        });

        return view('workers.index', compact('workers'));
    }

    // understand query
    // cache
    // more about indexing and query optimization
    // import and export

    public function create(): Response
    {
        return Inertia::render('Workers/Create');
    }

    public function store(WorkerRequest $request): RedirectResponse
    {
        Worker::create($request->validated());
        return redirect()->route('workers.index');
    }

    public function edit(Worker $worker): Response
    {
        return Inertia::render('Workers/Edit', compact('worker'));
    }

    public function update(WorkerRequest $request, Worker $worker): RedirectResponse
    {
        $worker->update($request->validated());
        return redirect()->route('workers.index');
    }

    public function destroy(Worker $worker): RedirectResponse
    {
        $worker->delete();
        return redirect()->route('workers.index');
    }

    // csv export
    // public function export(Request $request)
    // {
    //     // Validation
    //     $request->validate([
    //         'from_date' => 'required|date',
    //         'to_date' => 'required|date|after_or_equal:from_date',
    //     ]);

    //     $query = Worker::query();

    //     if ($request->status) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->city) {
    //         $query->where('city', $request->city);
    //     }

    //     $query->whereBetween('created_at', [
    //         $request->from_date . ' 00:00:00',
    //         $request->to_date . ' 23:59:59'
    //     ]);

    //     $count = $query->count();

    //     // Limit check
    //     // if ($count > 10000) {
    //     //     return back()->with('error', 'Max 10,000 records allowed');
    //     // }

    //     // 🔥 STREAM DOWNLOAD
    //     return response()->stream(function () use ($query) {

    //         $handle = fopen('php://output', 'w');

    //         // Header
    //         fputcsv($handle, [
    //             'Name','Email','Phone','City','State','Country',
    //             'Status','Experience','Salary','Joined At','Created At'
    //         ]);

    //         $query->chunk(1000, function ($workers) use ($handle) {
    //             foreach ($workers as $worker) {
    //                 fputcsv($handle, [
    //                     $worker->name,
    //                     $worker->email,
    //                     $worker->phone,
    //                     $worker->city,
    //                     $worker->state,
    //                     $worker->country,
    //                     $worker->status,
    //                     $worker->experience,
    //                     $worker->salary,
    //                     $worker->joined_at,
    //                     $worker->created_at
    //                 ]);
    //             }
    //         });

    //         fclose($handle);

    //     }, 200, [
    //         "Content-Type" => "text/csv",
    //         "Content-Disposition" => "attachment; filename=workers.csv",
    //     ]);
    // }

    // excel export small
    // public function export(Request $request)
    // {
    //     // ✅ Validation
    //     $request->validate([
    //         'from_date' => 'required|date',
    //         'to_date' => 'required|date|after_or_equal:from_date',
    //     ]);

    //     $query = Worker::query();

    //     // ✅ Filters (same as UI)
    //     if ($request->filled('name')) {
    //         $query->where('name', 'like', $request->name . '%');
    //     }

    //     if ($request->filled('email')) {
    //         $query->where('email', $request->email);
    //     }

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->filled('city')) {
    //         $query->where('city', $request->city);
    //     }

    //     if ($request->filled('from_date') && $request->filled('to_date')) {
    //         $query->whereBetween('created_at', [
    //             $request->from_date . ' 00:00:00',
    //             $request->to_date . ' 23:59:59'
    //         ]);
    //     }

    //     // ✅ Limit check (IMPORTANT)
    //     $total = $query->count();

    //     if ($total > 10000) {
    //         return back()->with('error', 'Max 10,000 records allowed');
    //     }

    //     // ✅ Create Excel
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     // ✅ Header
    //     $headers = [
    //         'Name','Email','Phone','City','State','Country',
    //         'Status','Experience','Salary','Joined At','Created At'
    //     ];

    //     $sheet->fromArray([$headers], null, 'A1');

    //     // 🔥 Style header (professional look)
    //     $sheet->getStyle('A1:K1')->getFont()->setBold(true);

    //     // 🔥 Auto column width
    //     foreach (range('A', 'K') as $col) {
    //         $sheet->getColumnDimension($col)->setAutoSize(true);
    //     }

    //     // ✅ Insert data using chunk (memory safe)
    //     $row = 2;

    //     $query->orderBy('id')->chunk(1000, function ($workers) use (&$sheet, &$row) {
    //         foreach ($workers as $worker) {
    //             $sheet->setCellValue("A$row", $worker->name);
    //             $sheet->setCellValue("B$row", $worker->email);
    //             $sheet->setCellValue("C$row", $worker->phone);
    //             $sheet->setCellValue("D$row", $worker->city);
    //             $sheet->setCellValue("E$row", $worker->state);
    //             $sheet->setCellValue("F$row", $worker->country);
    //             $sheet->setCellValue("G$row", $worker->status);
    //             $sheet->setCellValue("H$row", $worker->experience);
    //             $sheet->setCellValue("I$row", $worker->salary);
    //             $sheet->setCellValue("J$row", $worker->joined_at);
    //             $sheet->setCellValue("K$row", $worker->created_at);
    //             $row++;
    //         }
    //     });

    //     // ✅ Download
    //     $writer = new Xlsx($spreadsheet);

    //     return response()->streamDownload(function () use ($writer) {
    //         $writer->save('php://output');
    //     }, 'workers.xlsx');
    // }

    public function startExport(Request $request)
    {
        $export = Export::create([
            'filters' => json_encode($request->all()),
            'status' => 'pending'
        ]);

        GenerateWorkerExport::dispatch($export->id)->onQueue('exports');

        return response()->json([
            'export_id' => $export->id
        ]);
    }

    public function checkExport($id)
    {
        return Export::find($id);
    }

    public function downloadExport($id)
    {
        $export = Export::findOrFail($id);

        if ($export->status !== 'completed') {
            abort(404);
        }

        return response()->download(storage_path('app/public/'.$export->file_name));
    }
}
