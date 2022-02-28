<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class OrganizationsController extends Controller
{
    public function index()
    {
        return Inertia::render('Organizations/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organizations' => Organization::orderByDesc('id')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($organization) => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'address' => $organization->address,
                    'phone' => $organization->phone,
                    'created_at' => $organization->created_at->format('H:i d.m.Y'),
                    'deleted_at' => $organization->deleted_at,
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Organizations/Create');
    }

    public function store()
    {
        Organization::create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
            ])
        );

        return Redirect::route('organizations')->with('success', 'Организация, созданная.');
    }

    public function edit(Organization $organization)
    {
        return Inertia::render('Organizations/Edit', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'phone' => $organization->phone,
                'address' => $organization->address,
                'deleted_at' => $organization->deleted_at,
                'contacts' => collect([])
            ],
        ]);
    }

    public function update(Organization $organization)
    {
        $organization->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
            ])
        );

        return Redirect::back()->with('success', 'Организация обновлено.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return Redirect::back()->with('success', 'Организация удалена.');
    }

    public function restore(Organization $organization)
    {
        $organization->restore();

        return Redirect::back()->with('success', 'Организация восстановлена.');
    }
}
