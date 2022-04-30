<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\InvoiceItem;
use App\Models\Item;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class EstimateController extends Controller
{
    public function Index(Organization $organization)
    {
        $estimates = Estimate::where('organization_id', $organization->id)
            ->get()
            ->transform(fn ($estimate) => [
                'id' => $estimate->id,
                'name' => $estimate->item->name,
                'item_id' => $estimate->item_id,
                'count' => $estimate->count / InvoiceItem::FLOAT_TO_INT_COUNT,
                'measurement' => $estimate->measurement,
            ]);

        return Inertia::render('Estimate/Index', [
            'filters' => Request::only('begin'),
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
            ],
            'estimates' => $estimates,
            'items' => Item::filter(Request::only('search'))
                ->whereNotIn('id', $estimates->pluck('item_id'))
                ->with('measurement')
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'measurement' => $item->measurement ? $item->measurement->name : 'Удален!',
                ]),
        ]);
    }

    public function storeItem(Organization $organization, Item $item)
    {
        if (!Estimate::where('organization_id', $organization->id)->where('item_id', $item->id)->exists())
            Estimate::create([
                'organization_id' => $organization->id,
                'item_id' => $item->id,
                'measurement' => $item->measurement ? $item->measurement->name : 'Удален!',
            ]);

        $item->update(['sort' => $item->sort + 1]);

        return Redirect::back()->with('success', 'Товар, добавлено.');
    }

    public function update(Organization $organization)
    {
        Request::validate([
            'estimates.*.item_id' => ['required'],
            'estimates.*.count' => ['nullable', 'numeric', 'min:0', 'max:5000000000'],
        ]);


        foreach (Request::input('estimates') as $estimate) {
            Estimate::where('organization_id', $organization->id)
                ->where('item_id', $estimate['item_id'])
                ->firstOrFail()
                ->update([
                    'count' => $estimate['count'] * InvoiceItem::FLOAT_TO_INT_COUNT,
                ]);
        }

        return Redirect::back()->with('success', 'Успешно сохранено.');
    }
}
