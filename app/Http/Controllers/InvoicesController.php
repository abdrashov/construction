<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Organization;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class InvoicesController extends Controller
{
    public function create(Organization $organization)
    {
        return Inertia::render('Invoices/Create', [
            'organization' => $organization,
        ]);
    }

    public function store(Organization $organization)
    {
        $organization->invoices()->create(
            Request::validate([
                'name' => ['required', 'max:255'],
                'date' => ['required'],
                'supplier' => ['required', 'max:255'],
                'accepted' => ['required', 'max:255'],
            ])
        );

        return Redirect::route('organizations.show', $organization->id)->with('success', 'Накладной, создано.');
    }

    public function show(Organization $organization, Invoice $invoice)
    {
        return Inertia::render('Invoices/Show', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'invoice' => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'status' => $invoice->status,
                'date' => $invoice->date->format('d.m.Y'),
                'supplier' => $invoice->supplier,
                'accepted' => $invoice->accepted
            ],
        ]);
    }
}
