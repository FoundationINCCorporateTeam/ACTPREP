<?php
/**
 * ACT Test Prep Application - Main API Router
 * 
 * Handles all API requests
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/ai-client.php';
require_once __DIR__ . '/poll-jobs.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

// Set CORS headers
setCorsHeaders();

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Parse JSON body for POST requests
$input = [];
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true) ?? $_POST;
}

// Rate limiting
$clientIp = getClientIp();
if (!checkRateLimit($clientIp)) {
    jsonResponse(false, null, 'Rate limit exceeded. Please try again later.');
}

// Initialize managers
$auth = new AuthManager();
$pollJobManager = new PollJobManager();

// Route actions
try {
    switch ($action) {
        // ==================== Authentication ====================
        case 'login':
            handleLogin($auth, $input);
            break;
            
        case 'register':
            handleRegister($auth, $input);
            break;
            
        case 'logout':
            handleLogout($auth);
            break;
            
        case 'check_auth':
            handleCheckAuth($auth);
            break;
            
        case 'get_current_user':
            handleGetCurrentUser($auth);
            break;
            
        // ==================== Models & Config ====================
        case 'get_models':
            handleGetModels();
            break;
            
        case 'get_subjects':
            handleGetSubjects();
            break;
            
        case 'get_topics':
            handleGetTopics($input);
            break;
            
        // ==================== Lessons ====================
        case 'generate_lesson':
            $auth->requireAuth();
            handleGenerateLesson($pollJobManager, $auth, $input);
            break;
            
        case 'save_lesson':
            $auth->requireAuth();
            handleSaveLesson($auth, $input);
            break;
            
        case 'get_lesson':
            $auth->requireAuth();
            handleGetLesson($auth, $input);
            break;
            
        case 'get_lessons':
            $auth->requireAuth();
            handleGetLessons($auth, $input);
            break;
            
        case 'delete_lesson':
            $auth->requireAuth();
            handleDeleteLesson($auth, $input);
            break;
            
        case 'mark_lesson_completed':
            $auth->requireAuth();
            handleMarkLessonCompleted($auth, $input);
            break;
            
        // ==================== Quizzes ====================
        case 'generate_quiz':
            $auth->requireAuth();
            handleGenerateQuiz($pollJobManager, $auth, $input);
            break;
            
        case 'save_quiz':
            $auth->requireAuth();
            handleSaveQuiz($auth, $input);
            break;
            
        case 'get_quiz':
            $auth->requireAuth();
            handleGetQuiz($auth, $input);
            break;
            
        case 'get_quizzes':
            $auth->requireAuth();
            handleGetQuizzes($auth);
            break;
            
        case 'grade_quiz':
            $auth->requireAuth();
            handleGradeQuiz($auth, $input);
            break;
            
        case 'get_quiz_results':
            $auth->requireAuth();
            handleGetQuizResults($auth, $input);
            break;
            
        // ==================== Practice Tests ====================
        case 'generate_practice_test':
            $auth->requireAuth();
            handleGeneratePracticeTest($pollJobManager, $auth, $input);
            break;
            
        case 'save_test':
            $auth->requireAuth();
            handleSaveTest($auth, $input);
            break;
            
        case 'get_test':
            $auth->requireAuth();
            handleGetTest($auth, $input);
            break;
            
        case 'get_tests':
            $auth->requireAuth();
            handleGetTests($auth);
            break;
            
        case 'grade_test':
            $auth->requireAuth();
            handleGradeTest($auth, $input);
            break;
            
        // ==================== Chat ====================
        case 'chat_message':
            $auth->requireAuth();
            handleChatMessage($pollJobManager, $auth, $input);
            break;
            
        case 'get_chat_history':
            $auth->requireAuth();
            handleGetChatHistory($auth, $input);
            break;
            
        case 'get_conversations':
            $auth->requireAuth();
            handleGetConversations($auth);
            break;
            
        case 'create_conversation':
            $auth->requireAuth();
            handleCreateConversation($auth, $input);
            break;
            
        case 'clear_chat':
            $auth->requireAuth();
            handleClearChat($auth, $input);
            break;
            
        // ==================== Study Plans ====================
        case 'generate_study_plan':
            $auth->requireAuth();
            handleGenerateStudyPlan($pollJobManager, $auth, $input);
            break;
            
        case 'get_study_plan':
            $auth->requireAuth();
            handleGetStudyPlan($auth, $input);
            break;
            
        case 'get_study_plans':
            $auth->requireAuth();
            handleGetStudyPlans($auth);
            break;
            
        case 'update_study_plan':
            $auth->requireAuth();
            handleUpdateStudyPlan($auth, $input);
            break;
            
        case 'complete_task':
            $auth->requireAuth();
            handleCompleteTask($auth, $input);
            break;
            
        // ==================== Essays ====================
        case 'generate_essay_prompt':
            $auth->requireAuth();
            handleGenerateEssayPrompt($pollJobManager, $auth, $input);
            break;
            
        case 'save_essay':
            $auth->requireAuth();
            handleSaveEssay($auth, $input);
            break;
            
        case 'grade_essay':
            $auth->requireAuth();
            handleGradeEssay($pollJobManager, $auth, $input);
            break;
            
        case 'get_essays':
            $auth->requireAuth();
            handleGetEssays($auth);
            break;
            
        // ==================== Flashcards ====================
        case 'generate_flashcards':
            $auth->requireAuth();
            handleGenerateFlashcards($pollJobManager, $auth, $input);
            break;
            
        case 'save_flashcard_set':
            $auth->requireAuth();
            handleSaveFlashcardSet($auth, $input);
            break;
            
        case 'get_flashcard_sets':
            $auth->requireAuth();
            handleGetFlashcardSets($auth);
            break;
            
        case 'get_flashcard_set':
            $auth->requireAuth();
            handleGetFlashcardSet($auth, $input);
            break;
            
        case 'update_card_mastery':
            $auth->requireAuth();
            handleUpdateCardMastery($auth, $input);
            break;
            
        // ==================== Progress ====================
        case 'get_progress':
            $auth->requireAuth();
            handleGetProgress($auth);
            break;
            
        case 'update_progress':
            $auth->requireAuth();
            handleUpdateProgress($auth, $input);
            break;
            
        case 'get_statistics':
            $auth->requireAuth();
            handleGetStatistics($auth);
            break;
            
        // ==================== Poll Jobs ====================
        case 'create_poll_job':
            $auth->requireAuth();
            handleCreatePollJob($pollJobManager, $auth, $input);
            break;
            
        case 'check_poll_job':
            $auth->requireAuth();
            handleCheckPollJob($pollJobManager, $input);
            break;
            
        case 'get_poll_result':
            $auth->requireAuth();
            handleGetPollResult($pollJobManager, $input);
            break;
            
        case 'cancel_poll_job':
            $auth->requireAuth();
            handleCancelPollJob($pollJobManager, $auth, $input);
            break;
            
        // ==================== Settings ====================
        case 'get_settings':
            $auth->requireAuth();
            handleGetSettings($auth);
            break;
            
        case 'save_settings':
            $auth->requireAuth();
            handleSaveSettings($auth, $input);
            break;
            
        // ==================== User Profile ====================
        case 'get_profile':
            $auth->requireAuth();
            handleGetProfile($auth);
            break;
            
        case 'update_profile':
            $auth->requireAuth();
            handleUpdateProfile($auth, $input);
            break;
            
        case 'change_password':
            $auth->requireAuth();
            handleChangePassword($auth, $input);
            break;
            
        // ==================== Export/Import ====================
        case 'export_data':
            $auth->requireAuth();
            handleExportData($auth);
            break;
            
        case 'import_data':
            $auth->requireAuth();
            handleImportData($auth, $input);
            break;
            
        default:
            jsonResponse(false, null, 'Unknown action: ' . $action);
    }
} catch (Exception $e) {
    logError($e->getMessage(), ['action' => $action, 'trace' => $e->getTraceAsString()]);
    jsonResponse(false, null, 'An error occurred: ' . $e->getMessage());
}

// ==================== Handler Functions ====================

// Authentication Handlers
function handleLogin($auth, $input) {
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    $result = $auth->login($email, $password);
    
    if (isset($result['error'])) {
        jsonResponse(false, null, $result['error']);
    }
    
    jsonResponse(true, $result['user'], 'Login successful');
}

function handleRegister($auth, $input) {
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $name = $input['name'] ?? '';
    
    $result = $auth->register($email, $password, $name);
    
    if (isset($result['error'])) {
        jsonResponse(false, null, $result['error']);
    }
    
    jsonResponse(true, $result['user'], 'Registration successful');
}

function handleLogout($auth) {
    $auth->logout();
    jsonResponse(true, null, 'Logged out successfully');
}

function handleCheckAuth($auth) {
    jsonResponse(true, ['authenticated' => $auth->isLoggedIn()]);
}

function handleGetCurrentUser($auth) {
    $user = $auth->getCurrentUser();
    jsonResponse(true, $user);
}

// Models & Config Handlers
function handleGetModels() {
    jsonResponse(true, AI_MODELS);
}

function handleGetSubjects() {
    jsonResponse(true, ACT_SUBJECTS);
}

function handleGetTopics($input) {
    $subject = $input['subject'] ?? '';
    
    if (isset(ACT_SUBJECTS[$subject])) {
        jsonResponse(true, ACT_SUBJECTS[$subject]['topics']);
    }
    
    jsonResponse(false, null, 'Invalid subject');
}

// Lesson Handlers
function handleGenerateLesson($pollJobManager, $auth, $input) {
    $userId = $auth->getCurrentUserId();
    
    $jobId = $pollJobManager->createJob('generate_lesson', [
        'subject' => $input['subject'] ?? 'math',
        'topic' => $input['topic'] ?? 'General',
        'difficulty' => $input['difficulty'] ?? 'intermediate',
        'length' => $input['length'] ?? 'medium',
        'focus_areas' => $input['focus_areas'] ?? [],
        'model' => $input['model'] ?? null
    ], $userId);
    
    jsonResponse(true, ['job_id' => $jobId], 'Lesson generation started');
}

function handleSaveLesson($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $lessonManager = new LessonManager();
    
    $lesson = $lessonManager->create([
        'user_id' => $userId,
        'title' => $input['title'] ?? 'Untitled Lesson',
        'subject' => $input['subject'] ?? '',
        'topic' => $input['topic'] ?? '',
        'difficulty' => $input['difficulty'] ?? 'intermediate',
        'content' => $input['content'] ?? '',
        'completed' => false,
        'notes' => [],
        'bookmarked' => false
    ]);
    
    // Update progress
    $progressManager = new ProgressManager();
    $progressManager->recordActivity($userId, 'lessons', ['count' => 1]);
    
    // Add XP
    $userManager = new UserManager();
    $userManager->addXP($userId, XP_VALUES['lesson_completed']);
    
    jsonResponse(true, $lesson, 'Lesson saved successfully');
}

function handleGetLesson($auth, $input) {
    $lessonId = $input['id'] ?? $_GET['id'] ?? '';
    $lessonManager = new LessonManager();
    
    $lesson = $lessonManager->getById($lessonId);
    
    if (!$lesson) {
        jsonResponse(false, null, 'Lesson not found');
    }
    
    jsonResponse(true, $lesson);
}

function handleGetLessons($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $lessonManager = new LessonManager();
    
    $lessons = $lessonManager->getByUser($userId);
    
    // Apply filters
    if (!empty($input['subject'])) {
        $lessons = array_filter($lessons, fn($l) => $l['subject'] === $input['subject']);
    }
    
    // Sort by date (newest first)
    usort($lessons, fn($a, $b) => ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0));
    
    jsonResponse(true, array_values($lessons));
}

function handleDeleteLesson($auth, $input) {
    $lessonId = $input['id'] ?? '';
    $lessonManager = new LessonManager();
    
    $lessonManager->delete($lessonId);
    
    jsonResponse(true, null, 'Lesson deleted');
}

function handleMarkLessonCompleted($auth, $input) {
    $lessonId = $input['id'] ?? '';
    $userId = $auth->getCurrentUserId();
    $lessonManager = new LessonManager();
    
    $lesson = $lessonManager->markCompleted($lessonId, $userId);
    
    jsonResponse(true, $lesson, 'Lesson marked as completed');
}

// Quiz Handlers
function handleGenerateQuiz($pollJobManager, $auth, $input) {
    $userId = $auth->getCurrentUserId();
    
    $jobId = $pollJobManager->createJob('generate_quiz', [
        'subject' => $input['subject'] ?? 'math',
        'topic' => $input['topic'] ?? 'General',
        'question_count' => $input['question_count'] ?? 10,
        'difficulty' => $input['difficulty'] ?? 'intermediate',
        'question_types' => $input['question_types'] ?? ['multiple_choice'],
        'model' => $input['model'] ?? null
    ], $userId);
    
    jsonResponse(true, ['job_id' => $jobId], 'Quiz generation started');
}

function handleSaveQuiz($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $quizManager = new QuizManager();
    
    $quiz = $quizManager->create([
        'user_id' => $userId,
        'title' => $input['title'] ?? 'Practice Quiz',
        'subject' => $input['subject'] ?? '',
        'topic' => $input['topic'] ?? '',
        'difficulty' => $input['difficulty'] ?? 'intermediate',
        'questions' => $input['questions'] ?? [],
        'time_limit' => $input['time_limit'] ?? null,
        'completed' => false,
        'score' => null,
        'user_answers' => []
    ]);
    
    jsonResponse(true, $quiz, 'Quiz saved successfully');
}

function handleGetQuiz($auth, $input) {
    $quizId = $input['id'] ?? $_GET['id'] ?? '';
    $quizManager = new QuizManager();
    
    $quiz = $quizManager->getById($quizId);
    
    if (!$quiz) {
        jsonResponse(false, null, 'Quiz not found');
    }
    
    jsonResponse(true, $quiz);
}

function handleGetQuizzes($auth) {
    $userId = $auth->getCurrentUserId();
    $quizManager = new QuizManager();
    
    $quizzes = $quizManager->getByUser($userId);
    
    usort($quizzes, fn($a, $b) => ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0));
    
    jsonResponse(true, array_values($quizzes));
}

function handleGradeQuiz($auth, $input) {
    $quizId = $input['quiz_id'] ?? '';
    $answers = $input['answers'] ?? [];
    $timeTaken = $input['time_taken'] ?? 0;
    
    $quizManager = new QuizManager();
    $quiz = $quizManager->getById($quizId);
    
    if (!$quiz) {
        jsonResponse(false, null, 'Quiz not found');
    }
    
    // Calculate score
    $score = 0;
    $questions = $quiz['questions'] ?? [];
    
    foreach ($questions as $index => $question) {
        $userAnswer = $answers[$index] ?? null;
        $correctAnswer = $question['correct_answer'] ?? null;
        
        if ($userAnswer === $correctAnswer) {
            $score++;
        }
    }
    
    // Save results
    $result = $quizManager->saveResult($quizId, $answers, $score, $timeTaken);
    
    // Update progress
    $userId = $auth->getCurrentUserId();
    $progressManager = new ProgressManager();
    $progressManager->recordActivity($userId, 'quizzes', ['count' => 1]);
    $progressManager->updateSubjectProgress($userId, $quiz['subject'], $score, count($questions));
    
    // Add XP
    $userManager = new UserManager();
    $xp = XP_VALUES['quiz_completed'];
    if ($score === count($questions)) {
        $xp += XP_VALUES['quiz_perfect'];
        $userManager->addAchievement($userId, 'quiz_ace');
    }
    $userManager->addXP($userId, $xp);
    
    jsonResponse(true, [
        'score' => $score,
        'total' => count($questions),
        'percentage' => calculatePercentage($score, count($questions)),
        'time_taken' => $timeTaken,
        'quiz' => $result
    ], 'Quiz graded successfully');
}

function handleGetQuizResults($auth, $input) {
    $quizId = $input['id'] ?? $_GET['id'] ?? '';
    $quizManager = new QuizManager();
    
    $quiz = $quizManager->getById($quizId);
    
    if (!$quiz || !$quiz['completed']) {
        jsonResponse(false, null, 'Quiz results not found');
    }
    
    jsonResponse(true, $quiz);
}

// Practice Test Handlers
function handleGeneratePracticeTest($pollJobManager, $auth, $input) {
    $userId = $auth->getCurrentUserId();
    
    $jobId = $pollJobManager->createJob('generate_practice_test', [
        'sections' => $input['sections'] ?? ['english', 'math', 'reading', 'science'],
        'model' => $input['model'] ?? null
    ], $userId);
    
    jsonResponse(true, ['job_id' => $jobId], 'Practice test generation started');
}

function handleSaveTest($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $testManager = new TestManager();
    
    $test = $testManager->create([
        'user_id' => $userId,
        'title' => $input['title'] ?? 'Practice Test',
        'sections' => $input['sections'] ?? [],
        'completed' => false,
        'section_results' => [],
        'composite_score' => null
    ]);
    
    jsonResponse(true, $test, 'Test saved successfully');
}

function handleGetTest($auth, $input) {
    $testId = $input['id'] ?? $_GET['id'] ?? '';
    $testManager = new TestManager();
    
    $test = $testManager->getById($testId);
    
    if (!$test) {
        jsonResponse(false, null, 'Test not found');
    }
    
    jsonResponse(true, $test);
}

function handleGetTests($auth) {
    $userId = $auth->getCurrentUserId();
    $testManager = new TestManager();
    
    $tests = $testManager->getByUser($userId);
    
    usort($tests, fn($a, $b) => ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0));
    
    jsonResponse(true, array_values($tests));
}

function handleGradeTest($auth, $input) {
    $testId = $input['test_id'] ?? '';
    $sectionAnswers = $input['section_answers'] ?? [];
    
    $testManager = new TestManager();
    $test = $testManager->getById($testId);
    
    if (!$test) {
        jsonResponse(false, null, 'Test not found');
    }
    
    $sectionResults = [];
    
    foreach ($test['sections'] as $sectionName => $sectionData) {
        $questions = $sectionData['questions'] ?? [];
        $answers = $sectionAnswers[$sectionName] ?? [];
        
        $correct = 0;
        foreach ($questions as $index => $question) {
            if (isset($answers[$index]) && $answers[$index] === ($question['correct_answer'] ?? null)) {
                $correct++;
            }
        }
        
        $total = count($questions);
        $scaledScore = convertToActScale($correct, $total);
        
        $sectionResults[$sectionName] = [
            'correct' => $correct,
            'total' => $total,
            'percentage' => calculatePercentage($correct, $total),
            'score' => $scaledScore,
            'answers' => $answers
        ];
    }
    
    $result = $testManager->saveResult($testId, $sectionResults);
    
    // Update progress
    $userId = $auth->getCurrentUserId();
    $progressManager = new ProgressManager();
    $progressManager->recordActivity($userId, 'tests', ['count' => 1]);
    
    // Add XP
    $userManager = new UserManager();
    $userManager->addXP($userId, XP_VALUES['practice_test']);
    $userManager->addAchievement($userId, 'practice_test');
    
    jsonResponse(true, $result, 'Test graded successfully');
}

// Chat Handlers
function handleChatMessage($pollJobManager, $auth, $input) {
    $userId = $auth->getCurrentUserId();
    $conversationId = $input['conversation_id'] ?? null;
    $message = $input['message'] ?? '';
    
    $chatManager = new ChatManager();
    
    // Create or get conversation
    if (!$conversationId) {
        $conversation = $chatManager->createConversation($userId);
        $conversationId = $conversation['id'];
    }
    
    // Add user message
    $chatManager->addMessage($conversationId, 'user', $message);
    
    // Get conversation history
    $conversation = $chatManager->getById($conversationId);
    $history = $conversation['messages'] ?? [];
    
    // Create poll job for AI response
    $jobId = $pollJobManager->createJob('chat_message', [
        'history' => $history,
        'message' => $message,
        'context' => $input['context'] ?? [],
        'model' => $input['model'] ?? null
    ], $userId);
    
    jsonResponse(true, [
        'job_id' => $jobId,
        'conversation_id' => $conversationId
    ], 'Chat message sent');
}

function handleGetChatHistory($auth, $input) {
    $conversationId = $input['conversation_id'] ?? $_GET['conversation_id'] ?? '';
    $chatManager = new ChatManager();
    
    $conversation = $chatManager->getById($conversationId);
    
    if (!$conversation) {
        jsonResponse(false, null, 'Conversation not found');
    }
    
    jsonResponse(true, $conversation);
}

function handleGetConversations($auth) {
    $userId = $auth->getCurrentUserId();
    $chatManager = new ChatManager();
    
    $conversations = $chatManager->getByUser($userId);
    
    usort($conversations, fn($a, $b) => ($b['last_message_at'] ?? 0) - ($a['last_message_at'] ?? 0));
    
    jsonResponse(true, array_values($conversations));
}

function handleCreateConversation($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $chatManager = new ChatManager();
    
    $conversation = $chatManager->createConversation($userId, $input['title'] ?? 'New Chat');
    
    jsonResponse(true, $conversation, 'Conversation created');
}

function handleClearChat($auth, $input) {
    $conversationId = $input['conversation_id'] ?? '';
    $chatManager = new ChatManager();
    
    $chatManager->update($conversationId, ['messages' => []]);
    
    jsonResponse(true, null, 'Chat cleared');
}

// Study Plan Handlers
function handleGenerateStudyPlan($pollJobManager, $auth, $input) {
    $userId = $auth->getCurrentUserId();
    
    $jobId = $pollJobManager->createJob('generate_study_plan', [
        'current_score' => $input['current_score'] ?? null,
        'target_score' => $input['target_score'] ?? 30,
        'test_date' => $input['test_date'] ?? null,
        'hours_per_week' => $input['hours_per_week'] ?? 10,
        'weak_areas' => $input['weak_areas'] ?? [],
        'strong_areas' => $input['strong_areas'] ?? [],
        'model' => $input['model'] ?? null
    ], $userId);
    
    jsonResponse(true, ['job_id' => $jobId], 'Study plan generation started');
}

function handleGetStudyPlan($auth, $input) {
    $planId = $input['id'] ?? $_GET['id'] ?? '';
    $studyPlanManager = new StudyPlanManager();
    
    $plan = $studyPlanManager->getById($planId);
    
    if (!$plan) {
        jsonResponse(false, null, 'Study plan not found');
    }
    
    jsonResponse(true, $plan);
}

function handleGetStudyPlans($auth) {
    $userId = $auth->getCurrentUserId();
    $studyPlanManager = new StudyPlanManager();
    
    $plans = $studyPlanManager->getByUser($userId);
    
    jsonResponse(true, array_values($plans));
}

function handleUpdateStudyPlan($auth, $input) {
    $planId = $input['id'] ?? '';
    $studyPlanManager = new StudyPlanManager();
    
    $plan = $studyPlanManager->update($planId, $input['updates'] ?? []);
    
    jsonResponse(true, $plan, 'Study plan updated');
}

function handleCompleteTask($auth, $input) {
    $planId = $input['plan_id'] ?? '';
    $weekIndex = $input['week_index'] ?? 0;
    $dayIndex = $input['day_index'] ?? 0;
    $taskIndex = $input['task_index'] ?? 0;
    
    $studyPlanManager = new StudyPlanManager();
    $result = $studyPlanManager->completeTask($planId, $weekIndex, $dayIndex, $taskIndex);
    
    jsonResponse(true, $result, 'Task completed');
}

// Essay Handlers
function handleGenerateEssayPrompt($pollJobManager, $auth, $input) {
    $userId = $auth->getCurrentUserId();
    
    $jobId = $pollJobManager->createJob('generate_essay_prompt', [
        'model' => $input['model'] ?? null
    ], $userId);
    
    jsonResponse(true, ['job_id' => $jobId], 'Essay prompt generation started');
}

function handleSaveEssay($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $essayManager = new EssayManager();
    
    $essay = $essayManager->create([
        'user_id' => $userId,
        'prompt' => $input['prompt'] ?? '',
        'content' => $input['content'] ?? '',
        'word_count' => countWords($input['content'] ?? ''),
        'graded' => false,
        'scores' => null,
        'feedback' => null
    ]);
    
    jsonResponse(true, $essay, 'Essay saved successfully');
}

function handleGradeEssay($pollJobManager, $auth, $input) {
    $userId = $auth->getCurrentUserId();
    
    $jobId = $pollJobManager->createJob('grade_essay', [
        'essay' => $input['content'] ?? '',
        'prompt' => $input['prompt'] ?? '',
        'model' => $input['model'] ?? null
    ], $userId);
    
    jsonResponse(true, ['job_id' => $jobId], 'Essay grading started');
}

function handleGetEssays($auth) {
    $userId = $auth->getCurrentUserId();
    $essayManager = new EssayManager();
    
    $essays = $essayManager->getByUser($userId);
    
    usort($essays, fn($a, $b) => ($b['created_at'] ?? 0) - ($a['created_at'] ?? 0));
    
    jsonResponse(true, array_values($essays));
}

// Flashcard Handlers
function handleGenerateFlashcards($pollJobManager, $auth, $input) {
    $userId = $auth->getCurrentUserId();
    
    $jobId = $pollJobManager->createJob('generate_flashcards', [
        'subject' => $input['subject'] ?? 'math',
        'topic' => $input['topic'] ?? 'General',
        'count' => $input['count'] ?? 10,
        'model' => $input['model'] ?? null
    ], $userId);
    
    jsonResponse(true, ['job_id' => $jobId], 'Flashcard generation started');
}

function handleSaveFlashcardSet($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $flashcardManager = new FlashcardManager();
    
    $set = $flashcardManager->create([
        'user_id' => $userId,
        'title' => $input['title'] ?? 'Flashcard Set',
        'subject' => $input['subject'] ?? '',
        'topic' => $input['topic'] ?? '',
        'cards' => $input['cards'] ?? []
    ]);
    
    jsonResponse(true, $set, 'Flashcard set saved');
}

function handleGetFlashcardSets($auth) {
    $userId = $auth->getCurrentUserId();
    $flashcardManager = new FlashcardManager();
    
    $sets = $flashcardManager->getByUser($userId);
    
    jsonResponse(true, array_values($sets));
}

function handleGetFlashcardSet($auth, $input) {
    $setId = $input['id'] ?? $_GET['id'] ?? '';
    $flashcardManager = new FlashcardManager();
    
    $set = $flashcardManager->getById($setId);
    
    if (!$set) {
        jsonResponse(false, null, 'Flashcard set not found');
    }
    
    jsonResponse(true, $set);
}

function handleUpdateCardMastery($auth, $input) {
    $setId = $input['set_id'] ?? '';
    $cardIndex = $input['card_index'] ?? 0;
    $mastery = $input['mastery'] ?? 'learning';
    
    $flashcardManager = new FlashcardManager();
    $result = $flashcardManager->updateCardMastery($setId, $cardIndex, $mastery);
    
    jsonResponse(true, $result, 'Card mastery updated');
}

// Progress Handlers
function handleGetProgress($auth) {
    $userId = $auth->getCurrentUserId();
    $progressManager = new ProgressManager();
    
    $progress = $progressManager->getByUser($userId);
    
    if (!$progress) {
        $progress = $progressManager->initializeForUser($userId);
    }
    
    jsonResponse(true, $progress);
}

function handleUpdateProgress($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $progressManager = new ProgressManager();
    
    if (isset($input['activity_type'])) {
        $progressManager->recordActivity($userId, $input['activity_type'], $input);
    }
    
    jsonResponse(true, null, 'Progress updated');
}

function handleGetStatistics($auth) {
    $userId = $auth->getCurrentUserId();
    
    $quizManager = new QuizManager();
    $testManager = new TestManager();
    $lessonManager = new LessonManager();
    $progressManager = new ProgressManager();
    $userManager = new UserManager();
    
    $user = $userManager->getById($userId);
    $progress = $progressManager->getByUser($userId);
    
    $stats = [
        'user' => [
            'xp' => $user['xp'] ?? 0,
            'level' => $user['level'] ?? 1,
            'streak' => $user['streak'] ?? 0,
            'achievements' => $user['achievements'] ?? []
        ],
        'quizzes' => $quizManager->getStats($userId),
        'tests' => $testManager->getStats($userId),
        'lessons' => [
            'total' => count($lessonManager->getByUser($userId)),
            'completed' => count(array_filter($lessonManager->getByUser($userId), fn($l) => $l['completed'] ?? false))
        ],
        'subjects' => $progress['subjects'] ?? [],
        'daily_activity' => $progress['daily_activity'] ?? []
    ];
    
    jsonResponse(true, $stats);
}

// Poll Job Handlers
function handleCreatePollJob($pollJobManager, $auth, $input) {
    $userId = $auth->getCurrentUserId();
    $jobType = $input['job_type'] ?? '';
    $params = $input['params'] ?? [];
    
    $jobId = $pollJobManager->createJob($jobType, $params, $userId);
    
    jsonResponse(true, ['job_id' => $jobId], 'Job created');
}

function handleCheckPollJob($pollJobManager, $input) {
    $jobId = $input['job_id'] ?? $_GET['job_id'] ?? '';
    
    $status = $pollJobManager->checkJob($jobId);
    
    jsonResponse(true, $status);
}

function handleGetPollResult($pollJobManager, $input) {
    $jobId = $input['job_id'] ?? $_GET['job_id'] ?? '';
    
    $result = $pollJobManager->getResult($jobId);
    
    jsonResponse(true, $result);
}

function handleCancelPollJob($pollJobManager, $auth, $input) {
    $jobId = $input['job_id'] ?? '';
    $userId = $auth->getCurrentUserId();
    
    $result = $pollJobManager->cancelJob($jobId, $userId);
    
    if (isset($result['error'])) {
        jsonResponse(false, null, $result['error']);
    }
    
    jsonResponse(true, null, 'Job cancelled');
}

// Settings Handlers
function handleGetSettings($auth) {
    $userId = $auth->getCurrentUserId();
    $settingsManager = new SettingsManager();
    
    $settings = $settingsManager->getByUser($userId);
    
    if (!$settings) {
        $settings = $settingsManager->initializeForUser($userId);
    }
    
    jsonResponse(true, $settings);
}

function handleSaveSettings($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $settingsManager = new SettingsManager();
    
    $settings = $settingsManager->getByUser($userId);
    
    if (!$settings) {
        $settings = $settingsManager->initializeForUser($userId);
    }
    
    $updated = $settingsManager->update($settings['id'], $input);
    
    jsonResponse(true, $updated, 'Settings saved');
}

// Profile Handlers
function handleGetProfile($auth) {
    $user = $auth->getCurrentUser();
    jsonResponse(true, $user);
}

function handleUpdateProfile($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $result = $auth->updateProfile($userId, $input);
    
    if (isset($result['error'])) {
        jsonResponse(false, null, $result['error']);
    }
    
    jsonResponse(true, $result, 'Profile updated');
}

function handleChangePassword($auth, $input) {
    $userId = $auth->getCurrentUserId();
    $currentPassword = $input['current_password'] ?? '';
    $newPassword = $input['new_password'] ?? '';
    
    $result = $auth->changePassword($userId, $currentPassword, $newPassword);
    
    if (isset($result['error'])) {
        jsonResponse(false, null, $result['error']);
    }
    
    jsonResponse(true, null, 'Password changed successfully');
}

// Export/Import Handlers
function handleExportData($auth) {
    $userId = $auth->getCurrentUserId();
    $data = $auth->exportUserData($userId);
    
    if (isset($data['error'])) {
        jsonResponse(false, null, $data['error']);
    }
    
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="act-prep-export-' . date('Y-m-d') . '.json"');
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

function handleImportData($auth, $input) {
    // TODO: Implement data import
    jsonResponse(false, null, 'Import not yet implemented');
}
