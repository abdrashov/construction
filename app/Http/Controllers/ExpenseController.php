<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Organization;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function Index(Organization $organization)
    {
        $expenses = $organization->expenses()->orderByDesc('date')
            ->get()
            ->transform(fn ($expense) => [
                'id' => $expense->id,
                'name' => $expense->name,
                'category' => $expense->category->name,
                'fullname' => $expense->user->last_name . ' ' .  $expense->user->first_name,
                'price' => $expense->price ? $expense->price / Expense::FLOAT_TO_INT_PRICE : null,
                'paid' => $expense->paid ? $expense->paid / Expense::FLOAT_TO_INT_PRICE : null,
                'date' => $expense->date->format('Y-m-d'),
            ]);

        $paid_sum = 0;
        foreach ($expenses as $expense) {
            $paid_sum += $expense['paid'];
        }

        return Inertia::render('Expense/Index', [
            'filters' => Request::only('search', 'page'),
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'expenses' => $expenses,
            'paid_sum' => $paid_sum
        ]);
    }

    public function create(Organization $organization)
    {
        return Inertia::render('Expense/Create', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'categories' => ExpenseCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Organization $organization)
    {
        if (!is_null(Request::input('date'))) Request::merge(['date' => (new Carbon(Request::input('date')))->format('Y-m-d')]);

        Request::validate([
            'name' => ['required', 'max:255'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'expense_category_id' => ['required'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:5000000000'],
        ]);

        Request::merge([
            'price' => Request::input('price') ? Request::input('price') * Expense::FLOAT_TO_INT_PRICE : null,
            'user_id' => Auth::id()
        ]);

        $expense = $organization->expenses()
            ->create(Request::only('name', 'date', 'price', 'expense_category_id', 'user_id'));

        return Redirect::route('expenses-history', $expense->id)->with('success', 'Расход, создано.');
    }

    public function update(Organization $organization, Expense $expense)
    {
        if (!is_null(Request::input('date'))) Request::merge(['date' => (new Carbon(Request::input('date')))->format('Y-m-d')]);

        Request::validate([
            'name' => ['required', 'max:255'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'expense_category_id' => ['required'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:5000000000'],
        ]);

        Request::merge([
            'price' => Request::input('price') * Expense::FLOAT_TO_INT_PRICE,
        ]);

        $expense->update(Request::only('name', 'date', 'price', 'expense_category_id'));

        return Redirect::back()->with('success', 'Успешно обновлено.');
    }
}