<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Income extends Model
{
    protected $fillable = [
        'income_id',
        'number',
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'quantity',
        'total_price',
        'date_close',
        'warehouse_name',
        'nm_id'
    ];

    // Связи
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'warehouse_name', 'warehouse_name');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'income_id', 'income_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'income_id', 'income_id');
    }
}
