<?php

namespace App\Models;

use App\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SpatieLogsActivity;

    protected $fillable = [
        'name', 'item_category_id', 'measurement_id', 'sort',
    ];

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function measurement()
    {
        return $this->belongsTo(Measurement::class);
    }

    public function estimate()
    {
        return $this->hasOne(Estimate::class, 'item_id');
    }

    public function itemCategory()
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->when($filters['item_category_id'] ?? null, function ($query, $search) {
            $query->where('item_category_id', $search);
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })->orderByDesc('sort')->orderBy('name');
    }
}
