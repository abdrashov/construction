<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function index()
    {
        $reports = Organization::filter(Request::only('search', 'trashed'))
            ->select(
                'organizations.id',
                'organizations.name',
                'organizations.created_at',
                DB::raw(
                    '(SELECT COUNT(invoices.status) from invoices where organizations.id = invoices.organization_id and invoices.status = 1) as confirmed'
                ),
                DB::raw(
                    '(SELECT COUNT(invoices.status) from invoices where organizations.id = invoices.organization_id and invoices.status = 0) as not_confirmed'
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
            ->through(fn ($report) => [
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
