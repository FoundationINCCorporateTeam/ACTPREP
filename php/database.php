<?php
/**
 * ACT Test Prep Application - Database Manager
 * 
 * JSON file-based database management system
 */

require_once __DIR__ . '/config.php';

/**
 * Base Database Manager Class
 */
class DatabaseManager {
    protected $file;
    protected $data;
    
    public function __construct($filename) {
        $this->file = $filename;
        $this->load();
    }
    
    /**
     * Load data from JSON file
     */
    protected function load() {
        if (file_exists($this->file)) {
            $content = file_get_contents($this->file);
            $this->data = json_decode($content, true) ?? [];
        } else {
            $this->data = [];
            $this->save();
        }
    }
    
    /**
     * Save data to JSON file
     */
    protected function save() {
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->file, json_encode($this->data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Get all records
     */
    public function getAll() {
        return $this->data;
    }
    
    /**
     * Get record by ID
     */
    public function getById($id) {
        foreach ($this->data as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }
    
    /**
     * Get records by field value
     */
    public function getByField($field, $value) {
        return array_filter($this->data, function($item) use ($field, $value) {
            return isset($item[$field]) && $item[$field] === $value;
        });
    }
    
    /**
     * Create new record
     */
    public function create($data) {
        $data['id'] = $this->generateId();
        $data['created_at'] = time();
        $data['updated_at'] = time();
        $this->data[] = $data;
        $this->save();
        return $data;
    }
    
    /**
     * Update record
     */
    public function update($id, $updates) {
        foreach ($this->data as &$item) {
            if ($item['id'] === $id) {
                $item = array_merge($item, $updates);
                $item['updated_at'] = time();
                $this->save();
                return $item;
            }
        }
        return null;
    }
    
    /**
     * Delete record
     */
    public function delete($id) {
        $this->data = array_filter($this->data, function($item) use ($id) {
            return $item['id'] !== $id;
        });
        $this->data = array_values($this->data);
        $this->save();
        return true;
    }
    
    /**
     * Generate unique ID
     */
    protected function generateId() {
        return uniqid('', true);
    }
    
    /**
     * Search records
     */
    public function search($query, $fields) {
        $query = strtolower($query);
        return array_filter($this->data, function($item) use ($query, $fields) {
            foreach ($fields as $field) {
                if (isset($item[$field]) && strpos(strtolower($item[$field]), $query) !== false) {
                    return true;
                }
            }
            return false;
        });
    }
    
    /**
     * Count records
     */
    public function count() {
        return count($this->data);
    }
    
    /**
     * Get paginated records
     */
    public function paginate($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        return array_slice($this->data, $offset, $perPage);
    }
}

/**
 * User Manager Class
 */
class UserManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['users']);
    }
    
    /**
     * Create new user
     */
    public function createUser($email, $password, $name) {
        // Check if email exists
        if ($this->getByEmail($email)) {
            return ['error' => 'Email already exists'];
        }
        
        $userData = [
            'email' => strtolower(trim($email)),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'name' => trim($name),
            'avatar' => null,
            'xp' => 0,
            'level' => 1,
            'streak' => 0,
            'last_login' => null,
            'total_study_time' => 0,
            'achievements' => [],
            'settings' => [
                'theme' => 'light',
                'notifications' => true,
                'email_updates' => true,
                'timezone' => 'America/New_York'
            ]
        ];
        
        return $this->create($userData);
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $email = strtolower(trim($email));
        foreach ($this->data as $user) {
            if (isset($user['email']) && $user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }
    
    /**
     * Verify user password
     */
    public function verifyPassword($email, $password) {
        $user = $this->getByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
    
    /**
     * Update user XP
     */
    public function addXP($userId, $amount) {
        $user = $this->getById($userId);
        if (!$user) return null;
        
        $newXP = ($user['xp'] ?? 0) + $amount;
        $newLevel = $this->calculateLevel($newXP);
        
        return $this->update($userId, [
            'xp' => $newXP,
            'level' => $newLevel
        ]);
    }
    
    /**
     * Calculate level from XP
     */
    private function calculateLevel($xp) {
        $levels = LEVEL_THRESHOLDS;
        $level = 1;
        foreach ($levels as $lvl => $threshold) {
            if ($xp >= $threshold) {
                $level = $lvl;
            }
        }
        return $level;
    }
    
    /**
     * Update user streak
     */
    public function updateStreak($userId) {
        $user = $this->getById($userId);
        if (!$user) return null;
        
        $lastLogin = $user['last_login'] ?? 0;
        $today = strtotime('today');
        $yesterday = strtotime('yesterday');
        
        if ($lastLogin >= $today) {
            // Already logged in today
            return $user;
        } elseif ($lastLogin >= $yesterday && $lastLogin < $today) {
            // Logged in yesterday, increment streak
            $newStreak = ($user['streak'] ?? 0) + 1;
        } else {
            // Streak broken
            $newStreak = 1;
        }
        
        return $this->update($userId, [
            'streak' => $newStreak,
            'last_login' => time()
        ]);
    }
    
    /**
     * Add achievement to user
     */
    public function addAchievement($userId, $achievementId) {
        $user = $this->getById($userId);
        if (!$user) return null;
        
        $achievements = $user['achievements'] ?? [];
        if (!in_array($achievementId, $achievements)) {
            $achievements[] = $achievementId;
            
            // Add XP for achievement
            $achievementXP = ACHIEVEMENTS[$achievementId]['xp'] ?? 0;
            $this->addXP($userId, $achievementXP);
            
            return $this->update($userId, ['achievements' => $achievements]);
        }
        
        return $user;
    }
}

/**
 * Lesson Manager Class
 */
class LessonManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['lessons']);
    }
    
    /**
     * Get lessons by user
     */
    public function getByUser($userId) {
        return array_values($this->getByField('user_id', $userId));
    }
    
    /**
     * Get lessons by subject
     */
    public function getBySubject($subject) {
        return array_values($this->getByField('subject', $subject));
    }
    
    /**
     * Mark lesson as completed
     */
    public function markCompleted($lessonId, $userId) {
        return $this->update($lessonId, [
            'completed' => true,
            'completed_at' => time(),
            'completed_by' => $userId
        ]);
    }
    
    /**
     * Add note to lesson
     */
    public function addNote($lessonId, $note) {
        $lesson = $this->getById($lessonId);
        if (!$lesson) return null;
        
        $notes = $lesson['notes'] ?? [];
        $notes[] = [
            'id' => uniqid(),
            'content' => $note,
            'created_at' => time()
        ];
        
        return $this->update($lessonId, ['notes' => $notes]);
    }
}

/**
 * Quiz Manager Class
 */
class QuizManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['quizzes']);
    }
    
    /**
     * Get quizzes by user
     */
    public function getByUser($userId) {
        return array_values($this->getByField('user_id', $userId));
    }
    
    /**
     * Save quiz result
     */
    public function saveResult($quizId, $answers, $score, $timeTaken) {
        return $this->update($quizId, [
            'user_answers' => $answers,
            'score' => $score,
            'percentage' => $this->calculatePercentage($quizId, $score),
            'time_taken' => $timeTaken,
            'completed' => true,
            'completed_at' => time()
        ]);
    }
    
    /**
     * Calculate percentage
     */
    private function calculatePercentage($quizId, $score) {
        $quiz = $this->getById($quizId);
        if (!$quiz || !isset($quiz['questions'])) return 0;
        
        $totalQuestions = count($quiz['questions']);
        return $totalQuestions > 0 ? round(($score / $totalQuestions) * 100, 1) : 0;
    }
    
    /**
     * Get quiz statistics
     */
    public function getStats($userId) {
        $quizzes = $this->getByUser($userId);
        $completed = array_filter($quizzes, fn($q) => $q['completed'] ?? false);
        
        $totalScore = 0;
        $totalQuestions = 0;
        $bySubject = [];
        
        foreach ($completed as $quiz) {
            $totalScore += $quiz['score'] ?? 0;
            $totalQuestions += count($quiz['questions'] ?? []);
            
            $subject = $quiz['subject'] ?? 'unknown';
            if (!isset($bySubject[$subject])) {
                $bySubject[$subject] = ['correct' => 0, 'total' => 0];
            }
            $bySubject[$subject]['correct'] += $quiz['score'] ?? 0;
            $bySubject[$subject]['total'] += count($quiz['questions'] ?? []);
        }
        
        return [
            'total_quizzes' => count($quizzes),
            'completed_quizzes' => count($completed),
            'average_score' => $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100, 1) : 0,
            'by_subject' => $bySubject
        ];
    }
}

/**
 * Practice Test Manager Class
 */
class TestManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['tests']);
    }
    
    /**
     * Get tests by user
     */
    public function getByUser($userId) {
        return array_values($this->getByField('user_id', $userId));
    }
    
    /**
     * Save test result
     */
    public function saveResult($testId, $sectionResults) {
        $test = $this->getById($testId);
        if (!$test) return null;
        
        // Calculate composite score (ACT style)
        $scores = array_column($sectionResults, 'score');
        $composite = count($scores) > 0 ? round(array_sum($scores) / count($scores)) : 0;
        
        return $this->update($testId, [
            'section_results' => $sectionResults,
            'composite_score' => $composite,
            'completed' => true,
            'completed_at' => time()
        ]);
    }
    
    /**
     * Get test statistics
     */
    public function getStats($userId) {
        $tests = $this->getByUser($userId);
        $completed = array_filter($tests, fn($t) => $t['completed'] ?? false);
        
        $compositeScores = array_column($completed, 'composite_score');
        $averageComposite = count($compositeScores) > 0 
            ? round(array_sum($compositeScores) / count($compositeScores), 1) 
            : 0;
        
        return [
            'total_tests' => count($tests),
            'completed_tests' => count($completed),
            'average_composite' => $averageComposite,
            'highest_composite' => count($compositeScores) > 0 ? max($compositeScores) : 0,
            'latest_composite' => count($compositeScores) > 0 ? end($compositeScores) : 0
        ];
    }
}

/**
 * Chat History Manager Class
 */
class ChatManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['chat_history']);
    }
    
    /**
     * Get conversations by user
     */
    public function getByUser($userId) {
        return array_values($this->getByField('user_id', $userId));
    }
    
    /**
     * Add message to conversation
     */
    public function addMessage($conversationId, $role, $content) {
        $conversation = $this->getById($conversationId);
        if (!$conversation) return null;
        
        $messages = $conversation['messages'] ?? [];
        $messages[] = [
            'id' => uniqid(),
            'role' => $role,
            'content' => $content,
            'timestamp' => time()
        ];
        
        return $this->update($conversationId, [
            'messages' => $messages,
            'last_message_at' => time()
        ]);
    }
    
    /**
     * Create new conversation
     */
    public function createConversation($userId, $title = 'New Chat') {
        return $this->create([
            'user_id' => $userId,
            'title' => $title,
            'messages' => [],
            'last_message_at' => time()
        ]);
    }
}

/**
 * Study Plan Manager Class
 */
class StudyPlanManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['study_plans']);
    }
    
    /**
     * Get active plan for user
     */
    public function getActivePlan($userId) {
        $plans = $this->getByUser($userId);
        foreach ($plans as $plan) {
            if ($plan['status'] === 'active') {
                return $plan;
            }
        }
        return null;
    }
    
    /**
     * Get plans by user
     */
    public function getByUser($userId) {
        return array_values($this->getByField('user_id', $userId));
    }
    
    /**
     * Update task completion
     */
    public function completeTask($planId, $weekIndex, $dayIndex, $taskIndex) {
        $plan = $this->getById($planId);
        if (!$plan) return null;
        
        $weeks = $plan['weeks'] ?? [];
        if (isset($weeks[$weekIndex]['days'][$dayIndex]['tasks'][$taskIndex])) {
            $weeks[$weekIndex]['days'][$dayIndex]['tasks'][$taskIndex]['completed'] = true;
            $weeks[$weekIndex]['days'][$dayIndex]['tasks'][$taskIndex]['completed_at'] = time();
            return $this->update($planId, ['weeks' => $weeks]);
        }
        
        return null;
    }
}

/**
 * Essay Manager Class
 */
class EssayManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['essays']);
    }
    
    /**
     * Get essays by user
     */
    public function getByUser($userId) {
        return array_values($this->getByField('user_id', $userId));
    }
    
    /**
     * Save essay grade
     */
    public function saveGrade($essayId, $scores, $feedback) {
        $totalScore = 0;
        foreach ($scores as $domain => $score) {
            $totalScore += $score;
        }
        
        return $this->update($essayId, [
            'scores' => $scores,
            'total_score' => $totalScore,
            'feedback' => $feedback,
            'graded' => true,
            'graded_at' => time()
        ]);
    }
}

/**
 * Flashcard Manager Class
 */
class FlashcardManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['flashcards']);
    }
    
    /**
     * Get sets by user
     */
    public function getByUser($userId) {
        return array_values($this->getByField('user_id', $userId));
    }
    
    /**
     * Update card mastery
     */
    public function updateCardMastery($setId, $cardIndex, $mastery) {
        $set = $this->getById($setId);
        if (!$set) return null;
        
        $cards = $set['cards'] ?? [];
        if (isset($cards[$cardIndex])) {
            $cards[$cardIndex]['mastery'] = $mastery;
            $cards[$cardIndex]['last_reviewed'] = time();
            $cards[$cardIndex]['review_count'] = ($cards[$cardIndex]['review_count'] ?? 0) + 1;
            return $this->update($setId, ['cards' => $cards]);
        }
        
        return null;
    }
}

/**
 * Progress Manager Class
 */
class ProgressManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['progress']);
    }
    
    /**
     * Get progress for user
     */
    public function getByUser($userId) {
        $progress = $this->getByField('user_id', $userId);
        return !empty($progress) ? array_values($progress)[0] : null;
    }
    
    /**
     * Initialize progress for user
     */
    public function initializeForUser($userId) {
        $existing = $this->getByUser($userId);
        if ($existing) return $existing;
        
        return $this->create([
            'user_id' => $userId,
            'subjects' => [
                'english' => ['total_questions' => 0, 'correct' => 0],
                'math' => ['total_questions' => 0, 'correct' => 0],
                'reading' => ['total_questions' => 0, 'correct' => 0],
                'science' => ['total_questions' => 0, 'correct' => 0],
                'writing' => ['essays_submitted' => 0, 'average_score' => 0]
            ],
            'score_history' => [],
            'study_time_history' => [],
            'daily_activity' => []
        ]);
    }
    
    /**
     * Record activity
     */
    public function recordActivity($userId, $type, $data) {
        $progress = $this->getByUser($userId);
        if (!$progress) {
            $progress = $this->initializeForUser($userId);
        }
        
        $today = date('Y-m-d');
        $dailyActivity = $progress['daily_activity'] ?? [];
        
        if (!isset($dailyActivity[$today])) {
            $dailyActivity[$today] = [
                'lessons' => 0,
                'quizzes' => 0,
                'tests' => 0,
                'flashcards' => 0,
                'study_time' => 0
            ];
        }
        
        if (isset($dailyActivity[$today][$type])) {
            $dailyActivity[$today][$type] += $data['count'] ?? 1;
        }
        
        return $this->update($progress['id'], ['daily_activity' => $dailyActivity]);
    }
    
    /**
     * Update subject progress
     */
    public function updateSubjectProgress($userId, $subject, $correct, $total) {
        $progress = $this->getByUser($userId);
        if (!$progress) {
            $progress = $this->initializeForUser($userId);
        }
        
        $subjects = $progress['subjects'] ?? [];
        if (!isset($subjects[$subject])) {
            $subjects[$subject] = ['total_questions' => 0, 'correct' => 0];
        }
        
        $subjects[$subject]['total_questions'] += $total;
        $subjects[$subject]['correct'] += $correct;
        
        return $this->update($progress['id'], ['subjects' => $subjects]);
    }
}

/**
 * Settings Manager Class
 */
class SettingsManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['settings']);
    }
    
    /**
     * Get settings for user
     */
    public function getByUser($userId) {
        $settings = $this->getByField('user_id', $userId);
        return !empty($settings) ? array_values($settings)[0] : null;
    }
    
    /**
     * Initialize settings for user
     */
    public function initializeForUser($userId) {
        $existing = $this->getByUser($userId);
        if ($existing) return $existing;
        
        return $this->create([
            'user_id' => $userId,
            'theme' => 'light',
            'notifications' => true,
            'email_updates' => true,
            'timezone' => 'America/New_York',
            'language' => 'en',
            'study_reminders' => true,
            'reminder_time' => '18:00',
            'default_model' => DEFAULT_AI_MODEL,
            'font_size' => 'medium',
            'high_contrast' => false
        ]);
    }
}

/**
 * Analytics Manager Class
 */
class AnalyticsManager extends DatabaseManager {
    public function __construct() {
        parent::__construct(DB_FILES['analytics']);
    }
    
    /**
     * Record event
     */
    public function recordEvent($userId, $eventType, $data = []) {
        return $this->create([
            'user_id' => $userId,
            'event_type' => $eventType,
            'data' => $data,
            'timestamp' => time()
        ]);
    }
    
    /**
     * Get user activity summary
     */
    public function getUserSummary($userId, $days = 30) {
        $since = time() - ($days * 86400);
        $events = array_filter($this->data, function($event) use ($userId, $since) {
            return $event['user_id'] === $userId && $event['created_at'] >= $since;
        });
        
        $summary = [];
        foreach ($events as $event) {
            $type = $event['event_type'];
            if (!isset($summary[$type])) {
                $summary[$type] = 0;
            }
            $summary[$type]++;
        }
        
        return $summary;
    }
}
