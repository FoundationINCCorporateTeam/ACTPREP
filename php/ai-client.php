<?php
/**
 * ACT Test Prep Application - AI Client
 * 
 * Client for communicating with nano-gpt.com API
 */

require_once __DIR__ . '/config.php';

/**
 * NanoGPT API Client Class
 */
class NanoGPTClient {
    private $apiEndpoint;
    private $apiKey;
    private $defaultModel;
    private $maxRetries = 3;
    private $retryDelay = 1000; // milliseconds
    private $timeout = 120; // seconds
    private $cache = [];
    
    public function __construct() {
        $this->apiEndpoint = NANO_GPT_API_ENDPOINT;
        $this->apiKey = NANO_GPT_API_KEY;
        $this->defaultModel = getModelId(DEFAULT_AI_MODEL);
    }
    
    /**
     * Send chat completion request
     */
    public function chat($messages, $model = null, $options = []) {
        $model = $model ?: $this->defaultModel;
        
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 4096,
            'stream' => false
        ];
        
        return $this->makeRequest($payload);
    }
    
    /**
     * Generate lesson content
     */
    public function generateLesson($subject, $topic, $difficulty, $length, $focusAreas, $model = null) {
        $lengthInfo = LESSON_LENGTHS[$length] ?? LESSON_LENGTHS['medium'];
        $difficultyInfo = DIFFICULTY_LEVELS[$difficulty] ?? DIFFICULTY_LEVELS['intermediate'];
        $subjectInfo = ACT_SUBJECTS[$subject] ?? ['name' => ucfirst($subject)];
        
        $focusText = !empty($focusAreas) ? implode(', ', $focusAreas) : 'concepts, examples, and practice';
        
        $systemPrompt = "You are an expert ACT test preparation tutor. Create comprehensive, engaging lessons that help students improve their ACT scores. Use clear explanations, relevant examples, and include practice problems when appropriate.

Format your response using Markdown for structure. Use LaTeX for mathematical formulas:
- Inline math: \$formula\$ or \\(formula\\)
- Display math: \$\$formula\$\$ or \\[formula\\]

Include:
1. Introduction and learning objectives
2. Key concepts with clear explanations
3. Examples with step-by-step solutions
4. Practice problems (if applicable)
5. Summary and key takeaways
6. Tips for the ACT";

        $userPrompt = "Create a {$difficultyInfo['name']} level lesson on **{$topic}** for the ACT {$subjectInfo['name']} section.

Lesson Requirements:
- Target Length: approximately {$lengthInfo['minutes']} minutes of reading
- Difficulty Level: {$difficultyInfo['name']} ({$difficultyInfo['description']})
- Focus Areas: {$focusText}
- Score Range Target: {$difficultyInfo['score_range']}

Please make the lesson comprehensive and helpful for ACT preparation.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];
        
        return $this->chat($messages, $model ? getModelId($model) : null, ['max_tokens' => 8192]);
    }
    
    /**
     * Generate quiz questions
     */
    public function generateQuiz($subject, $topic, $questionCount, $difficulty, $questionTypes, $model = null) {
        $difficultyInfo = DIFFICULTY_LEVELS[$difficulty] ?? DIFFICULTY_LEVELS['intermediate'];
        $subjectInfo = ACT_SUBJECTS[$subject] ?? ['name' => ucfirst($subject)];
        $typesText = !empty($questionTypes) ? implode(', ', $questionTypes) : 'multiple choice';
        
        $systemPrompt = "You are an ACT test question creator. Generate high-quality, realistic ACT-style questions that test student knowledge and skills.

IMPORTANT: Your response MUST be valid JSON only. No markdown, no extra text, just pure JSON.

Use LaTeX for math formulas in questions and answers:
- Inline: \$formula\$ or \\(formula\\)
- Display: \$\$formula\$\$ or \\[formula\\]";

        $userPrompt = "Generate {$questionCount} {$difficultyInfo['name']}-level questions for ACT {$subjectInfo['name']} on the topic: **{$topic}**

Question types to include: {$typesText}

Return your response as a JSON object with this EXACT structure:
{
  \"questions\": [
    {
      \"id\": 1,
      \"type\": \"multiple_choice\",
      \"question\": \"Question text here\",
      \"options\": [\"A. Option 1\", \"B. Option 2\", \"C. Option 3\", \"D. Option 4\"],
      \"correct_answer\": 0,
      \"explanation\": \"Explanation of why this answer is correct\",
      \"topic\": \"specific topic\",
      \"difficulty\": \"{$difficulty}\"
    }
  ]
}

For true/false questions, use options: [\"True\", \"False\"]
For fill in the blank, use type: \"fill_blank\" and include \"correct_answer\" as the text answer.

Generate exactly {$questionCount} questions. Return ONLY valid JSON, no other text.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];
        
        return $this->chat($messages, $model ? getModelId($model) : null, ['max_tokens' => 8192]);
    }
    
    /**
     * Generate practice test section
     */
    public function generatePracticeTest($section, $model = null) {
        $sectionInfo = ACT_SUBJECTS[$section] ?? null;
        if (!$sectionInfo) {
            return ['error' => 'Invalid section'];
        }
        
        $questionCount = $sectionInfo['questions'];
        $topics = $sectionInfo['topics'];
        $topicsList = implode(', ', array_values($topics));
        
        $systemPrompt = "You are an ACT test creator. Generate a realistic, full-length ACT {$sectionInfo['name']} section with authentic difficulty distribution.

IMPORTANT: Your response MUST be valid JSON only. No markdown, no extra text.

For Reading and Science sections, include passage-based questions with the passage text.
Use LaTeX for math: \$formula\$ for inline, \$\$formula\$\$ for display.";

        $userPrompt = "Generate a complete ACT {$sectionInfo['name']} section with {$questionCount} questions.

Topics to cover: {$topicsList}

Include a mix of difficulty levels:
- 30% Easy (questions 1-" . round($questionCount * 0.3) . ")
- 40% Medium (questions " . round($questionCount * 0.3) . "-" . round($questionCount * 0.7) . ")
- 30% Hard (questions " . round($questionCount * 0.7) . "-{$questionCount})

Return as JSON:
{
  \"section\": \"{$section}\",
  \"time_limit\": {$sectionInfo['time_minutes']},
  \"questions\": [
    {
      \"id\": 1,
      \"type\": \"multiple_choice\",
      \"question\": \"Question text\",
      \"passage\": \"Optional passage text for reading/science\",
      \"options\": [\"A. ...\", \"B. ...\", \"C. ...\", \"D. ...\"],
      \"correct_answer\": 0,
      \"explanation\": \"Why this is correct\",
      \"topic\": \"topic name\",
      \"difficulty\": \"easy|medium|hard\"
    }
  ]
}

Return ONLY valid JSON.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];
        
        return $this->chat($messages, $model ? getModelId($model) : null, ['max_tokens' => 16384, 'temperature' => 0.8]);
    }
    
    /**
     * Chat with AI tutor
     */
    public function tutorChat($conversationHistory, $newMessage, $context = [], $model = null) {
        $contextInfo = '';
        if (!empty($context['subject'])) {
            $contextInfo .= "\nCurrent subject focus: " . $context['subject'];
        }
        if (!empty($context['recent_score'])) {
            $contextInfo .= "\nStudent's recent score: " . $context['recent_score'];
        }
        if (!empty($context['weak_areas'])) {
            $contextInfo .= "\nAreas needing improvement: " . implode(', ', $context['weak_areas']);
        }
        
        $systemPrompt = "You are an expert, friendly ACT tutor named ACT Prep Assistant. Help students with:
- Understanding ACT concepts and strategies
- Solving practice problems step by step
- Explaining correct and incorrect answers
- Providing study tips and test-taking strategies
- Motivating and encouraging students

Use clear explanations with examples. For math, use LaTeX: \$formula\$ for inline, \$\$formula\$\$ for display.
Be encouraging, patient, and helpful. Reference the student's progress when relevant.{$contextInfo}";

        $messages = [['role' => 'system', 'content' => $systemPrompt]];
        
        // Add conversation history
        foreach ($conversationHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content']
            ];
        }
        
        // Add new message
        $messages[] = ['role' => 'user', 'content' => $newMessage];
        
        return $this->chat($messages, $model ? getModelId($model) : null);
    }
    
    /**
     * Generate study plan
     */
    public function generateStudyPlan($params, $model = null) {
        $systemPrompt = "You are an ACT study plan specialist. Create detailed, personalized study plans that help students achieve their target scores.

IMPORTANT: Your response MUST be valid JSON only. No markdown, no extra text.";

        $currentScore = $params['current_score'] ?? 'unknown';
        $targetScore = $params['target_score'] ?? 30;
        $testDate = $params['test_date'] ?? 'in 3 months';
        $hoursPerWeek = $params['hours_per_week'] ?? 10;
        $weakAreas = !empty($params['weak_areas']) ? implode(', ', $params['weak_areas']) : 'all subjects';
        $strongAreas = !empty($params['strong_areas']) ? implode(', ', $params['strong_areas']) : 'none specified';
        
        $userPrompt = "Create a personalized ACT study plan:

Student Information:
- Current ACT Score: {$currentScore}
- Target Score: {$targetScore}
- Test Date: {$testDate}
- Available Study Time: {$hoursPerWeek} hours per week
- Weak Areas: {$weakAreas}
- Strong Areas: {$strongAreas}

Return as JSON:
{
  \"overview\": \"Brief overview of the plan\",
  \"total_weeks\": 12,
  \"weekly_hours\": {$hoursPerWeek},
  \"target_score\": {$targetScore},
  \"milestones\": [
    {\"week\": 4, \"goal\": \"Complete English review\", \"target_score\": 25}
  ],
  \"weeks\": [
    {
      \"week_number\": 1,
      \"focus\": \"Week focus area\",
      \"goals\": [\"Goal 1\", \"Goal 2\"],
      \"days\": [
        {
          \"day\": \"Monday\",
          \"tasks\": [
            {\"type\": \"lesson\", \"subject\": \"english\", \"topic\": \"Grammar basics\", \"duration\": 30},
            {\"type\": \"quiz\", \"subject\": \"english\", \"topic\": \"Punctuation\", \"questions\": 10}
          ]
        }
      ]
    }
  ],
  \"tips\": [\"Tip 1\", \"Tip 2\"]
}

Return ONLY valid JSON.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];
        
        return $this->chat($messages, $model ? getModelId($model) : null, ['max_tokens' => 12288]);
    }
    
    /**
     * Generate essay prompt
     */
    public function generateEssayPrompt($model = null) {
        $systemPrompt = "You are an ACT Writing test specialist. Generate authentic ACT-style essay prompts with three perspectives on contemporary issues.

IMPORTANT: Your response MUST be valid JSON only.";

        $userPrompt = "Generate an ACT Writing prompt with three perspectives.

Return as JSON:
{
  \"topic\": \"Brief topic title\",
  \"introduction\": \"Introduction paragraph describing the issue\",
  \"perspectives\": [
    {
      \"label\": \"Perspective One\",
      \"content\": \"First perspective on the issue\"
    },
    {
      \"label\": \"Perspective Two\",
      \"content\": \"Second perspective on the issue\"
    },
    {
      \"label\": \"Perspective Three\",
      \"content\": \"Third perspective on the issue\"
    }
  ],
  \"essay_task\": \"Write a unified, coherent essay in which you address the question of [topic]. In your essay, be sure to: clearly state your own perspective and analyze the relationship between your perspective and at least one other perspective; develop and support your ideas with reasoning and examples; organize your ideas clearly and logically; communicate your ideas effectively in standard written English.\"
}

Return ONLY valid JSON.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];
        
        return $this->chat($messages, $model ? getModelId($model) : null);
    }
    
    /**
     * Grade essay
     */
    public function gradeEssay($essayContent, $prompt, $model = null) {
        $systemPrompt = "You are an experienced ACT essay grader. Score essays using the official ACT Writing rubric (1-6 scale for each domain) and provide detailed, constructive feedback.

IMPORTANT: Your response MUST be valid JSON only.";

        $userPrompt = "Grade this ACT essay:

PROMPT:
{$prompt}

ESSAY:
{$essayContent}

Score using ACT Writing rubric (1-6 for each domain):
1. Ideas and Analysis
2. Development and Support
3. Organization
4. Language Use and Conventions

Return as JSON:
{
  \"scores\": {
    \"ideas_analysis\": 4,
    \"development_support\": 4,
    \"organization\": 4,
    \"language_use\": 4
  },
  \"total_score\": 16,
  \"overall_rating\": \"Good/Excellent/Needs Improvement\",
  \"feedback\": {
    \"strengths\": [\"Strength 1\", \"Strength 2\"],
    \"improvements\": [\"Area 1\", \"Area 2\"],
    \"ideas_analysis_feedback\": \"Specific feedback\",
    \"development_support_feedback\": \"Specific feedback\",
    \"organization_feedback\": \"Specific feedback\",
    \"language_use_feedback\": \"Specific feedback\"
  },
  \"suggestions\": [\"Suggestion 1\", \"Suggestion 2\"],
  \"sample_improvements\": \"Example of how a sentence could be improved\"
}

Return ONLY valid JSON.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];
        
        return $this->chat($messages, $model ? getModelId($model) : null);
    }
    
    /**
     * Generate flashcards
     */
    public function generateFlashcards($subject, $topic, $count, $model = null) {
        $subjectInfo = ACT_SUBJECTS[$subject] ?? ['name' => ucfirst($subject)];
        
        $systemPrompt = "You are an ACT study materials creator. Generate effective flashcards that help students memorize key concepts and formulas.

IMPORTANT: Your response MUST be valid JSON only.

Use LaTeX for math formulas: \$formula\$ for inline.";

        $userPrompt = "Generate {$count} flashcards for ACT {$subjectInfo['name']} on: {$topic}

Return as JSON:
{
  \"flashcards\": [
    {
      \"id\": 1,
      \"front\": \"Question or term\",
      \"back\": \"Answer or definition\",
      \"hint\": \"Optional hint\",
      \"category\": \"Category/subtopic\"
    }
  ]
}

Return ONLY valid JSON.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];
        
        return $this->chat($messages, $model ? getModelId($model) : null);
    }
    
    /**
     * Make API request with retry logic
     */
    private function makeRequest($payload, $attempt = 1) {
        $ch = curl_init($this->apiEndpoint);
        
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            if ($attempt < $this->maxRetries) {
                usleep($this->retryDelay * 1000 * $attempt);
                return $this->makeRequest($payload, $attempt + 1);
            }
            return ['error' => 'API request failed: ' . $error];
        }
        
        if ($httpCode >= 400) {
            if ($attempt < $this->maxRetries && $httpCode >= 500) {
                usleep($this->retryDelay * 1000 * $attempt);
                return $this->makeRequest($payload, $attempt + 1);
            }
            return ['error' => "API returned HTTP {$httpCode}", 'response' => $response];
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            return [
                'success' => true,
                'content' => $data['choices'][0]['message']['content'],
                'model' => $data['model'] ?? $payload['model'],
                'usage' => $data['usage'] ?? null
            ];
        }
        
        return ['error' => 'Unexpected API response format', 'response' => $data];
    }
    
    /**
     * Parse JSON from AI response
     */
    public function parseJsonResponse($content) {
        // Try to extract JSON from the response
        $content = trim($content);
        
        // Remove markdown code blocks if present
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/', $content, $matches)) {
            $content = $matches[1];
        }
        
        // Try to find JSON object or array
        if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
            $json = json_decode($matches[0], true);
            if ($json !== null) {
                return $json;
            }
        }
        
        // Try direct parse
        $json = json_decode($content, true);
        if ($json !== null) {
            return $json;
        }
        
        return null;
    }
    
    /**
     * Set model
     */
    public function setModel($modelKey) {
        $this->defaultModel = getModelId($modelKey);
    }
    
    /**
     * Get available models
     */
    public function getModels() {
        return AI_MODELS;
    }
}
