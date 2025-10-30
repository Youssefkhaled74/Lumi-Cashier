<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'item_unit_id',
        'quantity',
        'price',
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot the model and add event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically calculate total before saving
        static::saving(function ($orderItem) {
            $orderItem->total = $orderItem->quantity * $orderItem->price;
        });
    }

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the item unit that this order item references.
     */
    public function itemUnit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class);
    }

    /**
     * Get the item through the item unit.
     */
    public function item(): BelongsTo
    {
        return $this->itemUnit->item();
    }

    /**
     * Get the item name through relationships.
     */
    public function getItemNameAttribute(): string
    {
        return $this->itemUnit?->item?->name ?? 'N/A';
    }

    /**
     * Get the item barcode through relationships.
     */
    public function getBarcodeAttribute(): string
    {
        return $this->itemUnit?->barcode ?? 'N/A';
    }

    /**
     * Calculate the line total (quantity * price).
     * 
     * @return float
     */
    public function calculateTotal(): float
    {
        return (float) ($this->quantity * $this->price);
    }

    /**
     * Get the subtotal (alias for total).
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->total;
    }
}
