<?php
/**
 * ACT Test Prep Application - Poll Jobs Manager
 * 
 * Handles long-running AI requests without timeout
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/ai-client.php';

/**
 * Poll Job Manager Class
 */
class PollJobManager {
    private $db;
    private $aiClient;
    
    public function __construct() {
        $this->db = new DatabaseManager(DB_FILES['poll_jobs']);
        $this->aiClient = new NanoGPTClient();
    }
    
    /**
     * Create a new poll job
     */
    public function createJob($type, $params, $userId) {
        $jobId = 'job_' . uniqid('', true);
        
        $job = [
            'id' => $jobId,
            'type' => $type,
            'params' => $params,
            'user_id' => $userId,
            'status' => 'pending',
            'progress' => 0,
            'result' => null,
            'error' => null,
            'created_at' => time(),
            'started_at' => null,
            'completed_at' => null
        ];
        
        $this->saveJob($job);
        
        // Process job immediately (synchronous for PHP)
        // In a production environment, this would be handled by a background worker
        $this->processJob($jobId);
        
        return $jobId;
    }
    
    /**
     * Get job by ID
     */
    public function getJob($jobId) {
        return $this->db->getById($jobId);
    }
    
    /**
     * Check job status
     */
    public function checkJob($jobId) {
        $job = $this->getJob($jobId);
        
        if (!$job) {
            return ['error' => 'Job not found'];
        }
        
        return [
            'id' => $job['id'],
            'status' => $job['status'],
            'progress' => $job['progress'],
            'error' => $job['error'],
            'created_at' => $job['created_at'],
            'completed_at' => $job['completed_at']
        ];
    }
    
    /**
     * Get job result
     */
    public function getResult($jobId) {
        $job = $this->getJob($jobId);
        
        if (!$job) {
            return ['error' => 'Job not found'];
        }
        
        if ($job['status'] !== 'completed') {
            return [
                'status' => $job['status'],
                'progress' => $job['progress'],
                'error' => $job['error']
            ];
        }
        
        return [
            'status' => 'completed',
            'result' => $job['result']
        ];
    }
    
    /**
     * Process a job
     */
    public function processJob($jobId) {
        $job = $this->getJob($jobId);
        
        if (!$job || $job['status'] !== 'pending') {
            return;
        }
        
        // Update status to processing
        $job['status'] = 'processing';
        $job['started_at'] = time();
        $job['progress'] = 10;
        $this->saveJob($job);
        
        try {
            $result = $this->executeJob($job);
            
            $job['status'] = 'completed';
            $job['progress'] = 100;
            $job['result'] = $result;
            $job['completed_at'] = time();
        } catch (Exception $e) {
            $job['status'] = 'failed';
            $job['error'] = $e->getMessage();
            $job['completed_at'] = time();
        }
        
        $this->saveJob($job);
    }
    
    /**
     * Execute job based on type
     */
    private function executeJob($job) {
        $type = $job['type'];
        $params = $job['params'];
        
        switch ($type) {
            case 'generate_lesson':
                return $this->executeGenerateLesson($params);
                
            case 'generate_quiz':
                return $this->executeGenerateQuiz($params);
                
            case 'generate_practice_test':
                return $this->executeGeneratePracticeTest($params);
                
            case 'generate_study_plan':
                return $this->executeGenerateStudyPlan($params);
                
            case 'generate_essay_prompt':
                return $this->executeGenerateEssayPrompt($params);
                
            case 'grade_essay':
                return $this->executeGradeEssay($params);
                
            case 'generate_flashcards':
                return $this->executeGenerateFlashcards($params);
                
            case 'chat_message':
                return $this->executeChatMessage($params);
                
            default:
                throw new Exception('Unknown job type: ' . $type);
        }
    }
    
    /**
     * Execute lesson generation
     */
    private function executeGenerateLesson($params) {
        $subject = $params['subject'] ?? 'math';
        $topic = $params['topic'] ?? 'General';
        $difficulty = $params['difficulty'] ?? 'intermediate';
        $length = $params['length'] ?? 'medium';
        $focusAreas = $params['focus_areas'] ?? [];
        $model = $params['model'] ?? null;
        
        $response = $this->aiClient->generateLesson($subject, $topic, $difficulty, $length, $focusAreas, $model);
        
        if (isset($response['error'])) {
            throw new Exception($response['error']);
        }
        
        return [
            'type' => 'lesson',
            'subject' => $subject,
            'topic' => $topic,
            'difficulty' => $difficulty,
            'length' => $length,
            'content' => $response['content'],
            'model' => $response['model']
        ];
    }
    
    /**
     * Execute quiz generation
     */
    private function executeGenerateQuiz($params) {
        $subject = $params['subject'] ?? 'math';
        $topic = $params['topic'] ?? 'General';
        $questionCount = $params['question_count'] ?? 10;
        $difficulty = $params['difficulty'] ?? 'intermediate';
        $questionTypes = $params['question_types'] ?? ['multiple_choice'];
        $model = $params['model'] ?? null;
        
        $response = $this->aiClient->generateQuiz($subject, $topic, $questionCount, $difficulty, $questionTypes, $model);
        
        if (isset($response['error'])) {
            throw new Exception($response['error']);
        }
        
        $questions = $this->aiClient->parseJsonResponse($response['content']);
        
        if (!$questions) {
            throw new Exception('Failed to parse quiz questions');
        }
        
        return [
            'type' => 'quiz',
            'subject' => $subject,
            'topic' => $topic,
            'difficulty' => $difficulty,
            'questions' => $questions['questions'] ?? $questions,
            'model' => $response['model']
        ];
    }
    
    /**
     * Execute practice test generation
     */
    private function executeGeneratePracticeTest($params) {
        $sections = $params['sections'] ?? ['english', 'math', 'reading', 'science'];
        $model = $params['model'] ?? null;
        
        $testSections = [];
        
        foreach ($sections as $section) {
            $response = $this->aiClient->generatePracticeTest($section, $model);
            
            if (isset($response['error'])) {
                throw new Exception("Failed to generate {$section} section: " . $response['error']);
            }
            
            $sectionData = $this->aiClient->parseJsonResponse($response['content']);
            
            if ($sectionData) {
                $testSections[$section] = $sectionData;
            }
        }
        
        return [
            'type' => 'practice_test',
            'sections' => $testSections
        ];
    }
    
    /**
     * Execute study plan generation
     */
    private function executeGenerateStudyPlan($params) {
        $response = $this->aiClient->generateStudyPlan($params, $params['model'] ?? null);
        
        if (isset($response['error'])) {
            throw new Exception($response['error']);
        }
        
        $plan = $this->aiClient->parseJsonResponse($response['content']);
        
        if (!$plan) {
            throw new Exception('Failed to parse study plan');
        }
        
        return [
            'type' => 'study_plan',
            'plan' => $plan
        ];
    }
    
    /**
     * Execute essay prompt generation
     */
    private function executeGenerateEssayPrompt($params) {
        $response = $this->aiClient->generateEssayPrompt($params['model'] ?? null);
        
        if (isset($response['error'])) {
            throw new Exception($response['error']);
        }
        
        $prompt = $this->aiClient->parseJsonResponse($response['content']);
        
        if (!$prompt) {
            throw new Exception('Failed to parse essay prompt');
        }
        
        return [
            'type' => 'essay_prompt',
            'prompt' => $prompt
        ];
    }
    
    /**
     * Execute essay grading
     */
    private function executeGradeEssay($params) {
        $essayContent = $params['essay'] ?? '';
        $prompt = $params['prompt'] ?? '';
        $model = $params['model'] ?? null;
        
        $response = $this->aiClient->gradeEssay($essayContent, $prompt, $model);
        
        if (isset($response['error'])) {
            throw new Exception($response['error']);
        }
        
        $grade = $this->aiClient->parseJsonResponse($response['content']);
        
        if (!$grade) {
            throw new Exception('Failed to parse essay grade');
        }
        
        return [
            'type' => 'essay_grade',
            'grade' => $grade
        ];
    }
    
    /**
     * Execute flashcard generation
     */
    private function executeGenerateFlashcards($params) {
        $subject = $params['subject'] ?? 'math';
        $topic = $params['topic'] ?? 'General';
        $count = $params['count'] ?? 10;
        $model = $params['model'] ?? null;
        
        $response = $this->aiClient->generateFlashcards($subject, $topic, $count, $model);
        
        if (isset($response['error'])) {
            throw new Exception($response['error']);
        }
        
        $flashcards = $this->aiClient->parseJsonResponse($response['content']);
        
        if (!$flashcards) {
            throw new Exception('Failed to parse flashcards');
        }
        
        return [
            'type' => 'flashcards',
            'subject' => $subject,
            'topic' => $topic,
            'flashcards' => $flashcards['flashcards'] ?? $flashcards
        ];
    }
    
    /**
     * Execute chat message
     */
    private function executeChatMessage($params) {
        $history = $params['history'] ?? [];
        $message = $params['message'] ?? '';
        $context = $params['context'] ?? [];
        $model = $params['model'] ?? null;
        
        $response = $this->aiClient->tutorChat($history, $message, $context, $model);
        
        if (isset($response['error'])) {
            throw new Exception($response['error']);
        }
        
        return [
            'type' => 'chat_response',
            'content' => $response['content'],
            'model' => $response['model']
        ];
    }
    
    /**
     * Save job to database
     */
    private function saveJob($job) {
        $jobs = $this->db->getAll();
        $found = false;
        
        foreach ($jobs as &$existingJob) {
            if ($existingJob['id'] === $job['id']) {
                $existingJob = $job;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $jobs[] = $job;
        }
        
        // Save to file directly since we're not using the standard create/update
        file_put_contents(DB_FILES['poll_jobs'], json_encode(array_values($jobs), JSON_PRETTY_PRINT));
    }
    
    /**
     * Cancel a job
     */
    public function cancelJob($jobId, $userId) {
        $job = $this->getJob($jobId);
        
        if (!$job) {
            return ['error' => 'Job not found'];
        }
        
        if ($job['user_id'] !== $userId) {
            return ['error' => 'Unauthorized'];
        }
        
        if ($job['status'] === 'completed' || $job['status'] === 'failed') {
            return ['error' => 'Job already finished'];
        }
        
        $job['status'] = 'cancelled';
        $job['completed_at'] = time();
        $this->saveJob($job);
        
        return ['success' => true];
    }
    
    /**
     * Clean up old jobs
     */
    public function cleanupOldJobs($maxAge = 86400) {
        $jobs = $this->db->getAll();
        $cutoff = time() - $maxAge;
        
        $jobs = array_filter($jobs, function($job) use ($cutoff) {
            return $job['created_at'] >= $cutoff;
        });
        
        file_put_contents(DB_FILES['poll_jobs'], json_encode(array_values($jobs), JSON_PRETTY_PRINT));
    }
    
    /**
     * Get jobs by user
     */
    public function getJobsByUser($userId) {
        $jobs = $this->db->getAll();
        return array_filter($jobs, function($job) use ($userId) {
            return $job['user_id'] === $userId;
        });
    }
}
