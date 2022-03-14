<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ItemCategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Reference/ItemCategories/Index', [
            'filters' => Request::all('search', 'trashed'),
            'item_categories' => ItemCategory::orderBy('sort')->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function store()
    {
        ItemCategory::create(
            Request::validate([
                'name' => ['required', 'max:255', Rule::unique('item_categories')],
            ])
        );

        return Redirect::route('reference.items-categories')->with('success', 'Создано.');
    }

    public function edit(ItemCategory $item_category)
    {
        return Inertia::render('Reference/ItemCategories/Edit', [
            'item_category' => [
                'id' => $item_category->id,
                'name' => $item_category->name,
                'deleted_at' => $item_category->deleted_at,
            ],
        ]);
    }

    public function update(ItemCategory $item_category)
    {
        $item_category->update(
            Request::validate([
                'name' => ['required', 'max:255', Rule::unique('item_categories')->ignore($item_category->id)],
            ])
        );

        return Redirect::back()->with('success', 'Обновлено.');
    }

    public function destroy(ItemCategory $item_category)
    {
        $item_category->delete();

        return Redirect::back()->with('success', 'Удален.');
    }

    public function restore(ItemCategory $item_category)
    {
        $item_category->restore();

        return Redirect::back()->with('success', 'Восстановлен.');
    }
}
