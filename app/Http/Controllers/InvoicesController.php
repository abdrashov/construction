<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Organization;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class InvoicesController extends Controller
{
    public function Index(Organization $organization)
    {
        return Inertia::render('Invoices/Index', [
            'filters' => Request::all('name', 'date', 'supplier_id', 'accepted', 'status', 'pay', 'trashed'),
            'suppliers' => Supplier::orderBy('name')->get(),
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'address' => $organization->address,
                'users' => $organization->users,
                'deleted_at' => $organization->deleted_at,
                'invoices' => $organization->invoices()
                    ->filter(Request::only('name', 'date', 'supplier_id', 'accepted', 'status', 'pay', 'trashed'))
                    ->orderByDesc('date')
                    ->with('user')
                    ->withCount([
                        'invoiceItems' => function ($query) {
                            $query->select(DB::raw('SUM(count * price) as sum'));
                        }
                    ])
                    ->paginate(10)
                    ->withQueryString()
                    ->through(fn ($invoice) => [
                        'id' => $invoice->id,
                        'name' => $invoice->name,
                        'status' => $invoice->status,
                        'pay' => $invoice->pay,
                        'deleted_at' => $invoice->deleted_at,
                        'sum' => $invoice->invoice_items_count / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
                        'date' => $invoice->date->format('d.m.Y'),
                        'fullname' => $invoice->user->last_name . ' ' .  $invoice->user->first_name,
                        'created' => $invoice->created_at->format('H:i d.m.Y'),
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
        if (!is_null(Request::input('date'))) Request::merge(['date' => (new Carbon(Request::input('date')))->format('Y-m-d')]);

        Request::validate([
            'name' => ['required', 'max:255'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'supplier_id' => ['required', 'max:255'],
            'accepted' => ['required', 'max:255'],
            'file' => ['nullable'],
        ]);

        $supplier = Supplier::findOrFail(Request::input('supplier_id'));

        Request::merge(['supplier' => $supplier->name, 'user_id' => Auth::id()]);

        $invoice = $organization->invoices()
            ->where('name', Request::input('name'))
            ->where('supplier_id', Request::input('supplier_id'))
            ->where('date', Request::input('date'))
            ->first();

        if ($invoice) {
            return Redirect::route('invoice-items', $invoice->id)->with('error', '?????????????????? ?????? ????????????????????...');
        }

        $invoice = $organization->invoices()
            ->create(Request::only('name', 'date', 'supplier_id', 'accepted', 'supplier', 'user_id'));

        if (Request::file('file')) {
            $invoice->update(['file' => Request::file('file')->store('invoices')]);
        }

        return Redirect::route('invoice-items', $invoice->id)->with('success', '??????????????????, ??????????????.');
    }

    public function update(Organization $organization, Invoice $invoice)
    {
        if (!is_null(Request::input('date'))) Request::merge(['date' => (new Carbon(Request::input('date')))->format('Y-m-d')]);

        Request::validate([
            'name' => ['required', 'max:255'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
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

        return Redirect::back()->with('success', '??????????????????, ??????????????????.');
    }

    public function destroy(Organization $organization, Invoice $invoice)
    {
        $invoice->invoiceItems()->delete();
        $invoice->delete();

        return Redirect::back()->with('success', '??????????????????, ????????????.');
    }
}
