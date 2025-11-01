<?php

namespace App\Http\Controllers;

use App\Models\ShopSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ShopSettingsController extends Controller
{
    private const ADMIN_PASSWORD = 'lumi#8080';

    /**
     * عرض صفحة إعدادات المحل
     */
    public function index()
    {
        $settings = ShopSettings::current();
        return view('settings.shop', compact('settings'));
    }

    /**
     * التحقق من كلمة المرور
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if ($request->password === self::ADMIN_PASSWORD) {
            session(['shop_settings_unlocked' => true]);
            return response()->json([
                'success' => true,
                'message' => __('messages.password_correct'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('messages.password_incorrect'),
        ], 403);
    }

    /**
     * تحديث إعدادات المحل
     */
    public function update(Request $request)
    {
        // التحقق من فتح الإعدادات
        if (!session('shop_settings_unlocked')) {
            return redirect()->back()->with('error', __('messages.please_unlock_settings'));
        }

        // Build validation rules. If the php_fileinfo extension is missing, avoid mime-type guessing
        // which Symfony's MIME guesser relies on (prevents a crash on some PHP builds).
        $rules = [
            'shop_name' => 'required|string|max:255',
            'shop_name_en' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'address_en' => 'nullable|string|max:500',
            'tax_enabled' => 'nullable|boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_label' => 'nullable|string|max:50',
        ];

        if (extension_loaded('fileinfo')) {
            $rules['logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        } else {
            // Fall back to a basic file size check when fileinfo isn't available to avoid the
            // Symfony\Mime exception: "Unable to guess the MIME type as no guessers are available".
            Log::warning('ShopSettingsController::update - php_fileinfo extension not available; skipping MIME checks for logo upload.');
            $rules['logo'] = 'nullable|file|max:2048';
        }

        $request->validate($rules);

        $settings = ShopSettings::current();

        // حفظ البيانات
        $settings->shop_name = $request->shop_name;
        $settings->shop_name_en = $request->shop_name_en;
        $settings->phone = $request->phone;
        $settings->address = $request->address;
        $settings->address_en = $request->address_en;
    // Tax settings
    $settings->tax_enabled = $request->has('tax_enabled') ? (bool) $request->tax_enabled : true;
    $settings->tax_percentage = $request->tax_percentage !== null ? $request->tax_percentage : $settings->tax_percentage ?? config('cashier.tax.default_rate', 15);
    $settings->tax_label = $request->tax_label ?? $settings->tax_label ?? config('cashier.tax.label', 'VAT');

        // رفع اللوجو إذا تم تحديد ملف جديد
        if ($request->hasFile('logo')) {
            // حذف اللوجو القديم إن وجد
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            // رفع اللوجو الجديد
            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings->logo_path = $logoPath;
        }

        $settings->save();

        // إلغاء قفل الإعدادات بعد الحفظ
        session()->forget('shop_settings_unlocked');

        return redirect()->back()->with('success', __('messages.settings_updated_successfully'));
    }

    /**
     * حذف اللوجو
     */
    public function deleteLogo()
    {
        if (!session('shop_settings_unlocked')) {
            return response()->json([
                'success' => false,
                'message' => __('messages.please_unlock_settings'),
            ], 403);
        }

        $settings = ShopSettings::current();

        if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
            Storage::disk('public')->delete($settings->logo_path);
            $settings->logo_path = null;
            $settings->save();

            return response()->json([
                'success' => true,
                'message' => __('messages.logo_deleted_successfully'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('messages.no_logo_found'),
        ], 404);
    }
}
