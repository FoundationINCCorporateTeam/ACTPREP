/**
 * ACT Test Prep - API Communication Layer
 */

const API = {
    baseUrl: 'php/api.php',

    /**
     * Make API request
     */
    async request(action, data = {}, method = 'POST') {
        try {
            const url = method === 'GET' 
                ? `${this.baseUrl}?action=${action}&${new URLSearchParams(data)}`
                : this.baseUrl;

            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json'
                }
            };

            if (method === 'POST') {
                options.body = JSON.stringify({ action, ...data });
            }

            const response = await fetch(url, options);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            return result;
        } catch (error) {
            console.error('API request error:', error);
            return { success: false, message: error.message, errors: [error.message] };
        }
    },

    /**
     * GET request helper
     */
    async get(action, params = {}) {
        return this.request(action, params, 'GET');
    },

    /**
     * POST request helper
     */
    async post(action, data = {}) {
        return this.request(action, data, 'POST');
    },

    // ==================== Authentication ====================

    async login(email, password) {
        return this.post('login', { email, password });
    },

    async register(email, password, name) {
        return this.post('register', { email, password, name });
    },

    async logout() {
        return this.post('logout');
    },

    async checkAuth() {
        return this.get('check_auth');
    },

    async getCurrentUser() {
        return this.get('get_current_user');
    },

    // ==================== Config ====================

    async getModels() {
        return this.get('get_models');
    },

    async getSubjects() {
        return this.get('get_subjects');
    },

    async getTopics(subject) {
        return this.post('get_topics', { subject });
    },

    // ==================== Lessons ====================

    async generateLesson(params) {
        return this.post('generate_lesson', params);
    },

    async saveLesson(lesson) {
        return this.post('save_lesson', lesson);
    },

    async getLesson(id) {
        return this.get('get_lesson', { id });
    },

    async getLessons(filters = {}) {
        return this.post('get_lessons', filters);
    },

    async deleteLesson(id) {
        return this.post('delete_lesson', { id });
    },

    async markLessonCompleted(id) {
        return this.post('mark_lesson_completed', { id });
    },

    // ==================== Quizzes ====================

    async generateQuiz(params) {
        return this.post('generate_quiz', params);
    },

    async saveQuiz(quiz) {
        return this.post('save_quiz', quiz);
    },

    async getQuiz(id) {
        return this.get('get_quiz', { id });
    },

    async getQuizzes() {
        return this.get('get_quizzes');
    },

    async gradeQuiz(quizId, answers, timeTaken) {
        return this.post('grade_quiz', { quiz_id: quizId, answers, time_taken: timeTaken });
    },

    async getQuizResults(id) {
        return this.get('get_quiz_results', { id });
    },

    // ==================== Practice Tests ====================

    async generatePracticeTest(params) {
        return this.post('generate_practice_test', params);
    },

    async saveTest(test) {
        return this.post('save_test', test);
    },

    async getTest(id) {
        return this.get('get_test', { id });
    },

    async getTests() {
        return this.get('get_tests');
    },

    async gradeTest(testId, sectionAnswers) {
        return this.post('grade_test', { test_id: testId, section_answers: sectionAnswers });
    },

    // ==================== Chat ====================

    async sendChatMessage(conversationId, message, context = {}, model = null) {
        return this.post('chat_message', { 
            conversation_id: conversationId, 
            message, 
            context,
            model
        });
    },

    async getChatHistory(conversationId) {
        return this.get('get_chat_history', { conversation_id: conversationId });
    },

    async getConversations() {
        return this.get('get_conversations');
    },

    async createConversation(title) {
        return this.post('create_conversation', { title });
    },

    async clearChat(conversationId) {
        return this.post('clear_chat', { conversation_id: conversationId });
    },

    // ==================== Study Plans ====================

    async generateStudyPlan(params) {
        return this.post('generate_study_plan', params);
    },

    async getStudyPlan(id) {
        return this.get('get_study_plan', { id });
    },

    async getStudyPlans() {
        return this.get('get_study_plans');
    },

    async updateStudyPlan(id, updates) {
        return this.post('update_study_plan', { id, updates });
    },

    async completeTask(planId, weekIndex, dayIndex, taskIndex) {
        return this.post('complete_task', { 
            plan_id: planId, 
            week_index: weekIndex, 
            day_index: dayIndex, 
            task_index: taskIndex 
        });
    },

    // ==================== Essays ====================

    async generateEssayPrompt(model = null) {
        return this.post('generate_essay_prompt', { model });
    },

    async saveEssay(essay) {
        return this.post('save_essay', essay);
    },

    async gradeEssay(content, prompt, model = null) {
        return this.post('grade_essay', { content, prompt, model });
    },

    async getEssays() {
        return this.get('get_essays');
    },

    // ==================== Flashcards ====================

    async generateFlashcards(params) {
        return this.post('generate_flashcards', params);
    },

    async saveFlashcardSet(set) {
        return this.post('save_flashcard_set', set);
    },

    async getFlashcardSets() {
        return this.get('get_flashcard_sets');
    },

    async getFlashcardSet(id) {
        return this.get('get_flashcard_set', { id });
    },

    async updateCardMastery(setId, cardIndex, mastery) {
        return this.post('update_card_mastery', { set_id: setId, card_index: cardIndex, mastery });
    },

    // ==================== Progress ====================

    async getProgress() {
        return this.get('get_progress');
    },

    async updateProgress(activityType, data) {
        return this.post('update_progress', { activity_type: activityType, ...data });
    },

    async getStatistics() {
        return this.get('get_statistics');
    },

    // ==================== Poll Jobs ====================

    async createPollJob(jobType, params) {
        return this.post('create_poll_job', { job_type: jobType, params });
    },

    async checkPollJob(jobId) {
        return this.get('check_poll_job', { job_id: jobId });
    },

    async getPollResult(jobId) {
        return this.get('get_poll_result', { job_id: jobId });
    },

    async cancelPollJob(jobId) {
        return this.post('cancel_poll_job', { job_id: jobId });
    },

    // ==================== Settings ====================

    async getSettings() {
        return this.get('get_settings');
    },

    async saveSettings(settings) {
        return this.post('save_settings', settings);
    },

    // ==================== Profile ====================

    async getProfile() {
        return this.get('get_profile');
    },

    async updateProfile(data) {
        return this.post('update_profile', data);
    },

    async changePassword(currentPassword, newPassword) {
        return this.post('change_password', { current_password: currentPassword, new_password: newPassword });
    },

    // ==================== Export/Import ====================

    async exportData() {
        // This returns a file download
        window.location.href = `${this.baseUrl}?action=export_data`;
    },

    async importData(data) {
        return this.post('import_data', data);
    }
};

// Make available globally
window.API = API;
