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
        $object_id = 1;

        $report = Supplier::select(
            'suppliers.name as supplier',
            'organizations.name as organization',
            'invoices.name as invoice',
            DB::raw(
                '(SELECT COUNT(invoices.pay) FROM `invoices` WHERE `organizations`.`id` = `invoices`.`organization_id` AND `invoices`.`status` = 1) as pay'
            ),
            DB::raw(
                '(SELECT COUNT(invoices.pay) FROM `invoices` WHERE `organizations`.`id` = `invoices`.`organization_id` AND `invoices`.`status` = 0) as not_pay'
            ),
            )
            ->join('invoices', function ($query) use ($object_id) {
                $query->on('suppliers.id', '=', 'invoices.supplier_id')
                    ->where('invoices.status', true);
            })
            ->join('organizations', function ($query) use ($object_id) {
                $query->on('invoices.organization_id', '=', 'organizations.id')
                    ->where('invoices.organization_id', $object_id);
            })
            ->groupBy('suppliers.id')
            ->get();

        dd($report);

        $reports = Organization::filter(Request::only('search', 'trashed'))
            ->select(
                'organizations.id',
                'organizations.name',
                'organizations.created_at',
                DB::raw(
                    '(SELECT COUNT(invoices.status) FROM `invoices` WHERE `organizations`.`id` = `invoices`.`organization_id` AND `invoices`.`status` = 1) as confirmed'
                ),
                DB::raw(
                    '(SELECT COUNT(invoices.status) FROM `invoices` WHERE `organizations`.`id` = `invoices`.`organization_id` AND `invoices`.`status` = 0) as not_confirmed'
                ),
                DB::raw(
                    'SUM(invoice_items.price * invoice_items.count) as sum_price'
                ),
                DB::raw(
                    'SUM(invoice_items.count) as sum_count'
                )
            )
            ->leftJoin('invoices', 'organizations.id', '=', 'invoices.organization_id')
            ->leftJoin('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->groupBy('organizations.id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn($report) => [
                'id' => $report->id,
                'name' => $report->name,
                'created_at' => $report->created_at->format('Y-m-d'),
                'confirmed' => $report->confirmed ?? 0,
                'not_confirmed' => $report->not_confirmed ?? 0,
                'sum_price' => $report->sum_price ?? 0,
                'sum_count' => $report->sum_count ?? 0,
            ]);

        return Inertia::render('Reports/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organizations' => $reports,
        ]);
    }
}
