<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class SuppliersController extends Controller
{
    public function index()
    {
        return Inertia::render('Reference/Suppliers/Index', []);
    }
}
