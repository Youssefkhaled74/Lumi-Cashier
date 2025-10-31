// Receipt Printer JavaScript Library
class ReceiptPrinter {
    constructor(options = {}) {
        this.config = {
            autoConnect: options.autoConnect ?? true,
            paperSize: options.paperSize ?? '80mm',
            autoPrint: options.autoPrint ?? false,
            ...options
        };
        
        this.printer = null;
        this.connected = false;
        this.printQueue = [];
        
        if (this.config.autoConnect) {
            this.init();
        }
    }

    // Initialize printer connection
    async init() {
        console.log('[Printer] Initializing...');
        
        // Check for browser print support
        if (!window.print) {
            console.error('[Printer] Browser printing not supported');
            return false;
        }
        
        this.connected = true;
        console.log('[Printer] Ready');
        return true;
    }

    // Print receipt from URL
    async printReceipt(receiptUrl, options = {}) {
        try {
            console.log('[Printer] Printing receipt:', receiptUrl);
            
            const printOptions = {
                copies: options.copies ?? this.config.copies ?? 1,
                openDrawer: options.openDrawer ?? false,
                autoCut: options.autoCut ?? true,
            };

            // Open receipt in new window
            const printWindow = window.open(receiptUrl, '_blank', 'width=800,height=600');
            
            if (!printWindow) {
                throw new Error('Print window blocked. Please allow popups.');
            }

            // Wait for content to load
            printWindow.onload = () => {
                setTimeout(() => {
                    printWindow.print();
                    
                    // Close after printing
                    setTimeout(() => {
                        printWindow.close();
                    }, 500);
                }, 100);
            };

            return true;
        } catch (error) {
            console.error('[Printer] Print failed:', error);
            throw error;
        }
    }

    // Print order receipt
    async printOrder(orderId, options = {}) {
        const receiptUrl = `/admin/orders/${orderId}/invoice`;
        return await this.printReceipt(receiptUrl, options);
    }

    // Direct browser print
    printDirect(content) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(content);
        printWindow.document.close();
        
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    }

    // Silent print (auto-print without dialog)
    async silentPrint(receiptUrl) {
        try {
            // Create hidden iframe
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = receiptUrl;
            document.body.appendChild(iframe);

            iframe.onload = () => {
                try {
                    iframe.contentWindow.print();
                    
                    // Remove iframe after print
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                    }, 1000);
                } catch (error) {
                    console.error('[Printer] Silent print failed:', error);
                    document.body.removeChild(iframe);
                }
            };

            return true;
        } catch (error) {
            console.error('[Printer] Silent print error:', error);
            return false;
        }
    }

    // Test print
    async testPrint() {
        const testReceipt = this.generateTestReceipt();
        this.printDirect(testReceipt);
    }

    // Generate test receipt
    generateTestReceipt() {
        return `
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Receipt</title>
    <style>
        @page { margin: 0; size: ${this.config.paperSize} auto; }
        body { font-family: 'Courier New', monospace; font-size: 12px; width: ${this.config.paperSize}; margin: 0 auto; padding: 10mm; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 2px dashed #000; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="center bold" style="font-size: 16px; margin-bottom: 10px;">PRINTER TEST</div>
    <div class="center">Lumi POS System</div>
    <div class="divider"></div>
    <div>Date: ${new Date().toLocaleString()}</div>
    <div>Paper Size: ${this.config.paperSize}</div>
    <div>Status: Connected ✓</div>
    <div class="divider"></div>
    <div class="center">Test Print Successful!</div>
    <div style="margin-top: 20px;"></div>
</body>
</html>`;
    }

    // Check printer status
    getStatus() {
        return {
            connected: this.connected,
            paperSize: this.config.paperSize,
            autoPrint: this.config.autoPrint,
            queueLength: this.printQueue.length
        };
    }

    // Configure printer
    configure(options) {
        this.config = { ...this.config, ...options };
        console.log('[Printer] Configuration updated:', this.config);
    }
}

// ESC/POS Thermal Printer Support (for direct USB/Network printing)
class ESCPOSPrinter extends ReceiptPrinter {
    constructor(options = {}) {
        super(options);
        this.encoder = new TextEncoder();
    }

    // ESC/POS Commands
    get commands() {
        return {
            INIT: '\x1B\x40',
            CUT: '\x1D\x56\x00',
            PARTIAL_CUT: '\x1D\x56\x01',
            DRAWER: '\x1B\x70\x00\x19\xFA',
            BEEP: '\x1B\x42\x05\x09',
            ALIGN_LEFT: '\x1B\x61\x00',
            ALIGN_CENTER: '\x1B\x61\x01',
            ALIGN_RIGHT: '\x1B\x61\x02',
            BOLD_ON: '\x1B\x45\x01',
            BOLD_OFF: '\x1B\x45\x00',
            NORMAL: '\x1B\x21\x00',
            DOUBLE_WIDTH: '\x1B\x21\x20',
            DOUBLE_HEIGHT: '\x1B\x21\x10',
        };
    }

    // Connect to network printer
    async connectNetwork(ip, port = 9100) {
        console.log(`[ESC/POS] Connecting to ${ip}:${port}...`);
        // Network printing requires server-side implementation
        this.config.ip = ip;
        this.config.port = port;
        this.connected = true;
        return true;
    }

    // Connect to USB printer
    async connectUSB() {
        if (!navigator.usb) {
            throw new Error('WebUSB not supported in this browser');
        }

        try {
            const device = await navigator.usb.requestDevice({ filters: [] });
            await device.open();
            await device.selectConfiguration(1);
            await device.claimInterface(0);
            
            this.printer = device;
            this.connected = true;
            console.log('[ESC/POS] USB printer connected');
            return true;
        } catch (error) {
            console.error('[ESC/POS] USB connection failed:', error);
            throw error;
        }
    }

    // Open cash drawer
    async openDrawer() {
        if (!this.connected) return false;
        return await this.sendCommand(this.commands.DRAWER);
    }

    // Send raw ESC/POS command
    async sendCommand(command) {
        if (!this.printer) {
            console.warn('[ESC/POS] No printer connected');
            return false;
        }

        try {
            const data = this.encoder.encode(command);
            await this.printer.transferOut(1, data);
            return true;
        } catch (error) {
            console.error('[ESC/POS] Command failed:', error);
            return false;
        }
    }
}

// Auto-print functionality
function setupAutoPrint() {
    const autoPrintEnabled = document.querySelector('[data-auto-print]')?.dataset.autoPrint === 'true';
    
    if (autoPrintEnabled) {
        console.log('[Auto-Print] Enabled');
        
        // Listen for order completion events
        document.addEventListener('orderCompleted', (event) => {
            const orderId = event.detail.orderId;
            console.log('[Auto-Print] Order completed:', orderId);
            
            if (window.printer) {
                setTimeout(() => {
                    window.printer.printOrder(orderId);
                }, 500);
            }
        });
    }
}

// Initialize global printer instance
if (typeof window !== 'undefined') {
    window.ReceiptPrinter = ReceiptPrinter;
    window.ESCPOSPrinter = ESCPOSPrinter;
    
    // Auto-initialize
    document.addEventListener('DOMContentLoaded', () => {
        const printerConfig = window.printerConfig || {};
        window.printer = new ReceiptPrinter(printerConfig);
        setupAutoPrint();
    });
}
