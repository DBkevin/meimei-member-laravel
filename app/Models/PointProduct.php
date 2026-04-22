<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\ProductStatus;

class PointProduct extends Model
{
    protected $table = 'point_products';

    protected $fillable = [
        'name',
        'cover_url',
        'category',
        'points_price',
        'stock',
        'status',
        'sort',
        'description',
    ];

    protected $casts = [
        'points_price' => 'integer',
        'stock' => 'integer',
        'status' => ProductStatus::class,
        'sort' => 'integer',
    ];

    /**
     * 关系：多个兑换订单
     */
    public function redemptionOrders(): HasMany
    {
        return $this->hasMany(RedemptionOrder::class);
    }
}