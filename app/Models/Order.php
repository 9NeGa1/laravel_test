<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'g_number',
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'total_price',
        'discount_percent',
        'warehouse_name',
        'oblast',
        'income_id',
        'odid',
        'nm_id',
        'subject',
        'category',
        'brand',
        'is_cancel',
        'cancel_dt'
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

    public function income(): BelongsTo
    {
        return $this->belongsTo(Income::class, 'income_id', 'income_id');
    }
}
