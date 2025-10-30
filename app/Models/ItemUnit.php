<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemUnit extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Status constants.
     */
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_SOLD = 'sold';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'quantity',
        'price',
        'status',
        'order_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => self::STATUS_AVAILABLE,
        'quantity' => 1,
    ];

    /**
     * Get the item that owns the unit.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the order this unit belongs to (if sold).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get all order items that reference this unit.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the total quantity sold for this unit.
     */
    public function getTotalSoldAttribute(): int
    {
        return $this->orderItems()->sum('quantity');
    }

    /**
     * Get the available quantity (current stock minus sold).
     * 
     * Note: In a real system, quantity should be decremented on order creation.
     * This accessor is for display purposes.
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity);
    }

    /**
     * Check if this unit is available for sale.
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Decrease the quantity by the given amount.
     * 
     * @param int $amount
     * @return bool
     */
    public function decreaseQuantity(int $amount): bool
    {
        if ($this->quantity < $amount) {
            return false;
        }

        $this->quantity -= $amount;
        return $this->save();
    }

    /**
     * Increase the quantity by the given amount.
     * 
     * @param int $amount
     * @return bool
     */
    public function increaseQuantity(int $amount): bool
    {
        $this->quantity += $amount;
        return $this->save();
    }

    /**
     * Scope a query to only include units in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope a query to only include available units.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    /**
     * Scope a query to only include sold units.
     */
    public function scopeSold($query)
    {
        return $query->where('status', self::STATUS_SOLD);
    }

    /**
     * Scope a query to find by barcode.
     */
    public function scopeByBarcode($query, string $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Mark this unit as sold and link to order.
     */
    public function markAsSold(int $orderId): bool
    {
        $this->status = self::STATUS_SOLD;
        $this->order_id = $orderId;
        return $this->save();
    }
}
