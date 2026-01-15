/**
 * ACT Test Prep - Authentication Handler
 */

const Auth = {
    user: null,
    isAuthenticated: false,

    /**
     * Initialize auth state
     */
    async init() {
        // Check for existing session
        const session = Storage.getSession();
        if (session) {
            this.user = session;
            this.isAuthenticated = true;
        }

        // Verify with server
        try {
            const result = await API.checkAuth();
            if (result.success && result.data.authenticated) {
                const userResult = await API.getCurrentUser();
                if (userResult.success && userResult.data) {
                    this.user = userResult.data;
                    this.isAuthenticated = true;
                    Storage.setSession(this.user);
                    this.updateUI();
                }
            } else {
                this.clearAuth();
            }
        } catch (error) {
            console.error('Auth check failed:', error);
        }

        return this.isAuthenticated;
    },

    /**
     * Login user
     */
    async login(email, password) {
        try {
            const result = await API.login(email, password);
            
            if (result.success) {
                this.user = result.data;
                this.isAuthenticated = true;
                Storage.setSession(this.user);
                Storage.updateStudyStreak();
                this.updateUI();
                Notifications.success('Welcome back!', `Hello, ${this.user.name}`);
                return { success: true };
            }
            
            return { success: false, message: result.message || 'Login failed' };
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, message: 'An error occurred during login' };
        }
    },

    /**
     * Register new user
     */
    async register(email, password, name) {
        try {
            const result = await API.register(email, password, name);
            
            if (result.success) {
                this.user = result.data;
                this.isAuthenticated = true;
                Storage.setSession(this.user);
                this.updateUI();
                Notifications.success('Welcome!', 'Your account has been created');
                return { success: true };
            }
            
            return { success: false, message: result.message || 'Registration failed' };
        } catch (error) {
            console.error('Registration error:', error);
            return { success: false, message: 'An error occurred during registration' };
        }
    },

    /**
     * Logout user
     */
    async logout() {
        try {
            await API.logout();
        } catch (error) {
            console.error('Logout API error:', error);
        }
        
        this.clearAuth();
        Notifications.info('Logged out', 'See you next time!');
        window.location.href = 'index.html';
    },

    /**
     * Clear auth state
     */
    clearAuth() {
        this.user = null;
        this.isAuthenticated = false;
        Storage.clearSession();
    },

    /**
     * Require authentication
     */
    requireAuth() {
        if (!this.isAuthenticated) {
            Storage.set('redirect_after_login', window.location.href);
            window.location.href = 'index.html';
            return false;
        }
        return true;
    },

    /**
     * Get current user
     */
    getUser() {
        return this.user;
    },

    /**
     * Get user ID
     */
    getUserId() {
        return this.user?.id || null;
    },

    /**
     * Get user name
     */
    getUserName() {
        return this.user?.name || 'Guest';
    },

    /**
     * Get user initials
     */
    getUserInitials() {
        const name = this.user?.name || 'Guest';
        return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
    },

    /**
     * Update user data
     */
    async refreshUser() {
        if (!this.isAuthenticated) return;

        try {
            const result = await API.getCurrentUser();
            if (result.success && result.data) {
                this.user = result.data;
                Storage.setSession(this.user);
                this.updateUI();
            }
        } catch (error) {
            console.error('Refresh user error:', error);
        }
    },

    /**
     * Update UI with user data
     */
    updateUI() {
        // Update avatar
        const avatarEl = document.getElementById('user-avatar');
        if (avatarEl) {
            avatarEl.textContent = this.getUserInitials();
        }

        // Update name
        const nameEl = document.getElementById('user-name');
        if (nameEl) {
            nameEl.textContent = this.getUserName();
        }

        // Update dropdown
        const dropdownName = document.getElementById('dropdown-user-name');
        if (dropdownName) {
            dropdownName.textContent = this.getUserName();
        }

        const dropdownEmail = document.getElementById('dropdown-user-email');
        if (dropdownEmail) {
            dropdownEmail.textContent = this.user?.email || '';
        }

        // Update sidebar stats
        const sidebarStreak = document.getElementById('sidebar-streak');
        if (sidebarStreak) {
            sidebarStreak.textContent = this.user?.streak || 0;
        }

        const sidebarXP = document.getElementById('sidebar-xp');
        if (sidebarXP) {
            sidebarXP.textContent = this.user?.xp || 0;
        }

        const sidebarLevel = document.getElementById('sidebar-level');
        if (sidebarLevel) {
            sidebarLevel.textContent = this.user?.level || 1;
        }
    },

    /**
     * Update profile
     */
    async updateProfile(data) {
        try {
            const result = await API.updateProfile(data);
            if (result.success) {
                this.user = { ...this.user, ...result.data };
                Storage.setSession(this.user);
                this.updateUI();
                Notifications.success('Profile updated');
                return { success: true };
            }
            return { success: false, message: result.message };
        } catch (error) {
            console.error('Update profile error:', error);
            return { success: false, message: 'Failed to update profile' };
        }
    },

    /**
     * Change password
     */
    async changePassword(currentPassword, newPassword) {
        try {
            const result = await API.changePassword(currentPassword, newPassword);
            if (result.success) {
                Notifications.success('Password changed');
                return { success: true };
            }
            return { success: false, message: result.message };
        } catch (error) {
            console.error('Change password error:', error);
            return { success: false, message: 'Failed to change password' };
        }
    },

    /**
     * Check if user has achievement
     */
    hasAchievement(achievementId) {
        return this.user?.achievements?.includes(achievementId) || false;
    },

    /**
     * Get user level info
     */
    getLevelInfo() {
        const level = this.user?.level || 1;
        const xp = this.user?.xp || 0;
        
        const thresholds = {
            1: 0, 2: 100, 3: 300, 4: 600, 5: 1000,
            6: 1500, 7: 2200, 8: 3000, 9: 4000, 10: 5500,
            11: 7500, 12: 10000, 13: 13000, 14: 17000, 15: 22000,
            16: 28000, 17: 35000, 18: 43000, 19: 52000, 20: 62000
        };

        const currentThreshold = thresholds[level] || 0;
        const nextThreshold = thresholds[level + 1] || currentThreshold + 10000;
        const progress = ((xp - currentThreshold) / (nextThreshold - currentThreshold)) * 100;

        return {
            level,
            xp,
            currentThreshold,
            nextThreshold,
            progress: Math.min(100, Math.max(0, progress)),
            xpToNext: nextThreshold - xp
        };
    }
};

// Make available globally
window.Auth = Auth;
