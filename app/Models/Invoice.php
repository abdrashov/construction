<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_id', 'name', 'pay', 'status', 'date', 'supplier', 'accepted', 'supplier_id', 'file'
    ];

    protected $dates = [
        'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->when($filters['pay'] ?? null, function ($query, $search) {
            $query->where('pay', $search === '0000' ? 0 : $search);
        });
    }
}
