<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkerRequest;
use App\Models\Worker;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    // public function index(): Response
    // {
    //     $workers = Worker::paginate(20);
    //     return Inertia::render('Workers/Index', compact('workers'));
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

        // Pagination (optimized)
        $workers = $query
            ->select('name', 'email', 'status', 'city', 'created_at')
            ->orderBy('id', 'desc')
            ->simplePaginate(50)
            ->withQueryString();

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
}
