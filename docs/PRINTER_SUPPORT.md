# Receipt Printer Support - Implementation Summary

## 📋 Overview
Complete receipt printer support system with direct printing capabilities, thermal printer integration, and auto-print functionality for the Lumi POS system.

## ✅ Completed Features

### 1. **Printer Configuration System**
**File:** `config/printer.php`

- **Connection Types:**
  - Browser printing (default)
  - Network printers (IP/port)
  - USB printers (WebUSB API)
  - Bluetooth printers

- **Paper Sizes:**
  - 58mm (164.41pt)
  - 76mm (215.43pt)
  - 80mm (226.77pt)
  - 90mm (255.12pt) - Current receipt format

- **Auto-Print Settings:**
  - Trigger on order completion
  - Trigger on order payment
  - Configurable delay
  - Number of copies

- **ESC/POS Commands:**
  - Initialize printer
  - Paper cutting
  - Cash drawer control
  - Text alignment (left/center/right)
  - Text formatting (bold, underline, double size)
  - Font selection

### 2. **JavaScript Printer Library**
**File:** `public/js/printer.js`

- **ReceiptPrinter Class:**
  - Browser-based printing via window.print()
  - Silent print using hidden iframes
  - Print queue management
  - Auto-print event handling
  - Test receipt generation
  - Configuration loading from localStorage

- **ESCPOSPrinter Class (extends ReceiptPrinter):**
  - USB printer connection via WebUSB API
  - Network printer connection via IP/port
  - ESC/POS command encoding
  - Cash drawer control
  - Paper cutting control
  - Raw thermal printing

### 3. **Printer Settings UI Component**
**File:** `resources/views/components/printer-settings.blade.php`

Alpine.js component featuring:
- Real-time printer status indicator
- Auto-print toggle switches
- Paper size selector
- Connection type selector (browser/network/USB/bluetooth)
- Network printer configuration (IP/port)
- USB printer connection button
- Receipt content toggles:
  - Show/hide barcode
  - Show/hide customer info
  - Show/hide order notes
  - Show/hide footer
- Print options:
  - Number of copies
  - Auto-cut paper
  - Open cash drawer
  - Beep on completion
- LocalStorage persistence
- Test print functionality
- Reset to defaults

### 4. **Print Button Component**
**File:** `resources/views/components/print-button.blade.php`

Reusable Blade component with:
- Print receipt functionality
- Auto-print on page load (configurable)
- Customizable label and icon
- Fallback to window.open if printer.js unavailable
- Order ID binding

### 5. **Printer Settings Page**
**File:** `resources/views/admin/settings/printer.blade.php`

Complete admin interface with:
- Printer status dashboard:
  - Connection status
  - Paper size display
  - Auto-print status
  - Last print timestamp
- Quick actions:
  - Test print button
  - Reset settings button
  - View recent orders link
  - Printer setup guide
- Integration with printer-settings component
- Real-time status updates
- Notification system

### 6. **Integration into Main Application**

#### Layout Integration
**File:** `resources/views/layouts/admin.blade.php`
- Added printer.js script inclusion
- Added printerConfig JavaScript object from backend config
- Loaded after jQuery and DataTables

#### Order Details Page
**File:** `resources/views/admin/orders/show.blade.php`
- Added print button using `<x-print-button>` component
- Auto-print trigger on order completion
- Custom event dispatch: `orderCompleted`
- Session-based print tracking to avoid duplicates

#### Settings Page
**File:** `resources/views/admin/settings/index.blade.php`
- Added "Printer Settings" quick link card
- Navigation to `/admin/settings/printer`
- Icon and description

#### Routes
**File:** `routes/web.php`
- Added route: `GET /admin/settings/printer` → `settings.printer`

#### Controller
**File:** `app/Http/Controllers/SettingsController.php`
- Added `printer()` method
- Returns printer settings view

## 🔧 Usage Instructions

### For Administrators

#### 1. Access Printer Settings
1. Navigate to **Admin → Settings**
2. Click on **"Printer Settings"** card
3. Configure your printer preferences

#### 2. Browser Printing (Default)
- No setup required
- Works with any installed printer
- Best for PDF receipts
- Click "Print Receipt" on any order

#### 3. Network Thermal Printer
1. Go to Printer Settings
2. Select **"Network Printer"** from connection type
3. Enter printer IP address (e.g., `192.168.1.100`)
4. Enter port (default: `9100`)
5. Click **"Save Settings"**
6. Click **"Test Print"** to verify

#### 4. USB Thermal Printer
1. Go to Printer Settings
2. Select **"USB Printer"** from connection type
3. Click **"Connect USB Printer"**
4. Select your printer from the browser dialog
5. Grant permission
6. Click **"Test Print"** to verify

**Note:** USB printing requires Chrome or Edge browser (WebUSB API support)

#### 5. Auto-Print Configuration
1. Go to Printer Settings
2. Enable **"Auto-print receipts"**
3. Check triggers:
   - ✅ On order completion
   - ✅ On order payment
4. Set number of copies (1-5)
5. Configure print options:
   - Auto-cut paper
   - Open cash drawer
   - Beep on completion
6. Save settings

### For Developers

#### Print a Receipt from Code
```javascript
// Print specific order
window.printer.printOrder(orderId);

// Print current page receipt
window.printer.printReceipt('/admin/orders/123/invoice');

// Silent print (no preview)
window.printer.silentPrint('/admin/orders/123/invoice');

// Test print
window.printer.testPrint();
```

#### Trigger Auto-Print Event
```javascript
document.dispatchEvent(new CustomEvent('orderCompleted', {
    detail: { 
        orderId: 123,
        total: 99.99,
        timestamp: new Date().toISOString()
    }
}));
```

#### Access Printer Settings
```javascript
// Get current settings
const settings = window.printer.getSettings();

// Update settings
window.printer.updateSettings({
    autoPrint: true,
    paperSize: '80mm',
    connectionType: 'network'
});
```

#### Use Print Button Component
```blade
<x-print-button 
    :orderId="$order->id" 
    label="Print Receipt" 
    icon="bi-printer" 
    class="btn btn-primary"
    :autoPrint="true"
/>
```

## 📁 File Structure

```
config/
  └── printer.php                              # Printer configuration

public/js/
  └── printer.js                               # JavaScript printer library

resources/views/
  ├── components/
  │   ├── printer-settings.blade.php          # Alpine.js settings component
  │   └── print-button.blade.php              # Reusable print button
  ├── layouts/
  │   └── admin.blade.php                     # Updated with printer.js
  └── admin/
      ├── settings/
      │   ├── index.blade.php                 # Updated with printer link
      │   └── printer.blade.php               # Printer settings page
      └── orders/
          └── show.blade.php                  # Updated with print button

app/Http/Controllers/
  └── SettingsController.php                  # Added printer() method

routes/
  └── web.php                                 # Added printer settings route
```

## 🔌 Supported Printers

### Network Printers
- Epson TM-T20, TM-T88, TM-T82
- Star TSP100, TSP143, TSP650
- Citizen CT-S310II, CT-S601
- Any ESC/POS compatible thermal printer

### USB Printers
Requires WebUSB API support (Chrome/Edge):
- Same models as network printers
- Must support USB connection
- Driver installation may be required

### Browser Printing
- Any printer installed on the computer
- PDF generation via DomPDF
- Standard print dialog

## ⚙️ Configuration Options

### Auto-Print Triggers
```php
'auto_print_on' => [
    'order_completed' => true,   // Print when order is marked complete
    'order_paid' => true,        // Print when payment is received
]
```

### ESC/POS Commands
```php
'escpos' => [
    'init' => "\x1B\x40",              // Initialize printer
    'cut' => "\x1D\x56\x00",           // Full cut
    'drawer' => "\x1B\x70\x00\x19\xFA", // Open cash drawer
    'align_center' => "\x1B\x61\x01",  // Center alignment
    'bold_on' => "\x1B\x45\x01",       // Bold text
    // ... more commands
]
```

### Paper Sizes
```php
'paper_sizes' => [
    '90mm' => [
        'width_mm' => 90,
        'width_pt' => 255.12,  // Used by DomPDF
        'description' => '90mm thermal paper (wide)',
    ],
    // ... other sizes
]
```

## 🔍 Troubleshooting

### Issue: Print button does nothing
**Solution:** Check browser console for errors. Ensure printer.js is loaded:
```javascript
console.log(window.printer); // Should show ReceiptPrinter object
```

### Issue: USB printer not connecting
**Solution:**
1. Verify browser is Chrome or Edge
2. Check USB cable connection
3. Ensure printer is powered on
4. Grant browser permission when prompted

### Issue: Network printer not responding
**Solution:**
1. Verify printer IP address (ping test)
2. Check port number (usually 9100)
3. Ensure printer is on same network
4. Check firewall settings

### Issue: Auto-print not working
**Solution:**
1. Go to Printer Settings
2. Verify "Auto-print" is enabled
3. Check selected triggers
4. Test with manual print first
5. Check browser console for errors

### Issue: Receipt cut off on right side
**Solution:**
1. Go to Printer Settings
2. Select larger paper size (90mm)
3. Test print to verify

### Issue: Barcode not printing
**Solution:**
1. Go to Printer Settings
2. Enable "Show barcode on receipt"
3. Ensure Milon Barcode package is installed
4. Check order has valid barcode

## 📊 Browser Compatibility

| Browser | Browser Print | USB Print | Network Print |
|---------|---------------|-----------|---------------|
| Chrome  | ✅            | ✅        | ✅            |
| Edge    | ✅            | ✅        | ✅            |
| Firefox | ✅            | ❌        | ✅            |
| Safari  | ✅            | ❌        | ✅            |

**Note:** USB printing requires WebUSB API (Chrome/Edge only)

## 🚀 Next Steps (Future Enhancements)

### Server-Side Print Service
- PHP library for ESC/POS (mike42/escpos-php)
- Background print queue
- Print job management
- Printer status monitoring API

### Advanced Features
- Multiple printer support (kitchen printer, receipt printer)
- Custom receipt templates
- Logo/image printing
- QR code generation
- Email receipt option
- SMS receipt option

### Analytics
- Print statistics dashboard
- Paper usage tracking
- Printer uptime monitoring
- Cost per receipt calculation

## 📝 Testing Checklist

- [x] Printer configuration loads correctly
- [x] JavaScript library initializes
- [x] Settings component renders
- [x] Print button component works
- [x] Browser printing functional
- [ ] Network printer connection (needs physical printer)
- [ ] USB printer connection (needs physical printer)
- [ ] Auto-print triggers correctly
- [x] Test print generates sample receipt
- [x] Settings persist in localStorage
- [x] Status indicators update
- [ ] ESC/POS commands work (needs thermal printer)
- [ ] Cash drawer opens (needs printer with drawer)
- [ ] Paper cutting works (needs thermal printer)

## 📞 Support

For issues or questions:
1. Check printer connection and power
2. Review browser console for errors
3. Test with different paper sizes
4. Verify printer compatibility
5. Contact system administrator

## 📄 License

Part of Lumi POS system - All rights reserved.

---

**Last Updated:** 2025
**Version:** 1.0.0
**Author:** Development Team
