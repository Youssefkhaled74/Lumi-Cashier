<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Receipt Printer Configuration
    |--------------------------------------------------------------------------
    |
    | Configure thermal receipt printer settings for POS system
    |
    */

    'enabled' => env('PRINTER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Auto-Print Settings
    |--------------------------------------------------------------------------
    */

    'auto_print' => env('PRINTER_AUTO_PRINT', false),
    
    'auto_print_on' => [
        'order_completed' => env('PRINTER_AUTO_PRINT_ON_ORDER', true),
        'order_paid' => env('PRINTER_AUTO_PRINT_ON_PAYMENT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Printer Connection
    |--------------------------------------------------------------------------
    |
    | Supported: 'network', 'usb', 'bluetooth', 'browser'
    |
    */

    'connection_type' => env('PRINTER_CONNECTION_TYPE', 'browser'),

    // Network printer settings
    'network' => [
        'ip' => env('PRINTER_IP', '192.168.1.100'),
        'port' => env('PRINTER_PORT', 9100),
    ],

    // USB printer settings
    'usb' => [
        'vendor_id' => env('PRINTER_USB_VENDOR_ID', null),
        'product_id' => env('PRINTER_USB_PRODUCT_ID', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Paper Settings
    |--------------------------------------------------------------------------
    |
    | Paper sizes: '80mm', '58mm', '76mm'
    |
    */

    'paper_size' => env('PRINTER_PAPER_SIZE', '80mm'),
    
    'paper_width' => [
        '58mm' => [165.35, 841.89],  // points
        '76mm' => [215.43, 841.89],
        '80mm' => [226.77, 841.89],
        '90mm' => [255.12, 841.89],
    ],

    /*
    |--------------------------------------------------------------------------
    | Receipt Template
    |--------------------------------------------------------------------------
    */

    'template' => env('PRINTER_TEMPLATE', 'invoices.show'),

    'logo' => [
        'enabled' => env('PRINTER_LOGO_ENABLED', false),
        'path' => env('PRINTER_LOGO_PATH', null),
        'width' => env('PRINTER_LOGO_WIDTH', '60mm'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Print Options
    |--------------------------------------------------------------------------
    */

    'options' => [
        'copies' => env('PRINTER_COPIES', 1),
        'cut_paper' => env('PRINTER_CUT_PAPER', true),
        'open_drawer' => env('PRINTER_OPEN_DRAWER', false),
        'beep' => env('PRINTER_BEEP', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Receipt Content
    |--------------------------------------------------------------------------
    */

    'show_barcode' => env('PRINTER_SHOW_BARCODE', true),
    'show_customer_info' => env('PRINTER_SHOW_CUSTOMER', true),
    'show_notes' => env('PRINTER_SHOW_NOTES', true),
    'show_footer' => env('PRINTER_SHOW_FOOTER', true),

    /*
    |--------------------------------------------------------------------------
    | ESC/POS Commands
    |--------------------------------------------------------------------------
    |
    | ESC/POS command sequences for thermal printers
    |
    */

    'escpos' => [
        'init' => "\x1B\x40",              // Initialize printer
        'cut' => "\x1D\x56\x00",           // Full cut
        'partial_cut' => "\x1D\x56\x01",  // Partial cut
        'drawer' => "\x1B\x70\x00\x19\xFA", // Open cash drawer
        'beep' => "\x1B\x42\x05\x09",      // Beep
        'align_left' => "\x1B\x61\x00",
        'align_center' => "\x1B\x61\x01",
        'align_right' => "\x1B\x61\x02",
        'bold_on' => "\x1B\x45\x01",
        'bold_off' => "\x1B\x45\x00",
        'font_a' => "\x1B\x4D\x00",        // 12x24 font
        'font_b' => "\x1B\x4D\x01",        // 9x17 font
        'double_width' => "\x1B\x21\x20",
        'double_height' => "\x1B\x21\x10",
        'normal' => "\x1B\x21\x00",
    ],

];
