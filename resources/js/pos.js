/**
 * POS System JavaScript - Bilingual Support (Arabic & English)
 * Handles search, cart operations, keyboard shortcuts, and notifications
 */

// ========================================
// Notification System
// ========================================
class NotificationManager {
    constructor() {
        this.container = this.createContainer();
    }

    createContainer() {
        const container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            ${document.dir === 'rtl' ? 'left: 20px' : 'right: 20px'};
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        `;
        document.body.appendChild(container);
        return container;
    }

    show(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-amber-500',
            info: 'bg-blue-500'
        };

        notification.innerHTML = `
            <div class="${colors[type]} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 min-w-[300px] max-w-md">
                <span class="text-2xl">${icons[type]}</span>
                <span class="flex-1 font-semibold">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-1 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

        this.container.appendChild(notification);

        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100px)';
                setTimeout(() => notification.remove(), 300);
            }, duration);
        }

        return notification;
    }

    success(message, duration) {
        return this.show(message, 'success', duration);
    }

    error(message, duration) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration) {
        return this.show(message, 'info', duration);
    }
}

// Initialize notification manager
const notify = new NotificationManager();

// ========================================
// Audio Feedback
// ========================================
class AudioManager {
    constructor() {
        this.beepSound = this.createBeep();
        this.errorSound = this.createError();
    }

    createBeep() {
        // Success beep
        return new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSx+zPLTgjMGHW7A7+OZSA0PVK3o7aBSEgxIo+L0wWwhBi17y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQ==');
    }

    createError() {
        // Error sound
        return new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSx+zPLTgjMGHW7A7+OZSA0PVK3o7aBSEgxIo+L0wWwhBi17y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQxFoeD1wGwiFi97y/DYgS8GI3PG79yQQgUZY7Xm7KhUEQ==');
    }

    playBeep() {
        this.beepSound.currentTime = 0;
        this.beepSound.play().catch(() => {});
    }

    playError() {
        this.errorSound.currentTime = 0;
        this.errorSound.play().catch(() => {});
    }
}

// Initialize audio manager
const audio = new AudioManager();

// ========================================
// Barcode Scanner Support
// ========================================
class BarcodeScanner {
    constructor(callback) {
        this.buffer = '';
        this.timeout = null;
        this.callback = callback;
        this.init();
    }

    init() {
        document.addEventListener('keypress', (e) => {
            // Ignore input from form fields
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }

            // Clear timeout
            if (this.timeout) {
                clearTimeout(this.timeout);
            }

            // Add character to buffer
            if (e.key === 'Enter') {
                if (this.buffer.length > 3) {
                    this.callback(this.buffer);
                    audio.playBeep();
                }
                this.buffer = '';
            } else {
                this.buffer += e.key;
            }

            // Reset buffer after 100ms of inactivity
            this.timeout = setTimeout(() => {
                this.buffer = '';
            }, 100);
        });
    }
}

// ========================================
// Debounce Utility
// ========================================
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ========================================
// Local Storage Utilities
// ========================================
const storage = {
    get(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (e) {
            console.error('Error reading from localStorage:', e);
            return defaultValue;
        }
    },

    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (e) {
            console.error('Error writing to localStorage:', e);
            return false;
        }
    },

    remove(key) {
        try {
            localStorage.removeItem(key);
            return true;
        } catch (e) {
            console.error('Error removing from localStorage:', e);
            return false;
        }
    },

    clear() {
        try {
            localStorage.clear();
            return true;
        } catch (e) {
            console.error('Error clearing localStorage:', e);
            return false;
        }
    }
};

// ========================================
// Currency Formatter
// ========================================
function formatCurrency(amount, locale = 'en-US', currency = 'USD') {
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency
    }).format(amount);
}

// ========================================
// Date/Time Formatter
// ========================================
function formatDateTime(date, locale = 'en-US') {
    const lang = document.documentElement.lang || 'en';
    return new Intl.DateTimeFormat(lang === 'ar' ? 'ar-SA' : 'en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

// ========================================
// Print Utilities
// ========================================
function printReceipt(receiptHtml) {
    const printWindow = window.open('', '_blank');
    printWindow.document.write(receiptHtml);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}

// ========================================
// Export utilities for global use
// ========================================
window.POS = {
    notify,
    audio,
    storage,
    formatCurrency,
    formatDateTime,
    printReceipt,
    debounce,
    BarcodeScanner
};

// ========================================
// Initialize Alpine.js extensions
// ========================================
document.addEventListener('alpine:init', () => {
    // Add global magic properties
    Alpine.magic('notify', () => notify);
    Alpine.magic('audio', () => audio);
    Alpine.magic('storage', () => storage);
});

// ========================================
// Console Welcome Message
// ========================================
console.log('%c🛒 Lumi POS System', 'font-size: 24px; font-weight: bold; color: #4F46E5;');
console.log('%cBilingual Point of Sale - Ready!', 'font-size: 14px; color: #10B981;');
console.log('%cSupported Languages: العربية (AR) | English (EN)', 'font-size: 12px; color: #6B7280;');
