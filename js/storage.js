/**
 * ACT Test Prep - LocalStorage Manager
 */

const Storage = {
    prefix: 'act_prep_',

    /**
     * Set item in storage
     */
    set(key, value) {
        try {
            const serialized = JSON.stringify(value);
            localStorage.setItem(this.prefix + key, serialized);
            return true;
        } catch (error) {
            console.error('Storage set error:', error);
            return false;
        }
    },

    /**
     * Get item from storage
     */
    get(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(this.prefix + key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (error) {
            console.error('Storage get error:', error);
            return defaultValue;
        }
    },

    /**
     * Remove item from storage
     */
    remove(key) {
        try {
            localStorage.removeItem(this.prefix + key);
            return true;
        } catch (error) {
            console.error('Storage remove error:', error);
            return false;
        }
    },

    /**
     * Clear all app storage
     */
    clear() {
        try {
            Object.keys(localStorage)
                .filter(key => key.startsWith(this.prefix))
                .forEach(key => localStorage.removeItem(key));
            return true;
        } catch (error) {
            console.error('Storage clear error:', error);
            return false;
        }
    },

    /**
     * Check if key exists
     */
    has(key) {
        return localStorage.getItem(this.prefix + key) !== null;
    },

    /**
     * Get all keys
     */
    keys() {
        return Object.keys(localStorage)
            .filter(key => key.startsWith(this.prefix))
            .map(key => key.replace(this.prefix, ''));
    },

    /**
     * Get storage size in bytes
     */
    size() {
        let total = 0;
        Object.keys(localStorage)
            .filter(key => key.startsWith(this.prefix))
            .forEach(key => {
                total += localStorage.getItem(key).length * 2; // UTF-16 characters
            });
        return total;
    },

    // ==================== Specific Storage Methods ====================

    /**
     * Get/Set user session
     */
    getSession() {
        return this.get('session', null);
    },

    setSession(session) {
        return this.set('session', session);
    },

    clearSession() {
        return this.remove('session');
    },

    /**
     * Get/Set theme preference
     */
    getTheme() {
        return this.get('theme', 'light');
    },

    setTheme(theme) {
        return this.set('theme', theme);
    },

    /**
     * Get/Set settings
     */
    getSettings() {
        return this.get('settings', {
            theme: 'light',
            notifications: true,
            autoSave: true,
            defaultModel: 'deepseek-v3.2',
            fontSize: 'medium'
        });
    },

    setSettings(settings) {
        return this.set('settings', settings);
    },

    updateSettings(updates) {
        const current = this.getSettings();
        return this.setSettings({ ...current, ...updates });
    },

    /**
     * Get/Set recent items
     */
    getRecentLessons() {
        return this.get('recent_lessons', []);
    },

    addRecentLesson(lesson) {
        const recent = this.getRecentLessons();
        const filtered = recent.filter(l => l.id !== lesson.id);
        filtered.unshift(lesson);
        return this.set('recent_lessons', filtered.slice(0, 10));
    },

    getRecentQuizzes() {
        return this.get('recent_quizzes', []);
    },

    addRecentQuiz(quiz) {
        const recent = this.getRecentQuizzes();
        const filtered = recent.filter(q => q.id !== quiz.id);
        filtered.unshift(quiz);
        return this.set('recent_quizzes', filtered.slice(0, 10));
    },

    /**
     * Get/Set quiz state (for resuming)
     */
    getQuizState(quizId) {
        return this.get(`quiz_state_${quizId}`, null);
    },

    setQuizState(quizId, state) {
        return this.set(`quiz_state_${quizId}`, state);
    },

    clearQuizState(quizId) {
        return this.remove(`quiz_state_${quizId}`);
    },

    /**
     * Get/Set test state (for resuming)
     */
    getTestState(testId) {
        return this.get(`test_state_${testId}`, null);
    },

    setTestState(testId, state) {
        return this.set(`test_state_${testId}`, state);
    },

    clearTestState(testId) {
        return this.remove(`test_state_${testId}`);
    },

    /**
     * Get/Set chat draft
     */
    getChatDraft() {
        return this.get('chat_draft', '');
    },

    setChatDraft(draft) {
        return this.set('chat_draft', draft);
    },

    clearChatDraft() {
        return this.remove('chat_draft');
    },

    /**
     * Get/Set essay draft
     */
    getEssayDraft() {
        return this.get('essay_draft', { content: '', prompt: null });
    },

    setEssayDraft(draft) {
        return this.set('essay_draft', draft);
    },

    clearEssayDraft() {
        return this.remove('essay_draft');
    },

    /**
     * Get/Set last selected options
     */
    getLastOptions() {
        return this.get('last_options', {
            subject: 'math',
            topic: '',
            difficulty: 'intermediate',
            model: 'deepseek-v3.2'
        });
    },

    setLastOptions(options) {
        const current = this.getLastOptions();
        return this.set('last_options', { ...current, ...options });
    },

    /**
     * Get/Set favorites
     */
    getFavorites() {
        return this.get('favorites', { lessons: [], quizzes: [], flashcards: [] });
    },

    addFavorite(type, id) {
        const favorites = this.getFavorites();
        if (!favorites[type].includes(id)) {
            favorites[type].push(id);
            this.set('favorites', favorites);
        }
        return favorites;
    },

    removeFavorite(type, id) {
        const favorites = this.getFavorites();
        favorites[type] = favorites[type].filter(i => i !== id);
        this.set('favorites', favorites);
        return favorites;
    },

    isFavorite(type, id) {
        const favorites = this.getFavorites();
        return favorites[type]?.includes(id) || false;
    },

    /**
     * Get/Set study streak
     */
    getStudyStreak() {
        return this.get('study_streak', { count: 0, lastDate: null });
    },

    updateStudyStreak() {
        const streak = this.getStudyStreak();
        const today = new Date().toDateString();
        const lastDate = streak.lastDate;

        if (lastDate === today) {
            return streak;
        }

        const yesterday = new Date(Date.now() - 86400000).toDateString();
        
        if (lastDate === yesterday) {
            streak.count++;
        } else {
            streak.count = 1;
        }
        
        streak.lastDate = today;
        this.set('study_streak', streak);
        return streak;
    },

    /**
     * Get/Set daily stats
     */
    getDailyStats() {
        const today = new Date().toISOString().split('T')[0];
        return this.get(`daily_stats_${today}`, {
            lessonsViewed: 0,
            quizzesCompleted: 0,
            questionsAnswered: 0,
            studyTime: 0
        });
    },

    updateDailyStats(updates) {
        const today = new Date().toISOString().split('T')[0];
        const stats = this.getDailyStats();
        Object.keys(updates).forEach(key => {
            if (typeof updates[key] === 'number') {
                stats[key] = (stats[key] || 0) + updates[key];
            }
        });
        return this.set(`daily_stats_${today}`, stats);
    },

    /**
     * Cache API response
     */
    cacheResponse(key, data, ttl = 3600) {
        const cacheItem = {
            data,
            expires: Date.now() + (ttl * 1000)
        };
        return this.set(`cache_${key}`, cacheItem);
    },

    getCachedResponse(key) {
        const cacheItem = this.get(`cache_${key}`, null);
        if (!cacheItem) return null;
        
        if (Date.now() > cacheItem.expires) {
            this.remove(`cache_${key}`);
            return null;
        }
        
        return cacheItem.data;
    },

    /**
     * Clear expired cache
     */
    clearExpiredCache() {
        this.keys()
            .filter(key => key.startsWith('cache_'))
            .forEach(key => {
                const item = this.get(key);
                if (item && Date.now() > item.expires) {
                    this.remove(key);
                }
            });
    }
};

// Make available globally
window.Storage = Storage;
