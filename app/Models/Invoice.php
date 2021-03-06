<?php

namespace App\Models;

use App\SpatieLogsActivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SpatieLogsActivity;

    protected $fillable = [
        'organization_id', 'user_id', 'name', 'pay', 'status', 'date', 'supplier', 'accepted', 'supplier_id', 'file'
    ];

    protected $dates = [
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['name'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->when($filters['date'] ?? null, function ($query, $search) {
            $search = (new Carbon($search))->format('Y-m-d');
            $query->where('date', 'like', $search);
        })->when($filters['supplier_id'] ?? null, function ($query, $search) {
            $query->where('supplier_id', $search);
        })->when($filters['accepted'] ?? null, function ($query, $search) {
            $query->where('accepted', 'like', $search);
        })->when($filters['pay'] ?? null, function ($query, $search) {
            $query->where('pay', $search == "true" ? 1 : 0);
        })->when($filters['status'] ?? null, function ($query, $search) {
            $query->where('status', $search == "true" ? 1 : 0);
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
