/**
 * ACT Test Prep - UI Components and Interactions
 */

const UI = {
    /**
     * Initialize UI components
     */
    init() {
        this.initTheme();
        this.initNavigation();
        this.initDropdowns();
        this.initModals();
        this.initTabs();
        this.initAccordions();
        this.initTooltips();
        this.highlightActiveNav();
    },

    /**
     * Initialize theme
     */
    initTheme() {
        const savedTheme = Storage.getTheme();
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = savedTheme || (prefersDark ? 'dark' : 'light');
        
        this.setTheme(theme);

        // Theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const current = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
                this.setTheme(current === 'dark' ? 'light' : 'dark');
            });
        }
    },

    /**
     * Set theme
     */
    setTheme(theme) {
        document.documentElement.classList.toggle('dark', theme === 'dark');
        Storage.setTheme(theme);
    },

    /**
     * Initialize navigation
     */
    initNavigation() {
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileNav = document.getElementById('mobile-nav');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        if (mobileMenuBtn && mobileNav) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileNav.classList.toggle('hidden');
                
                // Toggle sidebar on mobile
                if (sidebar) {
                    sidebar.classList.toggle('open');
                }
                if (sidebarOverlay) {
                    sidebarOverlay.classList.toggle('hidden');
                }
            });
        }

        // Close sidebar on overlay click
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebarOverlay.classList.add('hidden');
                if (sidebar) sidebar.classList.remove('open');
                if (mobileNav) mobileNav.classList.add('hidden');
            });
        }

        // Logout button
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async () => {
                const confirmed = await Notifications.confirm('Logout', 'Are you sure you want to logout?');
                if (confirmed) {
                    Auth.logout();
                }
            });
        }
    },

    /**
     * Initialize dropdowns
     */
    initDropdowns() {
        // User dropdown
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userDropdown = document.getElementById('user-dropdown');

        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            // Close on outside click
            document.addEventListener('click', () => {
                userDropdown.classList.add('hidden');
            });
        }

        // Generic dropdowns
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            const trigger = dropdown.querySelector('[data-dropdown-trigger]');
            
            if (trigger) {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('active');
                });
            }
        });

        // Close all dropdowns on outside click
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown.active').forEach(d => d.classList.remove('active'));
        });
    },

    /**
     * Initialize modals
     */
    initModals() {
        // Close modal on backdrop click
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) {
                    backdrop.classList.remove('active');
                }
            });
        });

        // Close modal on close button
        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal-backdrop');
                if (modal) modal.classList.remove('active');
            });
        });

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-backdrop.active').forEach(m => m.classList.remove('active'));
            }
        });
    },

    /**
     * Open modal
     */
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
        }
    },

    /**
     * Close modal
     */
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    },

    /**
     * Initialize tabs
     */
    initTabs() {
        document.querySelectorAll('.tabs').forEach(tabContainer => {
            const tabs = tabContainer.querySelectorAll('.tab');
            const contentId = tabContainer.dataset.tabContent;
            const contents = contentId ? document.querySelectorAll(`#${contentId} .tab-content`) : [];

            tabs.forEach((tab, index) => {
                tab.addEventListener('click', () => {
                    // Update active tab
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');

                    // Update content
                    contents.forEach((content, i) => {
                        content.classList.toggle('hidden', i !== index);
                    });

                    // Trigger event
                    const event = new CustomEvent('tabChange', { detail: { index, tab } });
                    tabContainer.dispatchEvent(event);
                });
            });
        });
    },

    /**
     * Initialize accordions
     */
    initAccordions() {
        document.querySelectorAll('.accordion-item').forEach(item => {
            const header = item.querySelector('.accordion-header');
            
            if (header) {
                header.addEventListener('click', () => {
                    item.classList.toggle('active');
                });
            }
        });
    },

    /**
     * Initialize tooltips
     */
    initTooltips() {
        // Tooltips are handled via CSS with data-tooltip attribute
        // This adds keyboard accessibility
        document.querySelectorAll('[data-tooltip]').forEach(el => {
            el.setAttribute('tabindex', '0');
            el.setAttribute('role', 'button');
        });
    },

    /**
     * Highlight active navigation item
     */
    highlightActiveNav() {
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        const pageName = currentPage.replace('.html', '');

        document.querySelectorAll('[data-page]').forEach(link => {
            const isActive = link.dataset.page === pageName;
            link.classList.toggle('active', isActive);
        });
    },

    /**
     * Show loading overlay
     */
    showLoading(text = 'Loading...') {
        const overlay = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');
        
        if (overlay) {
            if (loadingText) loadingText.textContent = text;
            overlay.classList.remove('hidden');
        }
    },

    /**
     * Hide loading overlay
     */
    hideLoading() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.classList.add('hidden');
        }
    },

    /**
     * Create progress bar
     */
    createProgressBar(container, progress = 0, options = {}) {
        const { color = 'primary', showLabel = true, animate = true } = options;
        
        const html = `
            <div class="progress-bar ${color ? `progress-bar-${color}` : ''}">
                <div class="progress-fill ${animate ? 'transition-all duration-500' : ''}" style="width: ${progress}%"></div>
            </div>
            ${showLabel ? `<div class="text-sm text-gray-500 mt-1">${Math.round(progress)}%</div>` : ''}
        `;
        
        if (typeof container === 'string') {
            container = document.getElementById(container);
        }
        
        if (container) {
            container.innerHTML = html;
        }
        
        return container;
    },

    /**
     * Update progress bar
     */
    updateProgressBar(container, progress) {
        if (typeof container === 'string') {
            container = document.getElementById(container);
        }
        
        if (container) {
            const fill = container.querySelector('.progress-fill');
            const label = container.querySelector('.text-sm');
            
            if (fill) fill.style.width = `${progress}%`;
            if (label) label.textContent = `${Math.round(progress)}%`;
        }
    },

    /**
     * Create skeleton loading
     */
    skeleton(type = 'text', count = 1) {
        let html = '';
        
        for (let i = 0; i < count; i++) {
            switch (type) {
                case 'text':
                    html += '<div class="skeleton h-4 rounded mb-2"></div>';
                    break;
                case 'title':
                    html += '<div class="skeleton h-6 w-1/2 rounded mb-4"></div>';
                    break;
                case 'card':
                    html += `
                        <div class="card p-4">
                            <div class="skeleton h-6 w-3/4 rounded mb-3"></div>
                            <div class="skeleton h-4 rounded mb-2"></div>
                            <div class="skeleton h-4 w-5/6 rounded mb-2"></div>
                            <div class="skeleton h-4 w-2/3 rounded"></div>
                        </div>
                    `;
                    break;
                case 'avatar':
                    html += '<div class="skeleton w-10 h-10 rounded-full"></div>';
                    break;
                case 'button':
                    html += '<div class="skeleton h-10 w-24 rounded-lg"></div>';
                    break;
            }
        }
        
        return html;
    },

    /**
     * Animate element entrance
     */
    animateIn(element, animation = 'fade-in-up') {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        
        if (element) {
            element.classList.add(`animate-${animation}`);
        }
    },

    /**
     * Scroll to element
     */
    scrollTo(element, offset = 80) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        
        if (element) {
            const y = element.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top: y, behavior: 'smooth' });
        }
    },

    /**
     * Create empty state
     */
    emptyState(icon, title, message, actionText = '', actionCallback = null) {
        let html = `
            <div class="empty-state">
                <div class="empty-state-icon">${icon}</div>
                <h3 class="empty-state-title">${Utils.escapeHtml(title)}</h3>
                <p class="empty-state-text">${Utils.escapeHtml(message)}</p>
        `;
        
        if (actionText) {
            html += `<button class="btn btn-primary empty-state-action">${Utils.escapeHtml(actionText)}</button>`;
        }
        
        html += '</div>';
        
        const container = document.createElement('div');
        container.innerHTML = html;
        
        if (actionCallback) {
            const btn = container.querySelector('.empty-state-action');
            if (btn) btn.addEventListener('click', actionCallback);
        }
        
        return container.firstElementChild;
    },

    /**
     * Format form data
     */
    getFormData(form) {
        const formData = new FormData(form);
        const data = {};
        
        for (const [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        return data;
    },

    /**
     * Reset form
     */
    resetForm(form) {
        if (typeof form === 'string') {
            form = document.querySelector(form);
        }
        
        if (form) {
            form.reset();
            Validation.clearErrors(form);
        }
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    UI.init();
});

// Make available globally
window.UI = UI;
