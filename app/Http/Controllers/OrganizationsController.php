<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class OrganizationsController extends Controller
{
    public function index()
    {
        return Inertia::render('Organizations/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organizations' => Organization::filter(Request::only('search', 'trashed'))
                ->paginate(20)
                ->withQueryString()
                ->through(fn ($organization) => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'address' => $organization->address,
                    'created_at' => $organization->created_at->format('d.m.Y'),
                    'deleted_at' => $organization->deleted_at ? $organization->deleted_at->format('d.m.Y') : ''
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Organizations/Create');
    }

    public function store()
    {
        $request = Request::validate([
            'name' => ['required', 'max:255'],
            'address' => ['nullable', 'max:150'],
            'users.*.lastname' => ['required', 'max:150'],
            'users.*.firstname' => ['required', 'max:150'],
            'users.*.middlename' => ['nullable', 'max:150'],
            'users.*.default' => ['boolean'],
        ]);

        Organization::create($request);

        return Redirect::route('organizations')->with('success', 'Объект, создано.');
    }

    public function edit(Organization $organization)
    {
        return Inertia::render('Organizations/Edit', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'address' => $organization->address,
                'users' => $organization->users,
                'deleted_at' => $organization->deleted_at,
            ],
        ]);
    }

    public function update(Organization $organization)
    {
        $organization->update(
            Request::validate([
                'name' => ['required', 'max:255'],
                'address' => ['nullable', 'max:150'],
                'users.*.lastname' => ['required', 'max:150'],
                'users.*.firstname' => ['required', 'max:150'],
                'users.*.middlename' => ['nullable', 'max:150'],
                'users.*.default' => ['boolean'],
            ])
        );

        return Redirect::back()->with('success', 'Объект обновлено.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return Redirect::back()->with('success', 'Объект удалена.');
    }

    public function restore(Organization $organization)
    {
        $organization->restore();

        return Redirect::back()->with('success', 'Объект восстановлена.');
    }
}
