<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopSettings extends Model
{
    protected $fillable = [
        'shop_name',
        'shop_name_en',
        'logo_path',
        'phone',
        'address',
        'address_en',
        'tax_enabled',
        'tax_percentage',
        'tax_label',
    ];

    /**
     * Boot the model and clear cache on update
     */
    protected static function booted()
    {
        static::saved(function () {
            cache()->forget('shop_settings');
        });
        
        static::deleted(function () {
            cache()->forget('shop_settings');
        });
    }

    /**
     * الحصول على إعدادات المحل الحالية
     */
    public static function current()
    {
        // Cache the shop settings for 1 hour to prevent repeated DB queries
        return cache()->remember('shop_settings', 3600, function () {
            return static::first() ?? static::create([
                'shop_name' => 'نظام لومي POS',
                'shop_name_en' => 'Lumi POS System',
                'tax_enabled' => true,
                'tax_percentage' => 15,
                'tax_label' => 'VAT',
            ]);
        });
    }

    /**
     * الحصول على رابط اللوجو الكامل
     */
    public function getLogoUrlAttribute()
    {
        // Avoid calling Storage::disk(...)->exists() when the php_fileinfo extension
        // is not available because Flysystem's MIME detectors may try to instantiate
        // the finfo class and crash (observed on some PHP builds).
        if ($this->logo_path) {
            if (extension_loaded('fileinfo')) {
                if (Storage::disk('public')->exists($this->logo_path)) {
                    return Storage::url($this->logo_path);
                }
            } else {
                // Fallback: check the file directly on disk and construct a public URL
                $full = storage_path('app/public/' . $this->logo_path);
                if (file_exists($full)) {
                    // Public URL should be /storage/{path} when `php artisan storage:link` is used
                    return asset('storage/' . ltrim($this->logo_path, '/'));
                }
            }
        }
        return null;
    }

    /**
     * الحصول على اسم المحل حسب اللغة
     */
    public function getShopNameLocalizedAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->shop_name : $this->shop_name_en;
    }

    /**
     * الحصول على العنوان حسب اللغة
     */
    public function getAddressLocalizedAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->address : $this->address_en;
    }
}
