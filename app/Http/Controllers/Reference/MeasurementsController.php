<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use App\Models\Measurement;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class MeasurementsController extends Controller
{
    public function index()
    {
        return Inertia::render('Reference/Measurements/Index', [
            'filters' => Request::all('search', 'trashed'),
            'measurements' => Measurement::filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function store()
    {
        Measurement::create(
            Request::validate([
                'name' => ['required', 'max:255'],
            ])
        );

        return Redirect::route('reference.measurements')->with('success', 'Создано.');
    }

    public function edit(Measurement $measurement)
    {
        return Inertia::render('Reference/Measurements/Edit', [
            'measurement' => [
                'id' => $measurement->id,
                'name' => $measurement->name,
                'deleted_at' => $measurement->deleted_at,
            ],
        ]);
    }

    public function update(Measurement $measurement)
    {
        $measurement->update(
            Request::validate([
                'name' => ['required', 'max:255'],
            ])
        );

        return Redirect::back()->with('success', 'Обновлено.');
    }

    public function destroy(Measurement $measurement)
    {
        $measurement->delete();

        return Redirect::back()->with('success', 'Удален.');
    }

    public function restore(Measurement $measurement)
    {
        $measurement->restore();

        return Redirect::back()->with('success', 'Восстановлен.');
    }
}
