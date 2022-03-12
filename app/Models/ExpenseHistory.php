<?php

namespace App\Models;

use App\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseHistory extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SpatieLogsActivity;
    
    public const FLOAT_TO_INT_PRICE = 100;

    protected $fillable = [
        'expense_id', 'user_id', 'name', 'price', 'date'
    ];

    protected $dates = [
        'date',
    ];
    
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
