<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function index()
    {
        $organizations = Organization::get();

        // if (!Request::has('organization_id')) {
        //     Request::merge(['organization_id' => $organizations->first()->id]);
        // }

        $reports = Supplier::filter(Request::only('search'))
            ->select(
                'organizations.id as id',
                'suppliers.id as supplier_id',
                'suppliers.name as supplier',
                DB::raw('COUNT(`invoices`.`pay`) as pay'),
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
                    ->where('invoices.organization_id', Request::input('organization_id'));
            })
            ->groupBy('suppliers.id')
            ->get()
            ->transform(fn ($report) => [
                'id' => $report->id,
                'supplier_id' => $report->supplier_id,
                'supplier' => $report->supplier,
                'pay' => $report->pay ?? 0,
                'pay_sum' => $report->pay_sum ?? 0,
                'not_pay' => $report->not_pay ?? 0,
                'not_pay_sum' => $report->not_pay_sum ?? 0,
            ]);

        $not_reports = Supplier::filter(Request::only('search'))
            ->select(
                'organizations.id as id',
                'suppliers.id as supplier_id',
                'suppliers.name as supplier',
                DB::raw('COUNT(`invoices`.`pay`) as not_pay'),
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
                    ->where('invoices.organization_id', Request::input('organization_id'));
            })
            ->groupBy('suppliers.id')
            ->get()
            ->transform(fn ($report) => [
                'id' => $report->id,
                'supplier_id' => $report->supplier_id,
                'supplier' => $report->supplier,
                'pay' => $report->pay ?? 0,
                'pay_sum' => $report->pay_sum ?? 0,
                'not_pay' => $report->not_pay ?? 0,
                'not_pay_sum' => $report->not_pay_sum ?? 0,
            ]);

        foreach ($reports as $key_1 => $report_1) {
            foreach ($not_reports as $key_2 => $report_2) {
                if ($report_1['supplier_id'] == $report_2['supplier_id']) {
                    $reports[$key_1] = [
                        'id' => $report_1['id'],
                        'supplier_id' => $report_1['supplier_id'],
                        'supplier' => $report_1['supplier'],
                        'pay' => $report_1['pay'] + $report_2['pay'],
                        'pay_sum' => $report_1['pay_sum'] + $report_2['pay_sum'],
                        'not_pay' => $report_1['not_pay'] + $report_2['not_pay'],
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


        // $reports_merge->push([
        //     'id' => $report['id'],
        //     'supplier' => $report['supplier'],
        //     'pay' => $report['pay'] + $not_report['pay'],
        //     'pay_sum' => $report['pay_sum'] + $not_report['pay_sum'],
        //     'not_pay' => $report['not_pay'] + $not_report['not_pay'],
        //     'not_pay_sum' => $report['not_pay_sum'] + $not_report['not_pay_sum'],
        // ]);

        // $reports = collect($reports);
        // $not_reports = collect($not_reports);
        // $reports_merge = $reports->merge($not_reports);

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
            'organizations' => $organizations,
        ]);
    }

    public function all(Organization $organization, Supplier $supplier)
    {
        // $invoices = $organization->invoices()
        //     ->filter(Request::only('search', 'pay'))
        //     ->with('invoiceItems')
        //     ->where('supplier_id', $supplier->id)
        //     ->where('status', 1)
        //     ->get()
        //     ->transform(fn($invoice) => [
        //         'id' => $invoice->id,
        //         'name' => $invoice->name,
        //         'pay' => $invoice->pay,
        //         'status' => $invoice->status,
        //         'date' => $invoice->date->format('Y-m-d'),
        //         'accepted' => $invoice->accepted,
        //         'invoice_items' => $invoice->invoiceItems,
        //         'sum_count' => $invoice->invoiceItems()->sum('count'),
        //         'sum_price' => $invoice->invoiceItems()->sum('price'),
        //         'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum'),
        //     ]);
        $invoices = $organization->invoices()
            ->filter(Request::only('search', 'pay'))
            ->where('supplier_id', $supplier->id)
            ->where('status', 1)
            ->get()
            ->transform(fn($invoice) => [
                'id' => $invoice->id,
                'name' => $invoice->name,
                'pay' => $invoice->pay,
                'status' => $invoice->status,
                'date' => $invoice->date->format('Y-m-d'),
                'accepted' => $invoice->accepted,
                'sum_count' => $invoice->invoiceItems()->sum('count'),
                'sum_price' => $invoice->invoiceItems()->sum('price'),
                'sum' => $invoice->invoiceItems()->select(DB::raw('SUM(count * price) as sum'))->value('sum'),
            ]);

        return Inertia::render('Reports/Show', [
            'filters' => Request::only('search'),
            'organization' => $organization,
            'supplier' => $supplier,
            'invoices' => $invoices,
        ]);
    }

    public function pay(Organization $organization, Supplier $supplier)
    {
        return Inertia::render('Reports/Show', [
            'filters' => Request::only('search'),
        ]);
    }

    public function notPay(Organization $organization, Supplier $supplier)
    {
        return Inertia::render('Reports/Show', [
            'filters' => Request::only('search'),
        ]);
    }
}
