<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class InvoiceItemsController extends Controller
{
    public function Index(Invoice $invoice)
    {
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
                    'file' => $invoice->file ? '/file/' . $invoice->file : '',
                    'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum') / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
                ],
                'invoice_items' => $invoice->invoiceItems->transform(fn($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'count' => $item->count / InvoiceItem::FLOAT_TO_INT_COUNT,
                    'price' => $item->price / InvoiceItem::FLOAT_TO_INT_PRICE,
                    'measurement' => $item->measurement,
                ]),
                'items' => Item::filter(Request::only('search'))
                    ->with('measurement')
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
                'pay' => $invoice->pay,
                'date' => $invoice->date->format('d.m.Y'),
                'supplier' => $invoice->supplier,
                'accepted' => $invoice->accepted,
                'file' => $invoice->file ? '\\file\\' . $invoice->file : '',
                'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum') / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            ],
            'invoice_items' => $invoice->invoiceItems->transform(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'count' => $item->count / InvoiceItem::FLOAT_TO_INT_COUNT,
                'price' => $item->price / InvoiceItem::FLOAT_TO_INT_PRICE,
                'measurement' => $item->measurement,
            ]),
        ]);
    }

    public function store(Invoice $invoice, Item $item)
    {
        $invoice->invoiceItems()->create([
            'name' => $item->name,
            'item_id' => $item->id,
            'measurement' => $item->measurement ? $item->measurement->name : 'Удален!',
            'measurement_id' => $item->measurement_id,
        ]);

        $item->update(['sort' => $item->sort + 1]);

        return Redirect::back()->with('success', 'Товар, добавлено.');
    }

    public function update(Invoice $invoice)
    {
        Request::validate([
            'items.*.id' => ['required'],
            'items.*.price' => ['nullable', 'numeric', 'min:0', 'max:5000000000'],
            'items.*.count' => ['nullable', 'numeric', 'min:0', 'max:5000000000'],
        ]);


        foreach (Request::input('items') as $item) {
            InvoiceItem::findOrFail($item['id'])->update([
                'price' => $item['price'] * InvoiceItem::FLOAT_TO_INT_PRICE,
                'count' => $item['count'] * InvoiceItem::FLOAT_TO_INT_COUNT,
            ]);
        }

        return Redirect::route('invoice-items', $invoice->id)->with('success', 'Успешно сохранено.');
    }

    public function confirm(Invoice $invoice)
    {
        Request::validate([
            'items.*.id' => ['required'],
            'items.*.price' => ['nullable', 'numeric', 'min:0', 'max:5000000000'],
            'items.*.count' => ['nullable', 'numeric', 'min:0', 'max:5000000000'],
        ]);

        foreach (Request::input('items') as $item) {
            InvoiceItem::findOrFail($item['id'])->update([
                'price' => $item['price'] * InvoiceItem::FLOAT_TO_INT_PRICE,
                'count' => $item['count'] * InvoiceItem::FLOAT_TO_INT_COUNT,
            ]);
        }

        $invoice->update([
            'status' => true,
        ]);

        return Redirect::route('invoice-items', $invoice->id)->with('success', 'Успешно подтвержден.');
    }

    public function delete(Invoice $invoice)
    {
        Request::validate([
            'item_id' => ['required', 'max:255'],
        ]);

        $invoice->invoiceItems()->where('id', Request::input('item_id'))->delete();

        return Redirect::back()->with('success', 'Товар, удален.');
    }

    public function pay(Invoice $invoice)
    {
        $invoice->update([
            'pay' => true
        ]);

        return Redirect::route('invoices', $invoice->organization_id)->with('success', 'Успешно сохранено.');
    }
}
