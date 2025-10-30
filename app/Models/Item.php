<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'barcode',
        'description',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the category that owns the item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the units for the item.
     */
    public function units(): HasMany
    {
        return $this->hasMany(ItemUnit::class);
    }

    /**
     * Get all order items for this item through item units.
     */
    public function orderItems(): HasManyThrough
    {
        return $this->hasManyThrough(OrderItem::class, ItemUnit::class);
    }

    /**
     * Get the total available quantity across all units.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->units()->sum('quantity');
    }

    /**
     * Get the total value of inventory (quantity * price) across all units.
     */
    public function getInventoryValueAttribute(): float
    {
        return (float) $this->units()->get()->sum(function ($unit) {
            return $unit->quantity * $unit->price;
        });
    }

    /**
     * Check if the item is in stock.
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->total_quantity > 0;
    }

    /**
     * Get the count of available (unsold) units.
     */
    public function getAvailableStockAttribute(): int
    {
        return $this->units()->where('status', ItemUnit::STATUS_AVAILABLE)->count();
    }

    /**
     * Scope a query to only include items in stock.
     */
    public function scopeInStock($query)
    {
        return $query->whereHas('units', function ($q) {
            $q->where('status', ItemUnit::STATUS_AVAILABLE);
        });
    }

    /**
     * Scope a query to only include items by category.
     */
    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
