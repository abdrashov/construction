<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Organization;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function index()
    {
        if (!is_null(Request::input('begin'))) Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        if (!is_null(Request::input('end'))) Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);

        $reports = Supplier::filter(Request::only('search'))
            ->select(
                'organizations.id as id',
                'suppliers.id as supplier_id',
                'suppliers.name as supplier',
                DB::raw('SUM(invoice_items.price * invoice_items.count) as pay_sum'),
            )
            ->join('invoices', function ($query) {
                $query->on('suppliers.id', '=', 'invoices.supplier_id')
                    ->where('invoices.status', true)
                    ->where('invoices.pay', true)
                    ->when(Request::input('begin') ?? null, function ($query, $search) {
                        $query->where('invoices.date', '>=', $search);
                    })
                    ->when(Request::input('end') ?? null, function ($query, $search) {
                        $query->where('invoices.date', '<=', $search);
                    });
            })
            ->join('invoice_items', function ($query) {
                $query->on('invoices.id', '=', 'invoice_items.invoice_id');
            })
            ->join('organizations', function ($query) {
                $query->on('invoices.organization_id', '=', 'organizations.id')
                    ->where('organizations.id', Request::input('organization_id'))
                    ->whereNull('organizations.deleted_at');
            })
            ->groupBy('suppliers.id')
            ->get()
            ->transform(fn ($report) => [
                'id' => $report->id,
                'supplier_id' => $report->supplier_id,
                'supplier' => $report->supplier,
                'pay_sum' => $report->pay_sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
                'not_pay_sum' => $report->not_pay_sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            ]);

        $not_reports = Supplier::filter(Request::only('search'))
            ->select(
                'organizations.id as id',
                'suppliers.id as supplier_id',
                'suppliers.name as supplier',
                DB::raw('SUM(invoice_items.price * invoice_items.count) as not_pay_sum'),
            )
            ->join('invoices', function ($query) {
                $query->on('suppliers.id', '=', 'invoices.supplier_id')
                    ->where('invoices.status', true)
                    ->where('invoices.pay', false)
                    ->when(Request::input('begin') ?? null, function ($query, $search) {
                        $query->where('invoices.date', '>=', $search);
                    })
                    ->when(Request::input('end') ?? null, function ($query, $search) {
                        $query->where('invoices.date', '<=', $search);
                    });
            })
            ->join('invoice_items', function ($query) {
                $query->on('invoices.id', '=', 'invoice_items.invoice_id');
            })
            ->join('organizations', function ($query) {
                $query->on('invoices.organization_id', '=', 'organizations.id')
                    ->where('organizations.id', Request::input('organization_id'))
                    ->whereNull('organizations.deleted_at');
            })
            ->groupBy('suppliers.id')
            ->get()
            ->transform(fn ($report) => [
                'id' => $report->id,
                'supplier_id' => $report->supplier_id,
                'supplier' => $report->supplier,
                'pay_sum' => $report->pay_sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
                'not_pay_sum' => $report->not_pay_sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            ]);

        foreach ($reports as $key_1 => $report_1) {
            foreach ($not_reports as $key_2 => $report_2) {
                if ($report_1['supplier_id'] == $report_2['supplier_id']) {
                    $reports[$key_1] = [
                        'id' => $report_1['id'],
                        'supplier_id' => $report_1['supplier_id'],
                        'supplier' => $report_1['supplier'],
                        'pay_sum' => $report_1['pay_sum'] + $report_2['pay_sum'],
                        'not_pay_sum' => $report_1['not_pay_sum'] + $report_2['not_pay_sum'],
                    ];
                    unset($not_reports[$key_2]);
                }
            }
        }

        $reports_merge = collect();
        $sum_pay = 0;
        $not_sum_pay = 0;

        foreach ($reports as $report) {
            $reports_merge->push($report);
        }
        foreach ($not_reports as $not_report) {
            $reports_merge->push($not_report);
        }
        foreach ($reports_merge as $reports_mer) {
            $sum_pay += $reports_mer['pay_sum'];
            $not_sum_pay += $reports_mer['not_pay_sum'];
        }



        // $reports = Organization::filter(Request::only('search'))
        //     ->select(
        //         'organizations.id',
        //         'organizations.name',
        //         'organizations.created_at',
        //         DB::raw(
        //             '(SELECT COUNT(invoices.status) FROM `invoices` WHERE `organizations`.`id` = `invoices`.`organization_id` AND `invoices`.`status` = 1) as confirmed'
        //         ),
        //         DB::raw(
        //             '(SELECT COUNT(invoices.status) FROM `invoices` WHERE `organizations`.`id` = `invoices`.`organization_id` AND `invoices`.`status` = 0) as not_confirmed'
        //         ),
        //         DB::raw(
        //             'SUM(invoice_items.price * invoice_items.count) as sum_price'
        //         ),
        //         DB::raw(
        //             'SUM(invoice_items.count) as sum_count'
        //         )
        //     )
        //     ->leftJoin('invoices', 'organizations.id', '=', 'invoices.organization_id')
        //     ->leftJoin('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        //     ->groupBy('organizations.id')
        //     ->paginate(20)
        //     ->withQueryString()
        //     ->through(fn($report) => [
        //         'id' => $report->id,
        //         'name' => $report->name,
        //         'created_at' => $report->created_at->format('Y-m-d'),
        //         'confirmed' => $report->confirmed ?? 0,
        //         'not_confirmed' => $report->not_confirmed ?? 0,
        //         'sum_price' => $report->sum_price ?? 0,
        //         'sum_count' => $report->sum_count ?? 0,
        //     ]);

        return Inertia::render('Reports/Index', [
            'filters' => Request::all('search', 'organization_id', 'begin', 'end'),
            'reports' => $reports_merge,
            'organizations' => Organization::get(),
            'sum_pay' => $sum_pay,
            'not_sum_pay' => $not_sum_pay,
        ]);
    }

    public function all(Organization $organization, Supplier $supplier)
    {
        if (!is_null(Request::input('begin'))) Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        if (!is_null(Request::input('end'))) Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);

        $invoices = $organization->invoices()
            ->filter(Request::only('search'))
            ->where('supplier_id', $supplier->id)
            ->where('status', 1)
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('date', '<=', $search);
            })
            ->get()
            ->transform(fn ($invoice) => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'pay' => $invoice->pay,
                'date' => $invoice->date->format('Y-m-d'),
                'accepted' => $invoice->accepted,
                'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum') / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            ]);

        $sum_pay = 0;

        foreach ($invoices as $reports_mer) {
            $sum_pay += $reports_mer['sum'];
        }
        return Inertia::render('Reports/All', [
            'filters' => Request::all('search', 'begin', 'end'),
            'organization' => $organization,
            'supplier' => $supplier,
            'invoices' => $invoices,
            'sum_pay' => $sum_pay,
        ]);
    }

    public function pay(Organization $organization, Supplier $supplier)
    {
        if (!is_null(Request::input('begin'))) Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        if (!is_null(Request::input('end'))) Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);

        $invoices = $organization->invoices()
            ->filter(Request::only('search'))
            ->where('supplier_id', $supplier->id)
            ->where('pay', true)
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('date', '<=', $search);
            })
            ->where('status', 1)
            ->get()
            ->transform(fn ($invoice) => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'date' => $invoice->date->format('Y-m-d'),
                'accepted' => $invoice->accepted,
                'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum') / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            ]);

        $sum_pay = 0;

        foreach ($invoices as $reports_mer) {
            $sum_pay += $reports_mer['sum'];
        }

        return Inertia::render('Reports/Pay', [
            'filters' => Request::all('search', 'begin', 'end'),
            'organization' => $organization,
            'supplier' => $supplier,
            'invoices' => $invoices,
            'sum_pay' => $sum_pay,
        ]);
    }

    public function notPay(Organization $organization, Supplier $supplier)
    {
        if (!is_null(Request::input('begin'))) Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        if (!is_null(Request::input('end'))) Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);

        $invoices = $organization->invoices()
            ->filter(Request::only('search'))
            ->where('supplier_id', $supplier->id)
            ->where('pay', false)
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('date', '<=', $search);
            })
            ->where('status', 1)
            ->get()
            ->transform(fn ($invoice) => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'date' => $invoice->date->format('Y-m-d'),
                'accepted' => $invoice->accepted,
                'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum') / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            ]);

        $sum_pay = 0;

        foreach ($invoices as $reports_mer) {
            $sum_pay += $reports_mer['sum'];
        }

        return Inertia::render('Reports/NotPay', [
            'filters' => Request::all('search', 'begin', 'end'),
            'organization' => $organization,
            'supplier' => $supplier,
            'invoices' => $invoices,
            'sum_pay' => $sum_pay,
        ]);
    }

    public function invoiceItem(Organization $organization, Supplier $supplier, Invoice $invoice)
    {
        if (!is_null(Request::input('begin'))) Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        if (!is_null(Request::input('end'))) Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);

        return Inertia::render('Reports/InvoiceItems', [
            'filters' => Request::all('begin', 'end'),
            'organization' => $organization,
            'supplier' => $supplier,
            'invoice' => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'status' => $invoice->status,
                'pay' => $invoice->pay,
                'date' => $invoice->date->format('Y-m-d'),
                'supplier' => $invoice->supplier,
                'accepted' => $invoice->accepted,
                'file' => $invoice->file ? '\\file\\' . $invoice->file : '',
                'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum') / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            ],
            'invoice_items' => $invoice->invoiceItems->transform(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'count' => $item->count / InvoiceItem::FLOAT_TO_INT_COUNT,
                'price' => $item->price / InvoiceItem::FLOAT_TO_INT_PRICE,
                'measurement' => $item->measurement,
            ]),
        ]);
    }

    public function items()
    {
        $items = Item::orderByDesc('sort')->orderBy('name')
            ->whereHas('invoiceItems', function ($query) {
                $query->whereHas('invoice', function ($query) {
                    $query->whereHas('organization', function ($query) {
                        $query->where('id', Request::input('organization_id'));
                    });
                });
            })
            ->withCount(['invoiceItems AS invoice_items_count' => function ($query) {
                $query->select(DB::raw('SUM(count) as invoice_items_count'));
            }])
            ->with('measurement')
            ->get()
            ->transform(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'measurement' => $item->measurement ? $item->measurement->name : '',
                'count' => $item->invoice_items_count / InvoiceItem::FLOAT_TO_INT_COUNT,
            ]);
        // ->paginate(20)
        // ->withQueryString()
        // ->through(fn ($item) => [
        //     'id' => $item->id,
        //     'name' => $item->name,
        //     'measurement' => $item->measurement ? $item->measurement->name : '',
        //     'count' => $item->invoice_items_count,
        // ]);

        return Inertia::render('Reports/Items', [
            'filters' => Request::all('search', 'organization_id'),
            'organizations' => Organization::get(),
            'items' => $items,
        ]);
    }
}
