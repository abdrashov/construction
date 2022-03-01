<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AcceptedController extends Controller
{
    public function index()
    {
        return Inertia::render('Reference/Accepted/Index', []);
    }
}
