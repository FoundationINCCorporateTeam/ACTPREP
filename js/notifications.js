/**
 * ACT Test Prep - Notification System
 */

const Notifications = {
    container: null,
    timeout: 5000,

    /**
     * Initialize notifications
     */
    init() {
        this.container = document.getElementById('toast-container');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'fixed bottom-4 right-4 z-50 space-y-2';
            document.body.appendChild(this.container);
        }
    },

    /**
     * Show toast notification
     */
    show(type, title, message = '', duration = this.timeout) {
        if (!this.container) this.init();

        const toast = document.createElement('div');
        toast.className = `toast toast-${type} animate-fade-in-right`;
        
        const icons = {
            success: `<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`,
            error: `<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`,
            warning: `<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>`,
            info: `<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`
        };

        toast.innerHTML = `
            ${icons[type] || icons.info}
            <div class="toast-content">
                <div class="toast-title">${Utils.escapeHtml(title)}</div>
                ${message ? `<div class="toast-message">${Utils.escapeHtml(message)}</div>` : ''}
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;

        this.container.appendChild(toast);

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                toast.classList.add('toast-out');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        return toast;
    },

    /**
     * Success notification
     */
    success(title, message = '', duration) {
        return this.show('success', title, message, duration);
    },

    /**
     * Error notification
     */
    error(title, message = '', duration) {
        return this.show('error', title, message, duration);
    },

    /**
     * Warning notification
     */
    warning(title, message = '', duration) {
        return this.show('warning', title, message, duration);
    },

    /**
     * Info notification
     */
    info(title, message = '', duration) {
        return this.show('info', title, message, duration);
    },

    /**
     * Promise-based notification (for async operations)
     */
    async promise(promise, messages) {
        const loadingToast = this.show('info', messages.loading || 'Loading...', '', 0);
        
        try {
            const result = await promise;
            loadingToast.remove();
            this.success(messages.success || 'Success!');
            return result;
        } catch (error) {
            loadingToast.remove();
            this.error(messages.error || 'Error', error.message);
            throw error;
        }
    },

    /**
     * Confirm dialog
     */
    async confirm(title, message = 'Are you sure?') {
        return new Promise((resolve) => {
            const modal = document.getElementById('confirm-modal');
            const titleEl = document.getElementById('confirm-title');
            const messageEl = document.getElementById('confirm-message');
            const cancelBtn = document.getElementById('confirm-cancel');
            const okBtn = document.getElementById('confirm-ok');

            if (!modal) {
                // Fallback to native confirm
                resolve(confirm(`${title}\n\n${message}`));
                return;
            }

            titleEl.textContent = title;
            messageEl.textContent = message;
            modal.classList.remove('hidden');

            const cleanup = () => {
                modal.classList.add('hidden');
                cancelBtn.onclick = null;
                okBtn.onclick = null;
            };

            cancelBtn.onclick = () => {
                cleanup();
                resolve(false);
            };

            okBtn.onclick = () => {
                cleanup();
                resolve(true);
            };

            // Close on backdrop click
            modal.onclick = (e) => {
                if (e.target === modal) {
                    cleanup();
                    resolve(false);
                }
            };
        });
    },

    /**
     * Clear all notifications
     */
    clearAll() {
        if (this.container) {
            this.container.innerHTML = '';
        }
    }
};

// Initialize on load
document.addEventListener('DOMContentLoaded', () => Notifications.init());

// Make available globally
window.Notifications = Notifications;
