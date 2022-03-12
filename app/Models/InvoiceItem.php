<?php

namespace App\Models;

use App\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SpatieLogsActivity;

    protected $fillable = [
        'invoice_id', 'name', 'item_id', 'count', 'price', 'measurement', 'measurement_id',
    ];

    public const FLOAT_TO_INT_PRICE = 100;
    public const FLOAT_TO_INT_COUNT = 10000;

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
