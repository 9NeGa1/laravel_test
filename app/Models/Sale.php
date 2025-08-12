<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
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
        'is_supply',
        'is_realization',
        'promo_code_discount',
        'warehouse_name',
        'country_name',
        'oblast_okrug_name',
        'region_name',
        'income_id',
        'sale_id',
        'odid',
        'spp',
        'for_pay',
        'finished_price',
        'price_with_disc',
        'nm_id',
        'subject',
        'category',
        'brand',
        'is_storno'
    ];

    // Связи
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'warehouse_name', 'warehouse_name');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'income_id', 'income_id');
    }

    public function income(): BelongsTo
    {
        return $this->belongsTo(Income::class, 'income_id', 'income_id');
    }
}
