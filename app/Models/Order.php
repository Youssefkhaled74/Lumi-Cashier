<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Order status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Payment method constants.
     */
    public const PAYMENT_CASH = 'cash';
    public const PAYMENT_CARD = 'card';
    public const PAYMENT_OTHER = 'other';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'day_id',
        'subtotal',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total',
        'status',
        'payment_method',
        'customer_name',
        'customer_phone',
        'customer_email',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
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
        'status' => self::STATUS_PENDING,
        'payment_method' => self::PAYMENT_CASH,
        'subtotal' => 0,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'tax_percentage' => 0,
        'tax_amount' => 0,
        'total' => 0,
    ];

    /**
     * Get the order items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Alias for items() for better readability.
     */
    public function orderItems(): HasMany
    {
        return $this->items();
    }

    /**
     * Get the day this order belongs to.
     */
    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    /**
     * Get all item units through order items.
     */
    public function itemUnits(): HasManyThrough
    {
        return $this->hasManyThrough(ItemUnit::class, OrderItem::class, 'order_id', 'id', 'id', 'item_unit_id');
    }

    /**
     * Calculate and update the order total from order items.
     * 
     * @return void
     */
    public function calculateTotal(): void
    {
        // Calculate subtotal from order items
        $this->subtotal = $this->items()->sum('total');
        
        // Calculate discount amount
        if ($this->discount_percentage > 0) {
            $this->discount_amount = ($this->subtotal * $this->discount_percentage) / 100;
        }
        
        // Calculate amount after discount
        $amountAfterDiscount = $this->subtotal - $this->discount_amount;
        
        // Calculate tax amount
        if ($this->tax_percentage > 0) {
            $this->tax_amount = ($amountAfterDiscount * $this->tax_percentage) / 100;
        }
        
        // Calculate final total
        $this->total = $amountAfterDiscount + $this->tax_amount;
        
        $this->save();
    }

    /**
     * Apply discount to order
     * 
     * @param float $percentage
     * @return void
     */
    public function applyDiscount(float $percentage): void
    {
        $this->discount_percentage = $percentage;
        $this->calculateTotal();
    }

    /**
     * Apply tax to order
     * 
     * @param float $percentage
     * @return void
     */
    public function applyTax(float $percentage): void
    {
        $this->tax_percentage = $percentage;
        $this->calculateTotal();
    }

    /**
     * Set payment method
     * 
     * @param string $method
     * @return void
     */
    public function setPaymentMethod(string $method): void
    {
        $this->payment_method = $method;
        $this->save();
    }

    /**
     * Get the total items count in this order.
     */
    public function getTotalItemsCountAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Get the total units count in this order.
     */
    public function getTotalUnitsCountAttribute(): int
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Check if the order is completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the order is pending.
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the order is cancelled.
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Mark the order as completed.
     * 
     * @return bool
     */
    public function markAsCompleted(): bool
    {
        $this->status = self::STATUS_COMPLETED;
        return $this->save();
    }

    /**
     * Mark the order as cancelled.
     * 
     * @return bool
     */
    public function markAsCancelled(): bool
    {
        $this->status = self::STATUS_CANCELLED;
        return $this->save();
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include orders for a specific day.
     */
    public function scopeForDay($query, int $dayId)
    {
        return $query->where('day_id', $dayId);
    }

    /**
     * Scope a query to only include orders within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
