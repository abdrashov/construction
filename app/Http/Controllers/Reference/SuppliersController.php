<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SuppliersController extends Controller
{
    public function index()
    {
        return Inertia::render('Reference/Suppliers/Index', [
            'filters' => Request::all('search', 'trashed'),
            'suppliers' => Supplier::orderBy('name')
                ->filter(Request::only('search', 'trashed'))->paginate(10),
        ]);
    }

    public function store()
    {
        Supplier::create(
            Request::validate([
                'name' => ['required', 'max:255', Rule::unique('suppliers')],
            ])
        );

        return Redirect::route('reference.suppliers')->with('success', 'Создано.');
    }

    public function edit(Supplier $supplier)
    {
        return Inertia::render('Reference/Suppliers/Edit', [
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'deleted_at' => $supplier->deleted_at,
            ],
        ]);
    }

    public function update(Supplier $supplier)
    {
        $supplier->update(
            Request::validate([
                'name' => ['required', 'max:255', Rule::unique('suppliers')->ignore($supplier->id)],
            ])
        );

        return Redirect::back()->with('success', 'Обновлено.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return Redirect::back()->with('success', 'Удален.');
    }

    public function restore(Supplier $supplier)
    {
        $supplier->restore();

        return Redirect::back()->with('success', 'Восстановлен.');
    }
}
