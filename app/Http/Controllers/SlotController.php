<?php

namespace App\Http\Controllers;

use App\Http\Requests\SlotRequest;
use App\Models\Material;
use App\Models\Slot;
use App\Models\Worker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SlotController extends Controller
{
    public function index(): Response
    {
        $slots = Slot::with(['materials', 'workers'])->paginate(15);
        return Inertia::render('Slots/Index', compact('slots'));
    }

    public function create(): Response
    {
        // need lists to populate selects
        $materials = Material::all();
        $workers = Worker::all();

        return Inertia::render('Slots/Create', compact('materials', 'workers'));
    }

    public function store(SlotRequest $request): RedirectResponse
    {
        $data = $request->only(['total_bricks', 'start_date', 'end_date', 'status']);
        $slot = Slot::create($data);

        if ($request->has('materials')) {
            foreach ($request->input('materials') as $mat) {
                $slot->materials()->attach($mat['id'], [
                    'quantity' => $mat['quantity'],
                    'price' => $mat['price'],
                    'added_at' => $mat['added_at'] ?? now()->toDateString(),
                ]);
            }
        }

        if ($request->has('workers')) {
            foreach ($request->input('workers') as $w) {
                $slot->workers()->attach($w['id'], [
                    'start_time' => $w['start_time'] ?? null,
                    'end_time' => $w['end_time'] ?? null,
                    'amount' => $w['amount'],
                ]);
            }
        }

        return redirect()->route('slots.index');
    }

    public function show(Slot $slot): Response
    {
        $slot->load(['materials', 'workers']);
        return Inertia::render('Slots/Show', compact('slot'));
    }

    public function edit(Slot $slot): Response
    {
        $materials = Material::all();
        $workers = Worker::all();
        $slot->load(['materials', 'workers']);
        return Inertia::render('Slots/Edit', compact('slot', 'materials', 'workers'));
    }

    public function update(SlotRequest $request, Slot $slot): RedirectResponse
    {
        $slot->update($request->only(['total_bricks', 'start_date', 'end_date', 'status']));

        // sync relationships
        if ($request->has('materials')) {
            $syncData = [];
            foreach ($request->input('materials') as $mat) {
                $syncData[$mat['id']] = [
                    'quantity' => $mat['quantity'],
                    'price' => $mat['price'],
                    'added_at' => $mat['added_at'] ?? now()->toDateString(),
                ];
            }
            $slot->materials()->sync($syncData);
        }

        if ($request->has('workers')) {
            $syncData = [];
            foreach ($request->input('workers') as $w) {
                $syncData[$w['id']] = [
                    'start_time' => $w['start_time'] ?? null,
                    'end_time' => $w['end_time'] ?? null,
                    'amount' => $w['amount'],
                ];
            }
            $slot->workers()->sync($syncData);
        }

        return redirect()->route('slots.show', $slot);
    }

    public function destroy(Slot $slot): RedirectResponse
    {
        $slot->delete();
        return redirect()->route('slots.index');
    }
}
