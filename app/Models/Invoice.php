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
        'organization_id', 'name', 'status', 'date', 'supplier', 'accepted', 'file'
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
}
