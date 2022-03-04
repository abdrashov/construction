<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InvoiceItemsController extends Controller
{
    public function Index(Invoice $invoice)
    {
        $item_ids = array_map(
            fn($item_id) => $item_id['item_id'],
            $invoice->invoiceItems()->select('id', 'item_id')->get()->toArray()
        );

        if (!$invoice->status) {
            return Inertia::render('InvoiceItems/Index', [
                'filters' => Request::only('search', 'page'),
                'organization' => [
                    'id' => $invoice->organization->id,
                    'name' => $invoice->organization->name,
                    'users' => $invoice->organization->users,
                ],
                'invoice' => [
                    'id' => $invoice->id,
                    'name' => $invoice->name,
                    'status' => $invoice->status,
                    'date' => $invoice->date->format('Y-m-d'),
                    'supplier_id' => $invoice->supplier_id,
                    'accepted' => $invoice->accepted,
                    'file' => $invoice->file ? '\\file\\'.$invoice->file : '',
                ],
                'invoice_items' => $invoice->invoiceItems->transform(fn($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'count' => $item->count,
                    'price' => $item->price,
                    'measurement' => $item->measurement,
                ]),
                'items' => Item::filter(Request::only('search'))
                    ->whereNotIn('id', $item_ids)
                    ->paginate(10)
                    ->withQueryString()
                    ->through(fn($item) => [
                        'id' => $item->id,
                        'name' => $item->name,
                        'measurement' => $item->measurement ? $item->measurement->name : 'Удален!',
                    ]),
                'suppliers' => Supplier::orderBy('name')->get(),
            ]);
        }

        return Inertia::render('InvoiceItems/Show', [
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
                'file' => $invoice->file ? '\\file\\'.$invoice->file : '',
            ],
            'invoice_items' => $invoice->invoiceItems->transform(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'count' => $item->count,
                'price' => $item->price,
                'measurement' => $item->measurement,
            ]),
        ]);
    }

    public function store(Invoice $invoice, Item $item)
    {
        if ($invoice->invoiceItems()->where('item_id', $item->id)->exists()) {
            return Redirect::back()->with('error', 'Товар уже добавлено.');
        }

        $invoice->invoiceItems()->create([
            'name' => $item->name,
            'item_id' => $item->id,
            'measurement' => $item->measurement ? $item->measurement->name : 'Удален!',
            'measurement_id' => $item->measurement_id,
        ]);

        $item->update(['sort' => $item->sort+1]);

        return Redirect::back()->with('success', 'Товар, добавлено.');
    }

    public function update(Invoice $invoice)
    {
        Request::validate([
            'items.*.id' => ['required'],
            'items.*.price' => ['nullable', 'numeric', 'min:0', 'max:4294967295'],
            'items.*.count' => ['nullable', 'numeric', 'min:0', 'max:4294967295'],
        ]);

        foreach (Request::input('items') as $item) {
            InvoiceItem::findOrFail($item['id'])->update([
                'price' => $item['price'],
                'count' => $item['count'],
            ]);
        }

        return Redirect::route('invoice-items', $invoice->id)->with('success', 'Успешно сохранено.');
    }

    public function confirm(Invoice $invoice)
    {
        Request::validate([
            'items.*.id' => ['required'],
            'items.*.price' => ['nullable', 'numeric', 'min:0', 'max:4294967295'],
            'items.*.count' => ['nullable', 'numeric', 'min:0', 'max:4294967295'],
        ]);

        foreach (Request::input('items') as $item) {
            InvoiceItem::findOrFail($item['id'])->update([
                'price' => $item['price'],
                'count' => $item['count'],
            ]);
        }

        $invoice->update([
            'status' => true,
        ]);

        return Redirect::route('invoices', $invoice->organization_id)->with('success', 'Успешно подтвержден.');
    }

    public function delete(Invoice $invoice)
    {
        Request::validate([
            'item_id' => ['required', 'max:255'],
        ]);

        $invoice->invoiceItems()->where('id', Request::input('item_id'))->delete();

        return Redirect::back()->with('success', 'Товар, удален.');
    }
}
