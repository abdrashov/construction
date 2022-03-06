<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\Organization;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function index()
    {
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
                    ->where('invoices.pay', true);
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
                    ->where('invoices.pay', false);
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

        foreach ($reports as $report) {
            $reports_merge->push($report);
        }
        foreach ($not_reports as $not_report) {
            $reports_merge->push($not_report);
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
            'filters' => Request::all('search', 'organization_id'),
            'reports' => $reports_merge,
            'organizations' => Organization::get(),
        ]);
    }

    public function all(Organization $organization, Supplier $supplier)
    {
        $invoices = $organization->invoices()
            ->filter(Request::only('search'))
            ->where('supplier_id', $supplier->id)
            ->where('status', 1)
            ->get()
            ->transform(fn ($invoice) => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'pay' => $invoice->pay,
                'date' => $invoice->date->format('Y-m-d'),
                'accepted' => $invoice->accepted,
                'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum') / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            ]);

        return Inertia::render('Reports/All', [
            'filters' => Request::only('search'),
            'organization' => $organization,
            'supplier' => $supplier,
            'invoices' => $invoices,
        ]);
    }

    public function pay(Organization $organization, Supplier $supplier)
    {
        $invoices = $organization->invoices()
            ->filter(Request::only('search'))
            ->where('supplier_id', $supplier->id)
            ->where('pay', true)
            ->where('status', 1)
            ->get()
            ->transform(fn ($invoice) => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'date' => $invoice->date->format('Y-m-d'),
                'accepted' => $invoice->accepted,
                'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum') / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            ]);

        return Inertia::render('Reports/Pay', [
            'filters' => Request::only('search'),
            'organization' => $organization,
            'supplier' => $supplier,
            'invoices' => $invoices,
        ]);
    }

    public function notPay(Organization $organization, Supplier $supplier)
    {
        $invoices = $organization->invoices()
            ->filter(Request::only('search'))
            ->where('supplier_id', $supplier->id)
            ->where('pay', false)
            ->where('status', 1)
            ->get()
            ->transform(fn ($invoice) => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'date' => $invoice->date->format('Y-m-d'),
                'accepted' => $invoice->accepted,
                'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum') / (InvoiceItem::FLOAT_TO_INT_PRICE * InvoiceItem::FLOAT_TO_INT_COUNT),
            ]);

        return Inertia::render('Reports/NotPay', [
            'filters' => Request::only('search'),
            'organization' => $organization,
            'supplier' => $supplier,
            'invoices' => $invoices,
        ]);
    }
}
