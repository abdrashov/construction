<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Organization;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class InvoicesController extends Controller
{
    public function Index(Organization $organization)
    {
        return Inertia::render('Invoices/Index', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'address' => $organization->address,
                'deleted_at' => $organization->deleted_at,
                'invoices' => $organization->invoices()->orderByDesc('date')->get()->transform(fn ($invoice) => [
                    'id' => $invoice->id,
                    'name' => $invoice->name,
                    'status' => $invoice->status,
                    'date' => $invoice->date->format('d.m.Y'),
                    'supplier' => $invoice->supplier,
                    'accepted' => $invoice->accepted
                ]),
            ],
        ]);
    }

    public function store(Organization $organization)
    {
        Request::validate([
            'name' => ['required', 'max:255'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'supplier' => ['required', 'max:255'],
            'accepted' => ['required', 'max:255'],
            'file' => ['nullable'],
        ]);

        $invoice = $organization->invoices()->create(Request::only('name', 'date', 'supplier', 'accepted'));

        if (Request::file('file')) {
            $invoice->update(['file' => Request::file('file')->store('invoices')]);
        }

        return Redirect::route('invoices', $organization->id)->with('success', 'Накладной, создано.');
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
