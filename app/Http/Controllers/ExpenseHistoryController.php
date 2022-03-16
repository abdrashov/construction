<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseHistory;
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

class ExpenseHistoryController extends Controller
{
    public function Index(Expense $expense)
    {
        return Inertia::render('ExpenseHistory/Index', [
            'filters' => Request::only('search', 'page', 'begin', 'end'),
            'organization' => [
                'id' => $expense->organization->id,
                'name' => $expense->organization->name,
            ],
            'categories' => ExpenseCategory::orderBy('sort')->orderBy('name')->get(),
            'expense' => [
                'id' => $expense->id,
                'name' => $expense->name,
                'expense_category_id' => $expense->expense_category_id,
                'price' => $expense->price ? $expense->price / Expense::FLOAT_TO_INT_PRICE : null,
                'paid' => $expense->paid ? $expense->paid / Expense::FLOAT_TO_INT_PRICE : null,
                'date' => $expense->date->format('Y-m-d'),
            ],
            'expenses_histories' => $expense->expenseHistories()->orderByDesc('date')
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($expense) => [
                    'id' => $expense->id,
                    'name' => $expense->name,
                    'fullname' => $expense->user->last_name . ' ' .  $expense->user->first_name,
                    'price' => $expense->price / ExpenseHistory::FLOAT_TO_INT_PRICE,
                    'date' => $expense->date->format('d.m.Y'),
                ]),
        ]);
    }

    public function store(Expense $expense)
    {
        if (!is_null(Request::input('date'))) Request::merge(['date' => (new Carbon(Request::input('date')))->format('Y-m-d')]);

        Request::validate([
            'name' => ['nullable', 'max:255'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'price' => ['required', 'numeric', 'min:0', 'max:5000000000'],
        ]);

        Request::merge([
            'price' => Request::input('price') * ExpenseHistory::FLOAT_TO_INT_PRICE,
            'user_id' => Auth::id()
        ]);

        $expense->expenseHistories()
            ->create(Request::only('name', 'date', 'price', 'user_id'));

        $expense->update([
            'paid' => $expense->paid + Request::input('price')
        ]);

        return Redirect::back()->with('success', 'Оплата, создано.');
    }

    public function destroy(Expense $expense, ExpenseHistory $expense_history)
    {
        $expense->update([
            'paid' => $expense->paid - $expense_history->price
        ]);

        $expense_history->delete();

        return Redirect::back()->with('success', 'Удален.');
    }

}
