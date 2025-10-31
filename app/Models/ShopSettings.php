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
    ];

    /**
     * الحصول على إعدادات المحل الحالية
     */
    public static function current()
    {
        return static::first() ?? static::create([
            'shop_name' => 'نظام لومي POS',
            'shop_name_en' => 'Lumi POS System',
        ]);
    }

    /**
     * الحصول على رابط اللوجو الكامل
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            return Storage::url($this->logo_path);
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
