{{-- Print Button Component --}}
@props(['orderId', 'label' => 'Print Receipt', 'icon' => 'fa-print', 'class' => 'btn-primary', 'autoPrint' => false])

<button 
    type="button" 
    class="btn {{ $class }} print-receipt-btn" 
    data-order-id="{{ $orderId }}"
    data-auto-print="{{ $autoPrint ? 'true' : 'false' }}"
    onclick="printReceipt({{ $orderId }})"
    {{ $attributes }}
>
    <i class="fas {{ $icon }}"></i> {{ $label }}
</button>

<script>
function printReceipt(orderId, options = {}) {
    if (window.printer) {
        window.printer.printOrder(orderId, options)
            .then(() => {
                console.log('Receipt printed successfully');
            })
            .catch(error => {
                console.error('Print failed:', error);
                alert('Failed to print receipt: ' + error.message);
            });
    } else {
        // Fallback to direct window.open
        window.open(`/admin/orders/${orderId}/invoice`, '_blank');
    }
}

// Auto-print on page load if enabled
document.addEventListener('DOMContentLoaded', function() {
    const autoPrintBtn = document.querySelector('[data-auto-print="true"]');
    if (autoPrintBtn) {
        const orderId = autoPrintBtn.dataset.orderId;
        const settings = JSON.parse(localStorage.getItem('printerSettings') || '{}');
        
        if (settings.autoPrint) {
            setTimeout(() => {
                printReceipt(orderId);
            }, 1000);
        }
    }
});
</script>
