<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InvoiceItemsController extends Controller
{
    public function Index(Invoice $invoice)
    {
        return Inertia::render('InvoiceItems/Index', [
            'organization' => [
                'id' => $invoice->organization->id,
                'name' => $invoice->organization->name,
            ],
            'invoice' => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'status' => $invoice->status,
                'date' => $invoice->date->format('Y-m-d'),
                'supplier' => $invoice->supplier,
                'accepted' => $invoice->accepted,
                'file' => Storage::url($invoice->file),
                'items' => $invoice->invoiceItems->transform(fn($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'count' => $item->count,
                    'price' => $item->price,
                    'sum' => $item->count * $item->price,
                    'measurement' => $item->measurement,
                ]),
            ],
            'items' => Item::get(),
        ]);
    }

    public function store(Invoice $invoice)
    {
        Request::validate([
            'name' => ['required', 'max:255'],
            'measurement' => ['nullable'],
        ]);

        $invoice->invoiceItems()->create(Request::only('name', 'measurement'));

        return Redirect::back()->with('success', 'Товар, добавлено.');
    }

    public function update(Invoice $invoice, InvoiceItem $invoice_item)
    {
        Request::validate([
            'price' => ['nullable', 'numeric', 'min:0', 'max:4294967295'],
            'count' => ['nullable', 'numeric', 'min:0', 'max:4294967295'],
        ]);

        if (Request::has('price')) {
            $invoice_item->update(Request::only('price'));
        } elseif (Request::has('count')) {
            $invoice_item->update(Request::only('count'));
        }

        return Redirect::route('invoice-items', $invoice->id)->with('success', 'Успешно изменено.');
    }
}
