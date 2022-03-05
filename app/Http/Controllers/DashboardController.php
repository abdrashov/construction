<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Organization;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $users_count = User::count();
        $organizations_count = Organization::count();
        $items_count = Item::count();
        $invoices_count = Invoice::count();

        return Inertia::render('Dashboard/Index', [
            'users_count' => $users_count,
            'organizations_count' => $organizations_count,
            'items_count' => $items_count,
            'invoices_count' => $invoices_count,
        ]);
    }
}
