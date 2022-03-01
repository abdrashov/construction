<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InvoiceItemsController extends Controller
{
    public function Index(Invoice $invoice)
    {
        return Inertia::render('InvoiceItems/Index', [
            'invoice' => [
                'id' => $invoice->id,
                'organization_id' => $invoice->organization_id,
                'name' => $invoice->name,
                'status' => $invoice->status,
                'date' => $invoice->date->format('d.m.Y'),
                'supplier' => $invoice->supplier,
                'accepted' => $invoice->accepted,
                'file' => Storage::url($invoice->file),
                'items' => $invoice->invoiceItems->transform(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'count' => $item->count,
                    'price' => $item->price,
                    'sum' => $item->count * $item->price,
                    'measurement' => $item->measurement,
                ])
            ],
        ]);
    }

    public function store(Invoice $invoice)
    {
        Request::validate([
            'name' => ['required', 'max:255'],
            'count' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'measurement' => ['nullable'],
        ]);

        $invoice->invoiceItems()->create(Request::only('name', 'count', 'price', 'measurement'));

        return Redirect::route('invoice-items', $invoice->id)->with('success', 'Товар, добавлено.');
    }
}
