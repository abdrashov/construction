<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use App\Models\Accepted;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class AcceptedController extends Controller
{
    public function index()
    {
        return Inertia::render('Reference/Accepted/Index', [
            'filters' => Request::all('search', 'trashed'),
            'accepteds' => Accepted::filter(Request::only('search', 'trashed'))->paginate(10),
        ]);
    }

    public function store()
    {
        Accepted::create(
            Request::validate([
                'firstname' => ['required', 'max:255'],
                'lastname' => ['required', 'max:255'],
                'middlename' => ['nullable', 'max:255'],
            ])
        );

        return Redirect::route('reference.accepted')->with('success', 'Создано.');
    }

    public function edit(Accepted $accepted)
    {
        return Inertia::render('Reference/Accepted/Edit', [
            'accepted' => [
                'id' => $accepted->id,
                'lastname' => $accepted->lastname,
                'firstname' => $accepted->firstname,
                'middlename' => $accepted->middlename,
                'deleted_at' => $accepted->deleted_at,
            ],
        ]);
    }

    public function update(Accepted $accepted)
    {
        $accepted->update(
            Request::validate([
                'firstname' => ['required', 'max:255'],
                'lastname' => ['required', 'max:255'],
                'middlename' => ['nullable', 'max:255'],
            ])
        );

        return Redirect::back()->with('success', 'Обновлено.');
    }

    public function destroy(Accepted $accepted)
    {
        $accepted->delete();

        return Redirect::back()->with('success', 'Удален.');
    }

    public function restore(Accepted $accepted)
    {
        $accepted->restore();

        return Redirect::back()->with('success', 'Восстановлен.');
    }
}
