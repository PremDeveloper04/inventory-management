<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialRequest;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MaterialController extends Controller
{
    public function index(): Response
    {
        $materials = Material::paginate(20);
        return Inertia::render('Materials/Index', compact('materials'));
    }

    public function create(): Response
    {
        return Inertia::render('Materials/Create');
    }

    public function store(MaterialRequest $request): RedirectResponse
    {
        Material::create($request->validated());
        return redirect()->route('materials.index');
    }

    public function edit(Material $material): Response
    {
        return Inertia::render('Materials/Edit', compact('material'));
    }

    public function update(MaterialRequest $request, Material $material): RedirectResponse
    {
        $material->update($request->validated());
        return redirect()->route('materials.index');
    }

    public function destroy(Material $material): RedirectResponse
    {
        $material->delete();
        return redirect()->route('materials.index');
    }
}
