<?php
/**
 * ACT Test Prep Application - Configuration
 * 
 * This file contains all configuration constants and settings
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../data/error.log');

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// API Configuration
define('NANO_GPT_API_ENDPOINT', 'https://nano-gpt.com/api/v1/chat/completions');
define('NANO_GPT_API_KEY', 'sk-nano-d1f7d1c4-79a9-456d-8f8b-a3e7962693fc');

// Available AI Models
define('AI_MODELS', [
    'glm-4.7-thinking' => [
        'id' => 'zai-org/glm-4.7:thinking',
        'name' => 'GLM 4.7 Thinking',
        'description' => 'Advanced reasoning model with thinking capabilities'
    ],
    'deepseek-v3.2' => [
        'id' => 'deepseek/deepseek-v3.2:thinking',
        'name' => 'DeepSeek V3.2 Thinking',
        'description' => 'High-performance thinking model'
    ],
    'llama-3.1-405b' => [
        'id' => 'Meta-Llama-3-1-405B-Instruct-FP8',
        'name' => 'Llama 3.1 Large (405B)',
        'description' => 'Large-scale instruction-tuned model'
    ],
    'minimax-m2.1' => [
        'id' => 'minimax/minimax-m2.1',
        'name' => 'MiniMax M2.1',
        'description' => 'Efficient multi-purpose model'
    ],
    'mistral-large-3' => [
        'id' => 'mistralai/mistral-large-3-675b-instruct-2512',
        'name' => 'Mistral Large 3 (675B)',
        'description' => 'Enterprise-grade reasoning model'
    ],
    'kimi-k2' => [
        'id' => 'moonshotai/kimi-k2-thinking',
        'name' => 'Kimi K2 Thinking',
        'description' => 'Advanced Chinese-English reasoning model'
    ]
]);

// Default AI Model
define('DEFAULT_AI_MODEL', 'deepseek-v3.2');

// File Paths
define('DATA_DIR', __DIR__ . '/../data/');
define('UPLOADS_DIR', __DIR__ . '/../uploads/');

// Database Files
define('DB_FILES', [
    'users' => DATA_DIR . 'users.json',
    'lessons' => DATA_DIR . 'lessons.json',
    'quizzes' => DATA_DIR . 'quizzes.json',
    'tests' => DATA_DIR . 'tests.json',
    'chat_history' => DATA_DIR . 'chat_history.json',
    'study_plans' => DATA_DIR . 'study_plans.json',
    'essays' => DATA_DIR . 'essays.json',
    'flashcards' => DATA_DIR . 'flashcards.json',
    'progress' => DATA_DIR . 'progress.json',
    'settings' => DATA_DIR . 'settings.json',
    'poll_jobs' => DATA_DIR . 'poll_jobs.json',
    'analytics' => DATA_DIR . 'analytics.json'
]);

// ACT Subject Definitions
define('ACT_SUBJECTS', [
    'english' => [
        'name' => 'English',
        'questions' => 75,
        'time_minutes' => 45,
        'topics' => [
            'grammar' => 'Grammar & Usage',
            'punctuation' => 'Punctuation',
            'sentence_structure' => 'Sentence Structure',
            'strategy' => 'Strategy',
            'organization' => 'Organization',
            'style' => 'Style',
            'rhetorical_skills' => 'Rhetorical Skills',
            'production_of_writing' => 'Production of Writing',
            'knowledge_of_language' => 'Knowledge of Language',
            'conventions' => 'Conventions of Standard English'
        ]
    ],
    'math' => [
        'name' => 'Mathematics',
        'questions' => 60,
        'time_minutes' => 60,
        'topics' => [
            'pre_algebra' => 'Pre-Algebra',
            'elementary_algebra' => 'Elementary Algebra',
            'intermediate_algebra' => 'Intermediate Algebra',
            'coordinate_geometry' => 'Coordinate Geometry',
            'plane_geometry' => 'Plane Geometry',
            'trigonometry' => 'Trigonometry',
            'functions' => 'Functions',
            'statistics' => 'Statistics & Probability',
            'number_properties' => 'Number & Quantity',
            'modeling' => 'Modeling'
        ]
    ],
    'reading' => [
        'name' => 'Reading',
        'questions' => 40,
        'time_minutes' => 35,
        'topics' => [
            'key_ideas' => 'Key Ideas & Details',
            'craft_structure' => 'Craft & Structure',
            'integration' => 'Integration of Knowledge',
            'literary_narrative' => 'Literary Narrative/Prose Fiction',
            'social_science' => 'Social Science',
            'humanities' => 'Humanities',
            'natural_science' => 'Natural Science',
            'main_ideas' => 'Main Ideas',
            'supporting_details' => 'Supporting Details',
            'vocabulary_context' => 'Vocabulary in Context'
        ]
    ],
    'science' => [
        'name' => 'Science',
        'questions' => 40,
        'time_minutes' => 35,
        'topics' => [
            'data_interpretation' => 'Data Representation',
            'scientific_investigation' => 'Research Summaries',
            'evaluation_models' => 'Conflicting Viewpoints',
            'biology' => 'Biology',
            'chemistry' => 'Chemistry',
            'physics' => 'Physics',
            'earth_science' => 'Earth/Space Science',
            'analysis' => 'Analysis',
            'interpretation' => 'Interpretation',
            'scientific_reasoning' => 'Scientific Reasoning'
        ]
    ],
    'writing' => [
        'name' => 'Writing (Optional)',
        'questions' => 1,
        'time_minutes' => 40,
        'topics' => [
            'ideas_analysis' => 'Ideas and Analysis',
            'development_support' => 'Development and Support',
            'organization' => 'Organization',
            'language_use' => 'Language Use and Conventions',
            'essay_planning' => 'Essay Planning',
            'thesis_development' => 'Thesis Development',
            'argumentation' => 'Argumentation',
            'evidence_use' => 'Use of Evidence'
        ]
    ]
]);

// Difficulty Levels
define('DIFFICULTY_LEVELS', [
    'beginner' => [
        'name' => 'Beginner',
        'description' => 'Basic concepts and simple problems',
        'score_range' => '1-18'
    ],
    'intermediate' => [
        'name' => 'Intermediate',
        'description' => 'Standard ACT difficulty',
        'score_range' => '19-24'
    ],
    'advanced' => [
        'name' => 'Advanced',
        'description' => 'Challenging ACT problems',
        'score_range' => '25-30'
    ],
    'expert' => [
        'name' => 'Expert',
        'description' => 'Beyond ACT difficulty, competition-level',
        'score_range' => '31-36'
    ]
]);

// Lesson Lengths
define('LESSON_LENGTHS', [
    'short' => [
        'name' => 'Short',
        'minutes' => 5,
        'description' => 'Quick overview'
    ],
    'medium' => [
        'name' => 'Medium',
        'minutes' => 15,
        'description' => 'Standard lesson'
    ],
    'long' => [
        'name' => 'Long',
        'minutes' => 30,
        'description' => 'In-depth coverage'
    ]
]);

// Essay Scoring Rubric
define('ESSAY_SCORING', [
    'ideas_analysis' => [
        'name' => 'Ideas and Analysis',
        'description' => 'Clarity of argument and quality of analysis',
        'max_score' => 6
    ],
    'development_support' => [
        'name' => 'Development and Support',
        'description' => 'Development of ideas and use of evidence',
        'max_score' => 6
    ],
    'organization' => [
        'name' => 'Organization',
        'description' => 'Logical structure and coherence',
        'max_score' => 6
    ],
    'language_use' => [
        'name' => 'Language Use and Conventions',
        'description' => 'Grammar, vocabulary, and mechanics',
        'max_score' => 6
    ]
]);

// Quiz Question Types
define('QUESTION_TYPES', [
    'multiple_choice' => 'Multiple Choice',
    'true_false' => 'True/False',
    'fill_blank' => 'Fill in the Blank'
]);

// Poll Job Settings
define('POLL_JOB_TIMEOUT', 300); // 5 minutes
define('POLL_JOB_CHECK_INTERVAL', 2000); // 2 seconds
define('POLL_JOB_MAX_ATTEMPTS', 150);

// Rate Limiting
define('RATE_LIMIT_REQUESTS', 60);
define('RATE_LIMIT_WINDOW', 60); // seconds

// Security Settings
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_LIFETIME', 3600 * 24); // 24 hours
define('TOKEN_LIFETIME', 3600); // 1 hour

// Application Settings
define('APP_NAME', 'ACT Test Prep');
define('APP_VERSION', '1.0.0');
define('APP_URL', '/');

// XP and Gamification Settings
define('XP_VALUES', [
    'lesson_completed' => 50,
    'quiz_completed' => 30,
    'quiz_perfect' => 100,
    'practice_test' => 200,
    'essay_submitted' => 75,
    'flashcard_set' => 25,
    'daily_login' => 10,
    'streak_bonus' => 5 // per day
]);

define('LEVEL_THRESHOLDS', [
    1 => 0,
    2 => 100,
    3 => 300,
    4 => 600,
    5 => 1000,
    6 => 1500,
    7 => 2200,
    8 => 3000,
    9 => 4000,
    10 => 5500,
    11 => 7500,
    12 => 10000,
    13 => 13000,
    14 => 17000,
    15 => 22000,
    16 => 28000,
    17 => 35000,
    18 => 43000,
    19 => 52000,
    20 => 62000
]);

// Achievements
define('ACHIEVEMENTS', [
    'first_lesson' => [
        'name' => 'First Steps',
        'description' => 'Complete your first lesson',
        'icon' => '📚',
        'xp' => 25
    ],
    'quiz_ace' => [
        'name' => 'Quiz Ace',
        'description' => 'Score 100% on a quiz',
        'icon' => '🎯',
        'xp' => 50
    ],
    'practice_test' => [
        'name' => 'Test Taker',
        'description' => 'Complete a practice test',
        'icon' => '📝',
        'xp' => 100
    ],
    'week_streak' => [
        'name' => 'Week Warrior',
        'description' => 'Maintain a 7-day study streak',
        'icon' => '🔥',
        'xp' => 75
    ],
    'math_master' => [
        'name' => 'Math Master',
        'description' => 'Score 30+ on Math section',
        'icon' => '🧮',
        'xp' => 150
    ],
    'english_expert' => [
        'name' => 'English Expert',
        'description' => 'Score 30+ on English section',
        'icon' => '📖',
        'xp' => 150
    ],
    'reading_rockstar' => [
        'name' => 'Reading Rockstar',
        'description' => 'Score 30+ on Reading section',
        'icon' => '📕',
        'xp' => 150
    ],
    'science_star' => [
        'name' => 'Science Star',
        'description' => 'Score 30+ on Science section',
        'icon' => '🔬',
        'xp' => 150
    ],
    'essay_excellence' => [
        'name' => 'Essay Excellence',
        'description' => 'Score 10+ on Writing',
        'icon' => '✍️',
        'xp' => 100
    ],
    'flashcard_master' => [
        'name' => 'Flashcard Master',
        'description' => 'Master 100 flashcards',
        'icon' => '🃏',
        'xp' => 75
    ]
]);

// Helper function to get model ID from key
function getModelId($key) {
    $models = AI_MODELS;
    return $models[$key]['id'] ?? $models[DEFAULT_AI_MODEL]['id'];
}

// Helper function to format JSON response
function jsonResponse($success, $data = null, $message = '', $errors = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
        'errors' => $errors
    ]);
    exit;
}

// CORS headers for API
function setCorsHeaders() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }
}
