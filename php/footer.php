<?php
/**
 * ACT Test Prep Application - Footer Component
 * 
 * Footer included on all pages
 */
?>
        </main>
    </div>
    
    <!-- Footer -->
    <footer class="lg:ml-64 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-8 mt-auto">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <span class="text-2xl mr-2">📚</span>
                        ACT Prep
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Your AI-powered companion for ACT test preparation. Practice, learn, and achieve your target score.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="dashboard.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Dashboard</a></li>
                        <li><a href="lessons.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Lessons</a></li>
                        <li><a href="quiz.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Practice Quiz</a></li>
                        <li><a href="practice-test.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Practice Test</a></li>
                    </ul>
                </div>
                
                <!-- Study Resources -->
                <div>
                    <h4 class="font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="chat.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">AI Tutor</a></li>
                        <li><a href="flashcards.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Flashcards</a></li>
                        <li><a href="study-plan.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Study Plan</a></li>
                        <li><a href="essay.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Essay Practice</a></li>
                    </ul>
                </div>
                
                <!-- Account -->
                <div>
                    <h4 class="font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="profile.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Profile</a></li>
                        <li><a href="progress.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Progress</a></li>
                        <li><a href="settings.html" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Settings</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    © <?php echo date('Y'); ?> ACT Test Prep. All rights reserved.
                </p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Built with ❤️ for students
                    </span>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="hidden fixed inset-0 bg-white dark:bg-gray-900 bg-opacity-90 dark:bg-opacity-90 z-50 flex items-center justify-center">
        <div class="text-center">
            <div class="loading-spinner mb-4"></div>
            <p class="text-gray-600 dark:text-gray-400" id="loading-text">Loading...</p>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full p-6 animate-modal-in">
            <h3 class="text-lg font-semibold mb-2" id="confirm-title">Confirm Action</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6" id="confirm-message">Are you sure you want to proceed?</p>
            <div class="flex justify-end space-x-3">
                <button id="confirm-cancel" class="btn btn-secondary">Cancel</button>
                <button id="confirm-ok" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
    
    <!-- Core JavaScript Files -->
    <script src="js/utils.js"></script>
    <script src="js/storage.js"></script>
    <script src="js/api.js"></script>
    <script src="js/auth.js"></script>
    <script src="js/notifications.js"></script>
    <script src="js/poll.js"></script>
    <script src="js/math.js"></script>
    <script src="js/markdown.js"></script>
    <script src="js/validation.js"></script>
    <script src="js/charts.js"></script>
    <script src="js/ui.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
