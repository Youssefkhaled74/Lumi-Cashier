<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Day extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'opened_at',
        'closed_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the orders for the day.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get only completed orders for the day.
     */
    public function completedOrders(): HasMany
    {
        return $this->orders()->where('status', Order::STATUS_COMPLETED);
    }

    /**
     * Check if the day is currently open.
     */
    public function getIsOpenAttribute(): bool
    {
        return $this->opened_at !== null && $this->closed_at === null;
    }

    /**
     * Check if the day is closed.
     */
    public function getIsClosedAttribute(): bool
    {
        return $this->closed_at !== null;
    }

    /**
     * Get the total sales for the day (completed orders only).
     */
    public function getTotalSalesAttribute(): float
    {
        return (float) $this->completedOrders()->sum('total');
    }

    /**
     * Get the total number of completed orders.
     */
    public function getTotalOrdersAttribute(): int
    {
        return $this->completedOrders()->count();
    }

    /**
     * Get the duration the day was open (in hours).
     */
    public function getDurationInHoursAttribute(): ?float
    {
        if (!$this->opened_at) {
            return null;
        }

        $endTime = $this->closed_at ?? now();
        return $this->opened_at->diffInHours($endTime, true);
    }

    /**
     * Open the day.
     * 
     * @return bool
     */
    public function open(): bool
    {
        if ($this->is_open) {
            return false;
        }

        $this->opened_at = now();
        $this->closed_at = null;
        return $this->save();
    }

    /**
     * Close the day.
     * 
     * @return bool
     */
    public function close(): bool
    {
        if (!$this->is_open) {
            return false;
        }

        $this->closed_at = now();
        return $this->save();
    }

    /**
     * Scope a query to only include open days.
     */
    public function scopeOpen($query)
    {
        return $query->whereNotNull('opened_at')->whereNull('closed_at');
    }

    /**
     * Scope a query to only include closed days.
     */
    public function scopeClosed($query)
    {
        return $query->whereNotNull('closed_at');
    }

    /**
     * Scope a query to get the current open day.
     */
    public function scopeCurrent($query)
    {
        return $query->open()->whereDate('date', today());
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Get or create today's day record.
     * 
     * @return static
     */
    public static function getOrCreateToday(): self
    {
        return static::firstOrCreate(
            ['date' => today()],
            ['opened_at' => now()]
        );
    }
}
