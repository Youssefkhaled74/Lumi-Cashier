{{-- Printer Settings Component --}}
<div class="printer-settings" x-data="printerSettings()">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-print"></i> Receipt Printer Settings
            </h5>
        </div>
        <div class="card-body">
            {{-- Printer Status --}}
            <div class="alert" :class="printerStatus.connected ? 'alert-success' : 'alert-warning'">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas" :class="printerStatus.connected ? 'fa-check-circle' : 'fa-exclamation-triangle'"></i>
                        <strong x-text="printerStatus.connected ? 'Printer Connected' : 'Printer Not Connected'"></strong>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" @click="testPrint()">
                        <i class="fas fa-file-invoice"></i> Test Print
                    </button>
                </div>
            </div>

            {{-- Auto-Print Settings --}}
            <div class="mb-4">
                <h6 class="border-bottom pb-2">Auto-Print Options</h6>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="autoPrintEnabled" x-model="settings.autoPrint">
                    <label class="form-check-label" for="autoPrintEnabled">
                        Enable Auto-Print
                    </label>
                </div>
                <div class="form-check form-switch mt-2" x-show="settings.autoPrint">
                    <input class="form-check-input" type="checkbox" id="autoPrintOnOrder" x-model="settings.autoPrintOnOrder">
                    <label class="form-check-label" for="autoPrintOnOrder">
                        Auto-print when order is completed
                    </label>
                </div>
                <div class="form-check form-switch mt-2" x-show="settings.autoPrint">
                    <input class="form-check-input" type="checkbox" id="autoPrintOnPayment" x-model="settings.autoPrintOnPayment">
                    <label class="form-check-label" for="autoPrintOnPayment">
                        Auto-print when payment is received
                    </label>
                </div>
            </div>

            {{-- Paper Size --}}
            <div class="mb-4">
                <h6 class="border-bottom pb-2">Paper Settings</h6>
                <label for="paperSize" class="form-label">Paper Size</label>
                <select class="form-select" id="paperSize" x-model="settings.paperSize">
                    <option value="58mm">58mm (2.28")</option>
                    <option value="76mm">76mm (3")</option>
                    <option value="80mm">80mm (3.15") - Standard</option>
                    <option value="90mm">90mm (3.54")</option>
                </select>
            </div>

            {{-- Print Options --}}
            <div class="mb-4">
                <h6 class="border-bottom pb-2">Print Options</h6>
                <div class="row">
                    <div class="col-md-6">
                        <label for="copies" class="form-label">Number of Copies</label>
                        <input type="number" class="form-control" id="copies" x-model="settings.copies" min="1" max="5">
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="cutPaper" x-model="settings.cutPaper">
                            <label class="form-check-label" for="cutPaper">
                                Auto-cut paper
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Receipt Content --}}
            <div class="mb-4">
                <h6 class="border-bottom pb-2">Receipt Content</h6>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="showBarcode" x-model="settings.showBarcode">
                    <label class="form-check-label" for="showBarcode">
                        Show Order Barcode
                    </label>
                </div>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="showCustomer" x-model="settings.showCustomer">
                    <label class="form-check-label" for="showCustomer">
                        Show Customer Information
                    </label>
                </div>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="showNotes" x-model="settings.showNotes">
                    <label class="form-check-label" for="showNotes">
                        Show Order Notes
                    </label>
                </div>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="openDrawer" x-model="settings.openDrawer">
                    <label class="form-check-label" for="openDrawer">
                        Open Cash Drawer
                    </label>
                </div>
            </div>

            {{-- Connection Type --}}
            <div class="mb-4">
                <h6 class="border-bottom pb-2">Printer Connection</h6>
                <label for="connectionType" class="form-label">Connection Type</label>
                <select class="form-select" id="connectionType" x-model="settings.connectionType">
                    <option value="browser">Browser Print (Default)</option>
                    <option value="network">Network Printer</option>
                    <option value="usb">USB Printer</option>
                    <option value="bluetooth">Bluetooth Printer</option>
                </select>

                <div x-show="settings.connectionType === 'network'" class="mt-3">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="printerIP" class="form-label">Printer IP Address</label>
                            <input type="text" class="form-control" id="printerIP" x-model="settings.networkIP" placeholder="192.168.1.100">
                        </div>
                        <div class="col-md-4">
                            <label for="printerPort" class="form-label">Port</label>
                            <input type="number" class="form-control" id="printerPort" x-model="settings.networkPort" placeholder="9100">
                        </div>
                    </div>
                </div>

                <div x-show="settings.connectionType === 'usb'" class="mt-3">
                    <button type="button" class="btn btn-outline-primary" @click="connectUSB()">
                        <i class="fas fa-usb"></i> Connect USB Printer
                    </button>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" @click="saveSettings()">
                    <i class="fas fa-save"></i> Save Settings
                </button>
                <button type="button" class="btn btn-outline-secondary" @click="resetSettings()">
                    <i class="fas fa-undo"></i> Reset to Default
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function printerSettings() {
    return {
        settings: {
            autoPrint: false,
            autoPrintOnOrder: true,
            autoPrintOnPayment: true,
            paperSize: '80mm',
            copies: 1,
            cutPaper: true,
            showBarcode: true,
            showCustomer: true,
            showNotes: true,
            openDrawer: false,
            connectionType: 'browser',
            networkIP: '192.168.1.100',
            networkPort: 9100,
        },
        printerStatus: {
            connected: false,
            lastPrint: null,
        },

        init() {
            this.loadSettings();
            this.checkPrinterStatus();
        },

        loadSettings() {
            const saved = localStorage.getItem('printerSettings');
            if (saved) {
                this.settings = { ...this.settings, ...JSON.parse(saved) };
            }
        },

        saveSettings() {
            localStorage.setItem('printerSettings', JSON.stringify(this.settings));
            
            // Update global printer configuration
            if (window.printer) {
                window.printer.configure(this.settings);
            }

            alert('Printer settings saved successfully!');
        },

        resetSettings() {
            if (confirm('Reset all printer settings to default?')) {
                localStorage.removeItem('printerSettings');
                this.settings = {
                    autoPrint: false,
                    autoPrintOnOrder: true,
                    autoPrintOnPayment: true,
                    paperSize: '80mm',
                    copies: 1,
                    cutPaper: true,
                    showBarcode: true,
                    showCustomer: true,
                    showNotes: true,
                    openDrawer: false,
                    connectionType: 'browser',
                    networkIP: '192.168.1.100',
                    networkPort: 9100,
                };
                this.saveSettings();
            }
        },

        checkPrinterStatus() {
            if (window.printer) {
                const status = window.printer.getStatus();
                this.printerStatus.connected = status.connected;
            }
        },

        async testPrint() {
            if (window.printer) {
                try {
                    await window.printer.testPrint();
                    alert('Test print sent!');
                } catch (error) {
                    alert('Print failed: ' + error.message);
                }
            } else {
                alert('Printer not initialized');
            }
        },

        async connectUSB() {
            if (window.ESCPOSPrinter) {
                try {
                    const printer = new ESCPOSPrinter();
                    await printer.connectUSB();
                    this.printerStatus.connected = true;
                    alert('USB printer connected!');
                } catch (error) {
                    alert('USB connection failed: ' + error.message);
                }
            }
        }
    }
}
</script>
