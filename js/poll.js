/**
 * ACT Test Prep - Poll Job Manager
 */

const Poll = {
    activeJobs: new Map(),
    defaultInterval: 2000,
    maxAttempts: 150,

    /**
     * Create and poll a job
     */
    async execute(jobType, params, options = {}) {
        const {
            onProgress = () => {},
            onComplete = () => {},
            onError = () => {},
            interval = this.defaultInterval,
            maxAttempts = this.maxAttempts
        } = options;

        try {
            // Create the job
            const createResult = await API.createPollJob(jobType, params);
            
            if (!createResult.success) {
                throw new Error(createResult.message || 'Failed to create job');
            }

            const jobId = createResult.data.job_id;
            
            // Store job info
            this.activeJobs.set(jobId, {
                type: jobType,
                params,
                status: 'pending',
                startTime: Date.now()
            });

            // Poll for completion
            const result = await this.pollForCompletion(jobId, {
                interval,
                maxAttempts,
                onProgress
            });

            // Job completed
            this.activeJobs.delete(jobId);
            onComplete(result);
            return result;

        } catch (error) {
            onError(error);
            throw error;
        }
    },

    /**
     * Poll for job completion
     */
    async pollForCompletion(jobId, options = {}) {
        const {
            interval = this.defaultInterval,
            maxAttempts = this.maxAttempts,
            onProgress = () => {}
        } = options;

        for (let attempt = 0; attempt < maxAttempts; attempt++) {
            // Wait before checking
            await Utils.sleep(interval);

            // Check job status
            const statusResult = await API.checkPollJob(jobId);
            
            if (!statusResult.success) {
                throw new Error(statusResult.message || 'Failed to check job status');
            }

            const { status, progress, error } = statusResult.data;

            // Update progress
            const estimatedProgress = progress || Math.min(95, (attempt / maxAttempts) * 100);
            onProgress({
                status,
                progress: estimatedProgress,
                attempt,
                maxAttempts,
                elapsed: Date.now() - (this.activeJobs.get(jobId)?.startTime || Date.now())
            });

            // Check status
            if (status === 'completed') {
                const resultData = await API.getPollResult(jobId);
                if (resultData.success && resultData.data.result) {
                    return resultData.data.result;
                }
                throw new Error('Failed to get job result');
            }

            if (status === 'failed') {
                throw new Error(error || 'Job failed');
            }

            if (status === 'cancelled') {
                throw new Error('Job was cancelled');
            }
        }

        // Timeout
        throw new Error('Job timed out');
    },

    /**
     * Cancel a job
     */
    async cancel(jobId) {
        try {
            await API.cancelPollJob(jobId);
            this.activeJobs.delete(jobId);
            return true;
        } catch (error) {
            console.error('Failed to cancel job:', error);
            return false;
        }
    },

    /**
     * Get active jobs
     */
    getActiveJobs() {
        return Array.from(this.activeJobs.entries()).map(([id, info]) => ({
            id,
            ...info
        }));
    },

    /**
     * Check if job is active
     */
    isJobActive(jobId) {
        return this.activeJobs.has(jobId);
    },

    // ==================== Helper Methods for Common Jobs ====================

    /**
     * Generate lesson with polling
     */
    async generateLesson(params, callbacks = {}) {
        return this.execute('generate_lesson', params, {
            ...callbacks,
            onComplete: (result) => {
                if (callbacks.onComplete) callbacks.onComplete(result);
            }
        });
    },

    /**
     * Generate quiz with polling
     */
    async generateQuiz(params, callbacks = {}) {
        return this.execute('generate_quiz', params, {
            ...callbacks,
            onComplete: (result) => {
                if (callbacks.onComplete) callbacks.onComplete(result);
            }
        });
    },

    /**
     * Generate practice test with polling
     */
    async generatePracticeTest(params, callbacks = {}) {
        return this.execute('generate_practice_test', params, {
            ...callbacks,
            maxAttempts: 300, // Longer timeout for full tests
            onComplete: (result) => {
                if (callbacks.onComplete) callbacks.onComplete(result);
            }
        });
    },

    /**
     * Generate study plan with polling
     */
    async generateStudyPlan(params, callbacks = {}) {
        return this.execute('generate_study_plan', params, {
            ...callbacks,
            onComplete: (result) => {
                if (callbacks.onComplete) callbacks.onComplete(result);
            }
        });
    },

    /**
     * Generate essay prompt with polling
     */
    async generateEssayPrompt(params = {}, callbacks = {}) {
        return this.execute('generate_essay_prompt', params, {
            ...callbacks,
            onComplete: (result) => {
                if (callbacks.onComplete) callbacks.onComplete(result);
            }
        });
    },

    /**
     * Grade essay with polling
     */
    async gradeEssay(params, callbacks = {}) {
        return this.execute('grade_essay', params, {
            ...callbacks,
            onComplete: (result) => {
                if (callbacks.onComplete) callbacks.onComplete(result);
            }
        });
    },

    /**
     * Generate flashcards with polling
     */
    async generateFlashcards(params, callbacks = {}) {
        return this.execute('generate_flashcards', params, {
            ...callbacks,
            onComplete: (result) => {
                if (callbacks.onComplete) callbacks.onComplete(result);
            }
        });
    },

    /**
     * Chat message with polling
     */
    async chat(params, callbacks = {}) {
        return this.execute('chat_message', params, {
            ...callbacks,
            interval: 1000, // Faster polling for chat
            onComplete: (result) => {
                if (callbacks.onComplete) callbacks.onComplete(result);
            }
        });
    }
};

// Make available globally
window.Poll = Poll;
