<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Organization;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function common()
    {
        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

        $invoice_sum = Invoice::where('organization_id', Request::input('organization_id'))
            ->join('invoice_items', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereNull('invoices.deleted_at')
            ->whereNull('invoice_items.deleted_at')
            ->where('invoices.status', true)
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('invoices.date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('invoices.date', '<=', $search);
            })
            ->select(DB::raw('SUM(invoice_items.price * invoice_items.count) as sum'))
            ->groupBy('invoices.organization_id')
            ->value('sum');

        $expense_sum = Expense::where('organization_id', Request::input('organization_id'))
            ->join('expense_histories', 'expenses.id', '=', 'expense_histories.expense_id')
            ->whereNull('expenses.deleted_at')
            ->whereNull('expense_histories.deleted_at')
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('expense_histories.date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('expense_histories.date', '<=', $search);
            })
            ->select('expenses.*', DB::raw('SUM(expense_histories.price) as sum'))
            ->groupBy('expenses.organization_id')
            ->value('sum');

        return Inertia::render('Reports/Common', [
            'filters' => Request::all('organization_id', 'begin', 'end'),
            'organizations' => Organization::get(),
            'invoice_sum' => (float) $invoice_sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            'expense_sum' => (float) $expense_sum / Expense::FLOAT_TO_INT_PRICE,
        ]);
    }

    public function index()
    {
        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

        // $reports = Supplier::filter(Request::only('search'))
        //     ->select(
        //         'organizations.id as id',
        //         'suppliers.id as supplier_id',
        //         'suppliers.name as supplier',
        //         DB::raw('SUM(invoice_items.price * invoice_items.count) as pay_sum'),
        //     )
        //     ->join('invoices', 'suppliers.id', '=', 'invoices.supplier_id')
        //     ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        //     ->join('organizations', 'invoices.organization_id', '=', 'organizations.id')
        //     ->where('organizations.id', Request::input('organization_id'))
        //     ->whereNull('organizations.deleted_at')
        //     ->whereNull('invoice_items.deleted_at')
        //     ->where('invoices.status', true)
        //     ->where('invoices.pay', true)
        //     ->when(Request::input('begin') ?? null, function ($query, $search) {
        //         $query->where('invoices.date', '>=', $search);
        //     })
        //     ->when(Request::input('end') ?? null, function ($query, $search) {
        //         $query->where('invoices.date', '<=', $search);
        //     })
        //     ->whereNull('invoices.deleted_at')
        //     ->groupBy('suppliers.id')
        //     ->get()
        //     ->transform(fn ($report) => [
        //         'id' => $report->id,
        //         'supplier_id' => $report->supplier_id,
        //         'supplier' => $report->supplier,
        //         'pay_sum' => $report->pay_sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
        //         'not_pay_sum' => $report->not_pay_sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
        //     ]);

        // $not_reports = Supplier::filter(Request::only('search'))
        //     ->select(
        //         'organizations.id as id',
        //         'suppliers.id as supplier_id',
        //         'suppliers.name as supplier',
        //         DB::raw('SUM(invoice_items.price * invoice_items.count) as not_pay_sum'),
        //     )
        //     ->join('invoices', 'suppliers.id', '=', 'invoices.supplier_id')
        //     ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
        //     ->join('organizations', 'invoices.organization_id', '=', 'organizations.id')
        //     ->where('organizations.id', Request::input('organization_id'))
        //     ->whereNull('organizations.deleted_at')
        //     ->whereNull('invoice_items.deleted_at')
        //     ->where('invoices.status', true)
        //     ->where('invoices.pay', false)
        //     ->when(Request::input('begin') ?? null, function ($query, $search) {
        //         $query->where('invoices.date', '>=', $search);
        //     })
        //     ->when(Request::input('end') ?? null, function ($query, $search) {
        //         $query->where('invoices.date', '<=', $search);
        //     })
        //     ->whereNull('invoices.deleted_at')
        //     ->groupBy('suppliers.id')
        //     ->get()
        //     ->transform(fn ($report) => [
        //         'id' => $report->id,
        //         'supplier_id' => $report->supplier_id,
        //         'supplier' => $report->supplier,
        //         'pay_sum' => $report->pay_sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
        //         'not_pay_sum' => $report->not_pay_sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
        //     ]);

        // foreach ($reports as $key_1 => $report_1) {
        //     foreach ($not_reports as $key_2 => $report_2) {
        //         if ($report_1['supplier_id'] == $report_2['supplier_id']) {
        //             $reports[$key_1] = [
        //                 'id' => $report_1['id'],
        //                 'supplier_id' => $report_1['supplier_id'],
        //                 'supplier' => $report_1['supplier'],
        //                 'pay_sum' => $report_1['pay_sum'] + $report_2['pay_sum'],
        //                 'not_pay_sum' => $report_1['not_pay_sum'] + $report_2['not_pay_sum'],
        //             ];
        //             unset($not_reports[$key_2]);
        //         }
        //     }
        // }

        // $reports_merge = collect();
        // $sum_pay = 0;
        // $not_sum_pay = 0;

        // foreach ($reports as $report) {
        //     $reports_merge->push($report);
        // }
        // foreach ($not_reports as $not_report) {
        //     $reports_merge->push($not_report);
        // }
        // foreach ($reports_merge as $reports_mer) {
        //     $sum_pay += $reports_mer['pay_sum'];
        //     $not_sum_pay += $reports_mer['not_pay_sum'];
        // }

        $reports = Supplier::filter(Request::only('search'))
            ->select(
                'organizations.id as id',
                'suppliers.id as supplier_id',
                'suppliers.name as supplier',
                'invoices.pay as pay',
                DB::raw('SUM(invoice_items.price * invoice_items.count) as sum'),
            )
            ->join('invoices', 'suppliers.id', '=', 'invoices.supplier_id')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('organizations', 'invoices.organization_id', '=', 'organizations.id')
            ->where('organizations.id', Request::input('organization_id'))
            ->whereNull('organizations.deleted_at')
            ->whereNull('invoice_items.deleted_at')
            ->where('invoices.status', true)
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('invoices.date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('invoices.date', '<=', $search);
            })
            ->whereNull('invoices.deleted_at')
            ->orderBy('suppliers.name')
            ->groupBy('suppliers.id')
            ->groupBy('invoices.pay')
            ->get()
            ->transform(fn ($report) => [
                'id' => $report->id,
                'supplier_id' => $report->supplier_id,
                'supplier' => $report->supplier,
                'pay_sum' => $report->pay == 1 ? $report->sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT) : 0,
                'not_pay_sum' => $report->pay == 0 ? $report->sum / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT) : 0,
            ])
            ->toArray();

        for ($i = 0; $i < count($reports); $i++) {
            if (empty($reports[$i])) {
                continue;
            }
            for ($j = 0; $j < count($reports); $j++) {
                if (empty($reports[$j]) || $i == $j) {
                    continue;
                }
                if ($reports[$i]['supplier_id'] != $reports[$j]['supplier_id']) {
                    continue;
                }
                $reports[$i] = [
                    'id' => $reports[$i]['id'],
                    'supplier_id' => $reports[$i]['supplier_id'],
                    'supplier' => $reports[$i]['supplier'],
                    'pay_sum' => $reports[$i]['pay_sum'] + $reports[$j]['pay_sum'],
                    'not_pay_sum' => $reports[$i]['not_pay_sum'] + $reports[$j]['not_pay_sum'],
                ];

                unset($reports[$j]);
            }
        }


        $sum_pay = 0;
        $not_sum_pay = 0;
        $reports = array_values($reports);

        foreach ($reports as $report) {
            $sum_pay += $report['pay_sum'];
            $not_sum_pay += $report['not_pay_sum'];
        }


        return Inertia::render('Reports/Index', [
            'filters' => Request::all('search', 'organization_id', 'begin', 'end'),
            'reports' => $reports,
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
            ->filter(['name' => Request::input('search')])
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
            ->filter(['name' => Request::input('search')])
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
            ->filter(['name' => Request::input('search')])
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

        Request::merge([
            'old' => in_array(Request::input('old'), ['all', 'pay', 'not_pay']) ? Request::input('old') : 'all'
        ]);

        return Inertia::render('Reports/InvoiceItems', [
            'filters' => Request::all('begin', 'end', 'old'),
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
        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

        $items = InvoiceItem::select(
            'invoice_items.id',
            'invoice_items.item_id',
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
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('invoices.date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('invoices.date', '<=', $search);
            })
            ->when(Request::input('item_category_id') ?? null, function ($query, $search) {
                $query->where('item_categories.id', $search);
            })
            ->when(Request::input('supplier_id') ?? null, function ($query, $search) {
                $query->where('invoices.supplier_id', $search);
            })
            ->whereNull('invoices.deleted_at')
            ->groupBy('invoice_items.item_id')
            ->orderBy('item_categories.sort')
            ->orderBy('item_categories.name')
            ->orderBy('invoice_items.name')
            ->get()
            ->transform(fn ($item) => [
                'id' => $item->id,
                'item_id' => $item->item_id,
                'name' => $item->name,
                'item_category_id' => $item->item_category_id,
                'item_category' => $item->item_category,
                'measurement' => $item->measurement,
                'count' => $item->count / InvoiceItem::FLOAT_TO_INT_COUNT,
                'sum' => $item->sum / (InvoiceItem::FLOAT_TO_INT_COUNT * InvoiceItem::FLOAT_TO_INT_PRICE),
            ])->toArray();

        $item_categories = [];
        $item_category = 0;
        $item_category_count = 0;
        $valdate = 0;
        $max_lenght = 0;
        for ($index = 0; $index < count($items); $index++) {
            if ($valdate != $items[$index]['item_category_id']) {
                $item_categories[] = [
                    'column' => 'name',
                    'name' => $items[$index]['item_category'],
                ];
            }

            $item_categories[] = ['column' => 'content', 'index' => $index + 1] + $items[$index];
            $item_category += $items[$index]['sum'];
            $item_category_count += $items[$index]['count'];
            $number = explode('.', $item_category_count);
            if (count($number) > 1 && strlen($number[1]) > $max_lenght) {
                $max_lenght = strlen($number[1]);
            }

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
                $item_categories[] = [
                    'column' => 'sum',
                    'category_count' => round($item_category_count, $max_lenght),
                    'category_sum' => $item_category,
                ];
                $item_category_count = 0;
                $item_category = 0;
                $max_lenght = 0;
            }
        }

        $sum_item = 0;
        foreach ($items as $item) {
            $sum_item += $item['sum'];
        }

        return Inertia::render('Reports/Items', [
            'filters' => Request::all('organization_id', 'begin', 'end', 'item_category_id', 'supplier_id'),
            'organizations' => Organization::get(),
            'item_categories' => ItemCategory::orderBy('sort')->orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'items' => $item_categories,
            'sum_item' => $sum_item,
        ]);
    }

    public function itemSupplier(Organization $organization, Item $item)
    {
        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

        $suppliers = InvoiceItem::select(
            'suppliers.id',
            'suppliers.name',
            'invoices.name as invoice',
            'invoices.date',
            'invoice_items.measurement',
            DB::raw('invoice_items.price as price'),
            DB::raw('invoice_items.count as count'),
        )
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('suppliers', 'suppliers.id', '=', 'invoices.supplier_id')
            ->where('invoices.organization_id', $organization->id)
            ->where('invoices.status', true)
            ->where('invoice_items.item_id', $item->id)
            ->whereNull('invoices.deleted_at')
            ->whereNull('suppliers.deleted_at')
            ->whereNull('invoice_items.deleted_at')
            ->when(Request::input('search') ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('suppliers.name', 'like', '%' . $search . '%')
                        ->orWhere('invoices.name', 'like', '%' . $search . '%');
                });
            })
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('invoices.date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('invoices.date', '<=', $search);
            })
            ->groupBy('invoice_items.id')
            ->orderByDesc('invoices.date')
            ->get()
            ->transform(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'invoice' => $supplier->invoice,
                    'date' => $supplier->date ? (new Carbon($supplier->date))->format('d.m.Y') : '',
                    'count' => $supplier->count / InvoiceItem::FLOAT_TO_INT_COUNT,
                    'price' => $supplier->price / InvoiceItem::FLOAT_TO_INT_PRICE,
                    'measurement' => $supplier->measurement,
                    'sum' => ($supplier->count * $supplier->price) / (InvoiceItem::FLOAT_TO_INT_COUNT * InvoiceItem::FLOAT_TO_INT_PRICE),
                ];
            })->toArray();

        $sum_pay = 0;
        $count_pay = 0;
        $max_lenght = 0;
        foreach ($suppliers as $supplier) {
            $sum_pay += $supplier['sum'];
            $count_pay += $supplier['count'];
            $number = explode('.', $supplier['count']);
            if (count($number) > 1 && strlen($number[1]) > $max_lenght) {
                $max_lenght = strlen($number[1]);
            }
        }

        return Inertia::render('Reports/ItemSupplier', [
            'filters' => Request::all('search', 'begin', 'end', 'supplier_id'),
            'organization' => $organization,
            'suppliers' => $suppliers,
            'item' => $item,
            'sum_pay' => $sum_pay,
            'count_pay' => round($count_pay, $max_lenght)
        ]);
    }

    public function exportIndex()
    {
        if (Request::input('begin') == 'null') {
            Request::merge(['begin' => null]);
        }

        if (Request::input('end') == 'null') {
            Request::merge(['end' => null]);
        }

        if (Request::input('item_category_id') == 'null') {
            Request::merge(['item_category_id' => null]);
        }

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

        $html = '';
        $html = $html . '<table>';
        $html = $html . '<tr>
            <th rowspan="2">#</th>
            <th rowspan="2">Поставщик</th>
            <th colspan="3">Накладные</th>
        </tr>';
        $html = $html . '<tr>
            <th>Оплачен</th>
            <th>Не оплачен</th>
            <th>Итого</th>
        </tr>';
        $index = 0;
        foreach ($reports_merge as $item_category) {
            $html = $html . '<tr>';
            $html = $html . '<td colspan="1">';
            $html = $html . ++$index;
            $html = $html . '</td>';
            $html = $html . '<td>';
            $html = $html . $item_category['supplier'];
            $html = $html . '</td>';
            $html = $html . '<td>';
            $html = $html . $item_category['pay_sum'];
            $html = $html . '</td>';
            $html = $html . '<td>';
            $html = $html . $item_category['not_pay_sum'];
            $html = $html . '</td>';
            $html = $html . '<td>';
            $html = $html . $item_category['pay_sum'] + $item_category['not_pay_sum'];
            $html = $html . '</td>';
            $html = $html . '</tr>';
        }
        $html = $html . '<tr>
            <th colspan="2">ИТОГО</th>
            <th>' . $sum_pay . '</th>
            <th>' . $not_sum_pay . '</th>
            <th>' . $sum_pay + $not_sum_pay . '</th>
        </tr>';
        $html = $html . '</table>';

        $organization = Organization::findOrFail(Request::input('organization_id'));

        return view('report', [
            'title' => $organization->name,
            'begin' => Request::input('begin'),
            'end' => Request::input('end'),
            'report' => $html,
        ]);
    }

    public function exportItem()
    {
        if (Request::input('begin') == 'null') {
            Request::merge(['begin' => null]);
        }

        if (Request::input('end') == 'null') {
            Request::merge(['end' => null]);
        }

        if (Request::input('supplier_id') == 'null') {
            Request::merge(['supplier_id' => null]);
        }

        if (Request::input('item_category_id') == 'null') {
            Request::merge(['item_category_id' => null]);
        }

        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

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
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('invoices.date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('invoices.date', '<=', $search);
            })
            ->when(Request::input('item_category_id') ?? null, function ($query, $search) {
                $query->where('item_categories.id', $search);
            })
            ->when(Request::input('supplier_id') ?? null, function ($query, $search) {
                $query->where('invoices.supplier_id', $search);
            })
            ->whereNull('invoices.deleted_at')
            ->groupBy('invoice_items.item_id')
            ->orderBy('item_categories.sort')
            ->orderBy('item_categories.name')
            ->orderBy('invoice_items.name')
            ->get()
            ->transform(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'item_category_id' => $item->item_category_id,
                'item_category' => $item->item_category,
                'measurement' => $item->measurement,
                'count' => $item->count / InvoiceItem::FLOAT_TO_INT_COUNT,
                'sum' => $item->sum / (InvoiceItem::FLOAT_TO_INT_COUNT * InvoiceItem::FLOAT_TO_INT_PRICE),
            ])->toArray();

        $item_categories = [];
        $item_category = 0;
        $item_category_count = 0;
        $valdate = 0;
        for ($index = 0; $index < count($items); $index++) {
            if ($valdate != $items[$index]['item_category_id']) {
                $item_categories[] = [
                    'column' => 'name',
                    'name' => $items[$index]['item_category'],
                ];
            }

            $item_categories[] = ['column' => 'content', 'index' => $index + 1] + $items[$index];
            $item_category += $items[$index]['sum'];
            $item_category_count += $items[$index]['count'];

            $valdate = $items[$index]['item_category_id'];

            if (empty($items[$index + 1]) || $items[$index]['item_category_id'] != $items[$index + 1]['item_category_id']) {
                $item_categories[] = [
                    'column' => 'sum',
                    'category_count' => $item_category_count,
                    'category_sum' => $item_category,
                ];
                $item_category_count = 0;
                $item_category = 0;
            }
        }

        $sum_item = 0;
        foreach ($items as $item) {
            $sum_item += $item['sum'];
        }

        $organization = Organization::findOrFail(Request::input('organization_id'));

        $html = '';
        $html = $html . '<table>';
        $html = $html . '<tr>
            <th>#</th>
            <th>Название Товара</th>
            <th>Использовался</th>
            <th>Сумма</th>
        </tr>';
        foreach ($item_categories as $item_category) {
            $html = $html . '<tr>';
            if ($item_category['column'] == 'content') {
                $html = $html . '<td colspan="1">';
                $html = $html . $item_category['index'];
                $html = $html . '</td>';
                $html = $html . '<td>';
                $html = $html . $item_category['name'];
                $html = $html . '</td>';
                $html = $html . '<td>';
                $html = $html . $item_category['count'];
                $html = $html . $item_category['measurement'];
                $html = $html . '</td>';
                $html = $html . '<td>';
                $html = $html . $item_category['sum'];
                $html = $html . '</td>';
            } else if ($item_category['column'] == 'name') {
                $html = $html . '<td colspan="5">';
                $html = $html . $item_category['name'];
                $html = $html . '</td>';
            } else if ($item_category['column'] == 'sum') {
                $html = $html . '<th colspan="2">Итого</th>';
                $html = $html . '<th>';
                $html = $html . $item_category['category_count'];
                $html = $html . '</th>';
                $html = $html . '<th>';
                $html = $html . $item_category['category_sum'];
                $html = $html . '</th>';
            }
            $html = $html . '</tr>';
        }
        $html = $html . '<tr>
            <th colspan="3">ВСЕГО</th>
            <th colspan="2">' . $sum_item . '</th>
        </tr>';
        $html = $html . '</table>';

        return view('report', [
            'title' => $organization->name,
            'begin' => Request::input('begin'),
            'end' => Request::input('end'),
            'report' => $html,
        ]);
        // return Inertia::render('Reports/Export', [
        //     'title' => 'Отчет',
        //     'reports' => $html
        // ]);
    }
}
