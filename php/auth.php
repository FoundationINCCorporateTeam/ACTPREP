<?php
/**
 * ACT Test Prep Application - Authentication System
 * 
 * Handles user authentication, session management, and access control
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

/**
 * Authentication Manager Class
 */
class AuthManager {
    private $userManager;
    
    public function __construct() {
        $this->userManager = new UserManager();
    }
    
    /**
     * Register new user
     */
    public function register($email, $password, $name) {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email address'];
        }
        
        // Validate password
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            return ['error' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }
        
        // Validate name
        if (empty(trim($name))) {
            return ['error' => 'Name is required'];
        }
        
        // Create user
        $result = $this->userManager->createUser($email, $password, $name);
        
        if (isset($result['error'])) {
            return $result;
        }
        
        // Initialize progress and settings
        $progressManager = new ProgressManager();
        $progressManager->initializeForUser($result['id']);
        
        $settingsManager = new SettingsManager();
        $settingsManager->initializeForUser($result['id']);
        
        // Auto-login after registration
        return $this->createSession($result);
    }
    
    /**
     * Login user
     */
    public function login($email, $password) {
        // Validate input
        if (empty($email) || empty($password)) {
            return ['error' => 'Email and password are required'];
        }
        
        // Verify credentials
        $user = $this->userManager->verifyPassword($email, $password);
        
        if (!$user) {
            return ['error' => 'Invalid email or password'];
        }
        
        // Update streak
        $this->userManager->updateStreak($user['id']);
        
        // Create session
        return $this->createSession($user);
    }
    
    /**
     * Create session for user
     */
    private function createSession($user) {
        $token = bin2hex(random_bytes(32));
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['token'] = $token;
        $_SESSION['login_time'] = time();
        
        return [
            'success' => true,
            'user' => $this->sanitizeUser($user),
            'token' => $token
        ];
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_unset();
        session_destroy();
        
        // Start a new session for CSRF protection
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return ['success' => true];
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_time'])) {
            return false;
        }
        
        // Check session expiry
        if (time() - $_SESSION['login_time'] > SESSION_LIFETIME) {
            $this->logout();
            return false;
        }
        
        return true;
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $user = $this->userManager->getById($_SESSION['user_id']);
        return $user ? $this->sanitizeUser($user) : null;
    }
    
    /**
     * Get current user ID
     */
    public function getCurrentUserId() {
        return $this->isLoggedIn() ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Require authentication
     */
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            jsonResponse(false, null, 'Authentication required', ['code' => 'AUTH_REQUIRED']);
        }
    }
    
    /**
     * Sanitize user data (remove sensitive fields)
     */
    private function sanitizeUser($user) {
        $safe = $user;
        unset($safe['password']);
        return $safe;
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        $allowedFields = ['name', 'avatar'];
        $updates = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[$field] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            return ['error' => 'No valid fields to update'];
        }
        
        $result = $this->userManager->update($userId, $updates);
        return $result ? $this->sanitizeUser($result) : ['error' => 'Failed to update profile'];
    }
    
    /**
     * Change password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->userManager->getById($userId);
        
        if (!$user) {
            return ['error' => 'User not found'];
        }
        
        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return ['error' => 'Current password is incorrect'];
        }
        
        // Validate new password
        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            return ['error' => 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }
        
        // Update password
        $result = $this->userManager->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
        
        return $result ? ['success' => true] : ['error' => 'Failed to update password'];
    }
    
    /**
     * Generate password reset token
     */
    public function generateResetToken($email) {
        $user = $this->userManager->getByEmail($email);
        
        if (!$user) {
            // Don't reveal if email exists
            return ['success' => true, 'message' => 'If the email exists, a reset link will be sent'];
        }
        
        $token = bin2hex(random_bytes(32));
        $expires = time() + TOKEN_LIFETIME;
        
        $this->userManager->update($user['id'], [
            'reset_token' => $token,
            'reset_token_expires' => $expires
        ]);
        
        // In production, send email with reset link
        return [
            'success' => true,
            'message' => 'If the email exists, a reset link will be sent',
            'debug_token' => $token // Remove in production
        ];
    }
    
    /**
     * Reset password with token
     */
    public function resetPassword($token, $newPassword) {
        // Find user with valid token
        $users = $this->userManager->getAll();
        $user = null;
        
        foreach ($users as $u) {
            if (isset($u['reset_token']) && $u['reset_token'] === $token) {
                if (isset($u['reset_token_expires']) && $u['reset_token_expires'] > time()) {
                    $user = $u;
                    break;
                }
            }
        }
        
        if (!$user) {
            return ['error' => 'Invalid or expired reset token'];
        }
        
        // Validate new password
        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            return ['error' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }
        
        // Update password and clear token
        $result = $this->userManager->update($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_expires' => null
        ]);
        
        return $result ? ['success' => true] : ['error' => 'Failed to reset password'];
    }
    
    /**
     * Verify CSRF token
     */
    public function verifyCsrfToken($token) {
        return isset($_SESSION['token']) && hash_equals($_SESSION['token'], $token);
    }
    
    /**
     * Get CSRF token
     */
    public function getCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Delete account
     */
    public function deleteAccount($userId, $password) {
        $user = $this->userManager->getById($userId);
        
        if (!$user) {
            return ['error' => 'User not found'];
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['error' => 'Incorrect password'];
        }
        
        // Delete all user data
        $this->deleteUserData($userId);
        
        // Delete user
        $this->userManager->delete($userId);
        
        // Logout
        $this->logout();
        
        return ['success' => true];
    }
    
    /**
     * Delete all user data
     */
    private function deleteUserData($userId) {
        $managers = [
            new LessonManager(),
            new QuizManager(),
            new TestManager(),
            new ChatManager(),
            new StudyPlanManager(),
            new EssayManager(),
            new FlashcardManager(),
            new ProgressManager(),
            new SettingsManager()
        ];
        
        foreach ($managers as $manager) {
            $items = $manager->getByField('user_id', $userId);
            foreach ($items as $item) {
                $manager->delete($item['id']);
            }
        }
    }
    
    /**
     * Export user data
     */
    public function exportUserData($userId) {
        $user = $this->userManager->getById($userId);
        
        if (!$user) {
            return ['error' => 'User not found'];
        }
        
        $data = [
            'user' => $this->sanitizeUser($user),
            'lessons' => (new LessonManager())->getByField('user_id', $userId),
            'quizzes' => (new QuizManager())->getByField('user_id', $userId),
            'tests' => (new TestManager())->getByField('user_id', $userId),
            'chat_history' => (new ChatManager())->getByField('user_id', $userId),
            'study_plans' => (new StudyPlanManager())->getByField('user_id', $userId),
            'essays' => (new EssayManager())->getByField('user_id', $userId),
            'flashcards' => (new FlashcardManager())->getByField('user_id', $userId),
            'progress' => (new ProgressManager())->getByUser($userId),
            'settings' => (new SettingsManager())->getByUser($userId),
            'exported_at' => date('Y-m-d H:i:s')
        ];
        
        return $data;
    }
}

// Global auth instance
$auth = new AuthManager();
