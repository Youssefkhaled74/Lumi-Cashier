# Translation Testing Guide

## 🧪 How to Test the Translation System

### Quick Test Steps:

1. **Start your Laravel server** (if not already running):
   ```powershell
   php artisan serve
   ```

2. **Open the application**:
   - Navigate to: `http://127.0.0.1:8000`
   - Login with your credentials

3. **Test Language Switching**:
   - Look at the top navigation bar
   - You'll see **EN** and **عربي** buttons
   - Click **EN** → Everything should be in English
   - Click **عربي** → Everything should switch to Arabic (RTL layout)

---

## 📋 Pages to Test

### 1. Day Status Page
**URL:** `http://127.0.0.1:8000/admin/day/status`

**What to check:**
- [ ] Page title changes language
- [ ] "Back to Dashboard" button translates
- [ ] If day is open:
  - [ ] "Current Day" card labels translate
  - [ ] "Today's Performance" card translates
  - [ ] All 3 action buttons translate
- [ ] If no day is open:
  - [ ] "No Active Day" message translates
  - [ ] "Open New Day" button translates
  - [ ] Info cards translate

**Test both languages:**
- Click **EN** → Verify all text is in English
- Click **عربي** → Verify all text is in Arabic + RTL layout

---

### 2. Reports Page
**URL:** `http://127.0.0.1:8000/admin/reports`

**What to check:**
- [ ] Page title translates
- [ ] "From Date" and "To Date" labels translate
- [ ] "Generate Report" button translates
- [ ] "Reset" button translates
- [ ] Before generating report:
  - [ ] "Ready to Generate Report" message translates
  - [ ] All 3 info cards translate
- [ ] After generating report:
  - [ ] Summary cards labels translate
  - [ ] Inventory overview section translates
  - [ ] Top selling items table headers translate

**Test both languages:**
- Click **EN** → All should be in English
- Click **عربي** → All should be in Arabic

---

### 3. Days History Page
**URL:** `http://127.0.0.1:8000/admin/days`

**What to check:**
- [ ] Page title "Business Days History" translates
- [ ] "Current Day Status" button translates
- [ ] All table column headers translate
- [ ] "Open"/"Closed" status badges translate
- [ ] If no days exist:
  - [ ] Empty state message translates
  - [ ] "Go to Day Status" button translates

**Test both languages:**
- Click **EN** → Table and all labels in English
- Click **عربي** → Table and all labels in Arabic

---

## ✅ Expected Behavior

### English Mode (EN):
- All text displays in English
- Layout is Left-to-Right (LTR)
- Numbers and dates in English format
- Font: Inter (sans-serif)

### Arabic Mode (عربي):
- All text displays in Arabic
- Layout is Right-to-Left (RTL)
- Numbers in Arabic numerals
- Font: Tajawal (Arabic font)
- Icons remain on correct side for RTL

---

## 🐛 Troubleshooting

### If translations don't appear:

1. **Clear all caches:**
   ```powershell
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

2. **Hard refresh browser:**
   - Press `Ctrl + Shift + R` (Chrome/Edge)
   - Or `Ctrl + F5` (Firefox)

3. **Check language is set:**
   - Look at the EN/عربي buttons
   - The active language should be highlighted

### If you see literal text like "messages.day_status":

This means the translation key is missing. Check:
1. Translation file exists at `resources/lang/en/messages.php` or `resources/lang/ar/messages.php`
2. The key exists in the file
3. Caches have been cleared

---

## 📸 Visual Checklist

### Day Status Page - English
```
Day Status                              Back to Dashboard
────────────────────────────────────────────────────────

[Current Day Card]      [Today's Performance Card]
Opened At: 9:00 AM      Total Sales: $500.00
Duration: 2.5 hrs       Total Orders: 15
Day ID: #5              Average Order: $33.33

[View Orders]  [Manage Inventory]  [Close Day]
```

### Day Status Page - Arabic (RTL)
```
العودة للوحة التحكم                              حالة اليوم
────────────────────────────────────────────────────────

    [بطاقة أداء اليوم]      [بطاقة اليوم الحالي]
          ٥٠٠٫٠٠$ :إجمالي المبيعات      ٠٩:٠٠ ص :وقت الفتح
                  ١٥ :إجمالي الطلبات      ساعة ٢٫٥ :المدة
             ٣٣٫٣٣$ :متوسط الطلب      #٥ :رقم اليوم

        [إغلاق اليوم]  [إدارة المخزون]  [عرض الطلبات]
```

---

## 🎯 Success Criteria

✅ **Translation is successful if:**
1. No English text appears when Arabic is selected
2. No Arabic text appears when English is selected
3. No literal translation keys (like "messages.xxx") are visible
4. Layout properly reverses for RTL in Arabic mode
5. All buttons, labels, headings, and messages translate
6. Language preference persists during navigation

---

## 📞 Support

If you encounter issues:
1. Clear all caches
2. Check browser console for errors
3. Verify translation files exist
4. Ensure you're on the latest code version

---

**Happy Testing! 🎉**
