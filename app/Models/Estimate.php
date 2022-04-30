<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'item_id',
        'count',
        'price',
        'measurement',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
