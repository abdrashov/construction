<?php

namespace App\Http\Controllers;

use App\Models\Accepted;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\Supplier;
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
                'users' => $organization->users,
                'deleted_at' => $organization->deleted_at,
                'invoices' => $organization->invoices()->orderByDesc('date')->get()->transform(fn ($invoice) => [
                    'id' => $invoice->id,
                    'name' => $invoice->name,
                    'status' => $invoice->status,
                    'date' => $invoice->date->format('Y-m-d'),
                    'supplier' => $invoice->supplier,
                    'accepted' => $invoice->accepted,
                ]),
            ],
        ]);
    }

    public function create(Organization $organization)
    {
        return Inertia::render('Invoices/Create', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'users' => $organization->users,
            ],
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(Organization $organization)
    {
        Request::validate([
            'name' => ['required', 'max:255'],
            'date' => ['required', 'date'],
            'supplier_id' => ['required', 'max:255'],
            'accepted' => ['required', 'max:255'],
            'file' => ['nullable'],
        ]);

        $supplier = Supplier::findOrFail(Request::input('supplier_id'));

        Request::merge(['supplier' => $supplier->name]);

        $invoice = $organization->invoices()->create(Request::only('name', 'date', 'supplier_id', 'accepted', 'supplier'));

        if (Request::file('file')) {
            $invoice->update(['file' => Request::file('file')->store('invoices')]);
        }

        return Redirect::route('invoice-items', $invoice->id)->with('success', 'Накладной, создано.');
    }

    public function update(Organization $organization, Invoice $invoice)
    {
        Request::validate([
            'name' => ['required', 'max:255'],
            'date' => ['required', 'date'],
            'supplier_id' => ['required', 'max:255'],
            'accepted' => ['required', 'max:255'],
            'file' => ['nullable'],
        ]);

        $supplier = Supplier::findOrFail(Request::input('supplier_id'));

        Request::merge(['supplier' => $supplier->name]);

        $invoice->update(Request::only('name', 'date', 'supplier_id', 'supplier', 'accepted'));

        if (Request::file('file')) {
            $invoice->update(['file' => Request::file('file')->store('invoices')]);
        }

        return Redirect::back()->with('success', 'Накладной, обновлено.');
    }
}
