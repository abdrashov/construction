<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
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
        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

        $reports = Supplier::filter(Request::only('search'))
            ->select(
                'organizations.id as id',
                'suppliers.id as supplier_id',
                'suppliers.name as supplier',
                DB::raw('SUM(invoice_items.price * invoice_items.count) as pay_sum'),
            )
            ->join('invoices', 'suppliers.id', '=', 'invoices.supplier_id')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('organizations', 'invoices.organization_id', '=', 'organizations.id')
            ->where('organizations.id', Request::input('organization_id'))
            ->whereNull('organizations.deleted_at')
            ->whereNull('invoice_items.deleted_at')
            ->where('invoices.status', true)
            ->where('invoices.pay', true)
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('invoices.date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('invoices.date', '<=', $search);
            })
            ->whereNull('invoices.deleted_at')
            ->groupBy('suppliers.id')
            ->get()
            ->transform(fn($report) => [
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
            ->join('invoices', 'suppliers.id', '=', 'invoices.supplier_id')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('organizations', 'invoices.organization_id', '=', 'organizations.id')
            ->where('organizations.id', Request::input('organization_id'))
            ->whereNull('organizations.deleted_at')
            ->whereNull('invoice_items.deleted_at')
            ->where('invoices.status', true)
            ->where('invoices.pay', false)
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('invoices.date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('invoices.date', '<=', $search);
            })
            ->whereNull('invoices.deleted_at')
            ->groupBy('suppliers.id')
            ->get()
            ->transform(fn($report) => [
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
        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

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
            ->transform(fn($invoice) => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'pay' => $invoice->pay,
                'date' => $invoice->date->format('d.m.Y'),
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
        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

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
            ->transform(fn($invoice) => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'date' => $invoice->date->format('d.m.Y'),
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
        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

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
            ->transform(fn($invoice) => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'date' => $invoice->date->format('d.m.Y'),
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
        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

        return Inertia::render('Reports/InvoiceItems', [
            'filters' => Request::all('begin', 'end'),
            'organization' => $organization,
            'supplier' => $supplier,
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

    public function items()
    {

        // $items = InvoiceItem::select(
        //     'id',
        //     'name',
        //     'measurement',
        //     'item_id',
        //     DB::raw('SUM(count) as items_count'),
        //     DB::raw('SUM(count * price) as items_price'),
        // )
        //     ->whereHas('invoice', function ($query) {
        //         $query->where('organization_id', Request::input('organization_id'))
        //             ->where('status', true);
        //     })
        //     ->with('item.itemCategory')
        //     ->orderBy('name')
        //     ->groupBy('item_id')
        //     ->get()
        //     ->transform(fn($item) => [
        //         'id' => $item->id,
        //         'name' => $item->name,
        //         'item_category' => $item->item?->itemCategory ? $item->item?->itemCategory->name : null,
        //         'item_category_sort' => $item->item?->itemCategory ? $item->item?->itemCategory->sort : null,
        //         'measurement' => $item->measurement,
        //         'count' => $item->items_count / InvoiceItem::FLOAT_TO_INT_COUNT,
        //         'sum' => $item->items_price / (InvoiceItem::FLOAT_TO_INT_COUNT * InvoiceItem::FLOAT_TO_INT_PRICE),
        //     ])->toArray();

        // array_multisort(array_map(function ($element) {
        //     return $element['item_category'];
        // }, $items), SORT_ASC, $items);

        // array_multisort(array_map(function ($element) {
        //     return $element['name'];
        // }, $items), SORT_ASC, $items);

        // usort($items, function ($a, $b) {
        //     return ($a['item_category_sort'] - $b['item_category_sort']);
        // });

        $items = InvoiceItem::select(
            'invoice_items.id',
            'invoice_items.name',
            'item_categories.id as item_category_id',
            'item_categories.name as item_category',
            'invoice_items.measurement',
            DB::raw('SUM(invoice_items.count) as count'),
            DB::raw('SUM(invoice_items.count * invoice_items.price) as sum'),
        )
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('items', 'invoice_items.item_id', '=', 'items.id')
            ->join('item_categories', 'items.item_category_id', '=', 'item_categories.id')
            ->where('invoices.organization_id', Request::input('organization_id'))
            ->where('invoices.status', true)
            ->groupBy('invoice_items.item_id')
            ->orderBy('item_categories.sort')
            ->orderBy('item_categories.name')
            ->orderBy('invoice_items.name')
            ->get()
            ->transform(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'item_category_id' => $item->item_category_id,
                'item_category' => $item->item_category,
                'measurement' => $item->measurement,
                'count' => $item->count / InvoiceItem::FLOAT_TO_INT_COUNT,
                'sum' => $item->sum / (InvoiceItem::FLOAT_TO_INT_COUNT * InvoiceItem::FLOAT_TO_INT_PRICE),
            ])->toArray();

        //     dd( $items);

        $item_categories = [];
        $item_category = 0;
        $valdate = 0;
        for ($index = 0; $index < count($items); $index++) {
            if ($valdate != $items[$index]['item_category_id']) {
                $item_categories[] = [
                    'name' => $items[$index]['item_category'],
                ];
            }

            $item_categories[] = ['index' => $index + 1] + $items[$index];
            $item_category += $items[$index]['sum'];

            // if ($index - 1 >= 0 && $items[$index - 1]['item_category'] != $items[$index]['item_category']) {
            //     $valdate = true;
            // }
            // if ($valdate) {
            //     $item_categories[] = [
            //         'name' => $items[$index]['item_category'],
            //     ];
            //     $valdate = false;
            // }
            // $item_categories[] = ['index' => $index + 1] + $items[$index];
            // $item_category += $items[$index]['sum'];

            // if ($index + 1 >= count($items) || $items[$index + 1]['item_category'] != $items[$index]['item_category']) {
            //     $item_categories[count($item_categories) - 1] += ['category_sum' => $item_category];
            //     $item_category = 0;
            // }
            $valdate = $items[$index]['item_category_id'];

            if (empty($items[$index + 1]) || $items[$index]['item_category_id'] != $items[$index + 1]['item_category_id']) {
                $item_categories[count($item_categories) - 1] += ['category_sum' => $item_category];
                $item_category = 0;
            }
        }

        $sum_item = 0;
        foreach ($items as $item) {
            $sum_item += $item['sum'];
        }

        return Inertia::render('Reports/Items', [
            'filters' => Request::all('search', 'organization_id'),
            'organizations' => Organization::get(),
            'items' => $item_categories,
            'sum_item' => $sum_item,
        ]);
    }
}
