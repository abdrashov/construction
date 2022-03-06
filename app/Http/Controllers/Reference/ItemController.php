<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Measurement;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index()
    {
        return Inertia::render('Reference/Items/Index', [
            'filters' => Request::all('search', 'trashed'),
            'items' => Item::orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->with('measurement')
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'measurement' => $item->measurement ? $item->measurement->name : 'Удален!',
                ]),
            'measurements' => Measurement::get(),
        ]);
    }

    public function store()
    {
        Item::create(
            Request::validate([
                'name' => ['required', 'max:255'],
                'measurement_id' => ['required', 'max:255'],
            ])
        );

        return Redirect::route('reference.items')->with('success', 'Создано.');
    }

    public function edit(Item $item)
    {
        return Inertia::render('Reference/Items/Edit', [
            'item' => [
                'id' => $item->id,
                'name' => $item->name,
                'measurement_id' => $item->measurement_id,
                'deleted_at' => $item->deleted_at,
            ],
            'measurements' => Measurement::get(),
        ]);
    }

    public function update(Item $item)
    {
        $item->update(
            Request::validate([
                'name' => ['required', 'max:255'],
                'measurement_id' => ['required', 'max:255'],
            ])
        );

        return Redirect::back()->with('success', 'Обновлено.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return Redirect::back()->with('success', 'Удален.');
    }

    public function restore(Item $item)
    {
        $item->restore();

        return Redirect::back()->with('success', 'Восстановлен.');
    }
}
