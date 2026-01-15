/**
 * ACT Test Prep - Main Application
 */

const App = {
    async init() {
        // Check for public pages
        const publicPages = ['index.html', ''];
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        
        if (!publicPages.includes(currentPage)) {
            // Check auth
            const isAuth = await Auth.init();
            if (!isAuth) {
                window.location.href = 'index.html';
                return;
            }
        }
        
        // Initialize components
        UI.init();
        Notifications.init();
        MarkdownRenderer.init();
        MathRenderer.init();
        
        // Refresh user data periodically
        setInterval(() => {
            if (Auth.isAuthenticated) {
                Auth.refreshUser();
            }
        }, 300000); // Every 5 minutes
        
        console.log('ACT Test Prep initialized');
    },

    // Get current page name
    getCurrentPage() {
        return window.location.pathname.split('/').pop()?.replace('.html', '') || 'index';
    },

    // Navigate to page
    navigate(page) {
        window.location.href = page.includes('.html') ? page : page + '.html';
    },

    // Check if on specific page
    isPage(pageName) {
        return this.getCurrentPage() === pageName;
    }
};

// Initialize app
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});

// Make globally available
window.App = App;
