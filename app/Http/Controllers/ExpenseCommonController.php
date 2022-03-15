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

class ExpenseCommonController extends Controller
{
    public function Index()
    {
        if (!is_null(Request::input('begin'))) {
            Request::merge(['begin' => (new Carbon(Request::input('begin')))->format('Y-m-d')]);
        }

        if (!is_null(Request::input('end'))) {
            Request::merge(['end' => (new Carbon(Request::input('end')))->format('Y-m-d')]);
        }

        $expenses = Expense::whereNull('organization_id')
            ->orderByDesc('date')
            ->when(Request::input('begin') ?? null, function ($query, $search) {
                $query->where('date', '>=', $search);
            })
            ->when(Request::input('end') ?? null, function ($query, $search) {
                $query->where('date', '<=', $search);
            })
            ->when(Request::input('expense_category_id') ?? null, function ($query, $search) {
                $query->where('expense_category_id', $search);
            })
            ->get()
            ->transform(fn ($expense) => [
                'id' => $expense->id,
                'name' => $expense->name,
                'category' => $expense->category->name,
                'fullname' => $expense->user->last_name . ' ' .  $expense->user->first_name,
                'price' => $expense->price ? $expense->price / Expense::FLOAT_TO_INT_PRICE : null,
                'paid' => $expense->paid ? $expense->paid / Expense::FLOAT_TO_INT_PRICE : null,
                'date' => $expense->date->format('d.m.Y'),
            ]);
        $paid_sum = 0;
        foreach ($expenses as $expense) {
            $paid_sum += $expense['paid'];
        }
        return Inertia::render('ExpenseCommon/Index', [
            'filters' => Request::only('begin', 'end', 'expense_category_id'),
            'expenses' => $expenses,
            'paid_sum' => $paid_sum,
            'expense_categories' => ExpenseCategory::get()
        ]);
    }

    public function create(Organization $organization)
    {
        return Inertia::render('ExpenseCommon/Create', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'categories' => ExpenseCategory::orderBy('sort')->orderBy('name')->get(),
        ]);
    }

    public function store()
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

        $expense = Expense::create(Request::only('name', 'date', 'price', 'expense_category_id', 'user_id'));

        return Redirect::route('expense-common-history', $expense->id)->with('success', 'Расход, создано.');
    }

    public function update(Organization $organization, Expense $expense)
    {
        if (!is_null(Request::input('date'))) Request::merge(['date' => (new Carbon(Request::input('date')))->format('Y-m-d')]);

        Request::validate([
            'name' => ['required', 'max:255'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'expense_category_id' => ['required'],
            'price' => ['required', 'numeric', 'min:0', 'max:5000000000'],
        ]);

        Request::merge([
            'price' => Request::input('price') * Expense::FLOAT_TO_INT_PRICE,
        ]);

        $expense->update(Request::only('name', 'date', 'price', 'expense_category_id'));

        return Redirect::back()->with('success', 'Успешно обновлено.');
    }

    public function delete(Invoice $invoice)
    {
        Request::validate([
            'item_id' => ['required', 'max:255'],
        ]);

        $invoice->invoiceItems()->where('id', Request::input('item_id'))->delete();

        return Redirect::back()->with('success', 'Успешно удален.');
    }
}
