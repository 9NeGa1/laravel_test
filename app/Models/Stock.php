<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    protected $fillable = [
        'date',
        'last_change_date',
        'supplier_article',
        'tech_size',
        'barcode',
        'quantity',
        'is_supply',
        'is_realization',
        'quantity_full',
        'warehouse_name',
        'in_way_to_client',
        'in_way_from_client',
        'nm_id',
        'subject',
        'category',
        'brand',
        'sc_code',
        'price',
        'discount'
    ];

    // Связи
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'warehouse_name', 'warehouse_name');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'warehouse_name', 'warehouse_name');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class, 'warehouse_name', 'warehouse_name');
    }
}
