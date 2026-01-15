/**
 * ACT Test Prep - Utility Functions
 */

const Utils = {
    /**
     * Generate unique ID
     */
    generateId(prefix = '') {
        return prefix + Date.now().toString(36) + Math.random().toString(36).substr(2);
    },

    /**
     * Format date
     */
    formatDate(timestamp, format = 'short') {
        const date = new Date(timestamp * 1000);
        const options = format === 'short' 
            ? { month: 'short', day: 'numeric', year: 'numeric' }
            : { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    },

    /**
     * Format time ago
     */
    timeAgo(timestamp) {
        const seconds = Math.floor(Date.now() / 1000 - timestamp);
        
        if (seconds < 60) return 'just now';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' min ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' hr ago';
        if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
        
        return this.formatDate(timestamp);
    },

    /**
     * Format duration (seconds to readable)
     */
    formatDuration(seconds) {
        if (seconds < 60) return seconds + 's';
        if (seconds < 3600) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return mins + 'm' + (secs > 0 ? ' ' + secs + 's' : '');
        }
        const hrs = Math.floor(seconds / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        return hrs + 'h' + (mins > 0 ? ' ' + mins + 'm' : '');
    },

    /**
     * Format timer display
     */
    formatTimer(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return mins.toString().padStart(2, '0') + ':' + secs.toString().padStart(2, '0');
    },

    /**
     * Calculate percentage
     */
    percentage(value, total) {
        return total > 0 ? Math.round((value / total) * 100 * 10) / 10 : 0;
    },

    /**
     * Truncate text
     */
    truncate(text, length = 100) {
        if (!text || text.length <= length) return text;
        return text.substring(0, length) + '...';
    },

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    /**
     * Debounce function
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Throttle function
     */
    throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    /**
     * Deep clone object
     */
    deepClone(obj) {
        return JSON.parse(JSON.stringify(obj));
    },

    /**
     * Sleep/delay
     */
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    },

    /**
     * Get query parameters
     */
    getQueryParams() {
        const params = {};
        const searchParams = new URLSearchParams(window.location.search);
        for (const [key, value] of searchParams) {
            params[key] = value;
        }
        return params;
    },

    /**
     * Update query parameter
     */
    setQueryParam(key, value) {
        const url = new URL(window.location);
        url.searchParams.set(key, value);
        window.history.replaceState({}, '', url);
    },

    /**
     * Slugify text
     */
    slugify(text) {
        return text
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s-]+/g, '-')
            .trim();
    },

    /**
     * Capitalize first letter
     */
    capitalize(text) {
        return text.charAt(0).toUpperCase() + text.slice(1);
    },

    /**
     * Get greeting based on time
     */
    getGreeting() {
        const hour = new Date().getHours();
        if (hour < 12) return 'Good morning';
        if (hour < 17) return 'Good afternoon';
        return 'Good evening';
    },

    /**
     * Get subject color
     */
    getSubjectColor(subject) {
        const colors = {
            english: 'blue',
            math: 'green',
            reading: 'purple',
            science: 'orange',
            writing: 'pink'
        };
        return colors[subject] || 'gray';
    },

    /**
     * Get difficulty color
     */
    getDifficultyColor(difficulty) {
        const colors = {
            beginner: 'green',
            intermediate: 'blue',
            advanced: 'orange',
            expert: 'red'
        };
        return colors[difficulty] || 'gray';
    },

    /**
     * Convert ACT raw score to scaled score
     */
    rawToScaled(correct, total) {
        if (total <= 0) return 1;
        const percentage = (correct / total) * 100;
        
        if (percentage >= 100) return 36;
        if (percentage >= 97) return 35;
        if (percentage >= 94) return 34;
        if (percentage >= 91) return 33;
        if (percentage >= 88) return 32;
        if (percentage >= 85) return 31;
        if (percentage >= 82) return 30;
        if (percentage >= 79) return 29;
        if (percentage >= 76) return 28;
        if (percentage >= 73) return 27;
        if (percentage >= 70) return 26;
        if (percentage >= 67) return 25;
        if (percentage >= 64) return 24;
        if (percentage >= 61) return 23;
        if (percentage >= 58) return 22;
        if (percentage >= 55) return 21;
        if (percentage >= 52) return 20;
        if (percentage >= 49) return 19;
        if (percentage >= 46) return 18;
        if (percentage >= 43) return 17;
        if (percentage >= 40) return 16;
        if (percentage >= 37) return 15;
        if (percentage >= 34) return 14;
        if (percentage >= 31) return 13;
        if (percentage >= 28) return 12;
        if (percentage >= 25) return 11;
        if (percentage >= 22) return 10;
        return Math.max(1, Math.floor(percentage / 3));
    },

    /**
     * Get percentile for ACT score
     */
    getPercentile(score) {
        const percentiles = {
            36: 99, 35: 99, 34: 99, 33: 98, 32: 97,
            31: 95, 30: 93, 29: 90, 28: 87, 27: 84,
            26: 80, 25: 76, 24: 72, 23: 67, 22: 62,
            21: 56, 20: 50, 19: 44, 18: 38, 17: 32,
            16: 26, 15: 21, 14: 16, 13: 12, 12: 9,
            11: 6, 10: 4, 9: 2, 8: 1, 7: 1,
            6: 1, 5: 1, 4: 1, 3: 1, 2: 1, 1: 1
        };
        return percentiles[score] || 1;
    },

    /**
     * Calculate composite score
     */
    compositeScore(scores) {
        if (!scores || scores.length === 0) return 0;
        return Math.round(scores.reduce((a, b) => a + b, 0) / scores.length);
    },

    /**
     * Get score color class
     */
    getScoreClass(score, max = 36) {
        const pct = (score / max) * 100;
        if (pct >= 85) return 'text-green-600';
        if (pct >= 70) return 'text-blue-600';
        if (pct >= 55) return 'text-yellow-600';
        return 'text-red-600';
    },

    /**
     * Download data as file
     */
    downloadFile(data, filename, type = 'application/json') {
        const blob = new Blob([typeof data === 'string' ? data : JSON.stringify(data, null, 2)], { type });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    },

    /**
     * Copy text to clipboard
     */
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            console.error('Failed to copy:', err);
            return false;
        }
    },

    /**
     * Check if mobile device
     */
    isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    },

    /**
     * Shuffle array
     */
    shuffle(array) {
        const arr = [...array];
        for (let i = arr.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }
        return arr;
    },

    /**
     * Get random item from array
     */
    randomItem(array) {
        return array[Math.floor(Math.random() * array.length)];
    },

    /**
     * Smooth scroll to element
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
     * Count words in text
     */
    wordCount(text) {
        return text ? text.trim().split(/\s+/).filter(Boolean).length : 0;
    },

    /**
     * Estimate reading time (minutes)
     */
    readingTime(text, wpm = 200) {
        const words = this.wordCount(text);
        return Math.max(1, Math.ceil(words / wpm));
    }
};

// Make available globally
window.Utils = Utils;
