<?php

namespace App\Models;

use App\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SpatieLogsActivity;

    protected $fillable = [
        'name', 'sort'
    ];
}
