<?php
/**
 * ACT Test Prep Application - Sidebar Component
 * 
 * Collapsible sidebar navigation
 */
?>
        <!-- Sidebar Navigation -->
        <aside id="sidebar" class="hidden lg:block w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 fixed left-0 top-16 bottom-0 overflow-y-auto transition-transform duration-300 z-30">
            <nav class="p-4 space-y-1">
                <!-- Study Section -->
                <div class="sidebar-section">
                    <h3 class="sidebar-section-title">Study</h3>
                    <a href="lessons.html" class="sidebar-link" data-page="lessons">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Lessons</span>
                    </a>
                    <a href="flashcards.html" class="sidebar-link" data-page="flashcards">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Flashcards</span>
                    </a>
                    <a href="study-plan.html" class="sidebar-link" data-page="study-plan">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Study Plan</span>
                    </a>
                </div>
                
                <!-- Practice Section -->
                <div class="sidebar-section">
                    <h3 class="sidebar-section-title">Practice</h3>
                    <a href="quiz.html" class="sidebar-link" data-page="quiz">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Quizzes</span>
                    </a>
                    <a href="practice-test.html" class="sidebar-link" data-page="practice-test">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Practice Tests</span>
                    </a>
                    <a href="essay.html" class="sidebar-link" data-page="essay">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Essay Writing</span>
                    </a>
                </div>
                
                <!-- Help Section -->
                <div class="sidebar-section">
                    <h3 class="sidebar-section-title">Help</h3>
                    <a href="chat.html" class="sidebar-link" data-page="chat">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span>AI Tutor</span>
                    </a>
                </div>
                
                <!-- Progress Section -->
                <div class="sidebar-section">
                    <h3 class="sidebar-section-title">Progress</h3>
                    <a href="progress.html" class="sidebar-link" data-page="progress">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Analytics</span>
                    </a>
                    <a href="profile.html" class="sidebar-link" data-page="profile">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Profile</span>
                    </a>
                </div>
                
                <!-- Subject Quick Links -->
                <div class="sidebar-section mt-6">
                    <h3 class="sidebar-section-title">Subjects</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="lessons.html?subject=english" class="subject-chip bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200">English</a>
                        <a href="lessons.html?subject=math" class="subject-chip bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200">Math</a>
                        <a href="lessons.html?subject=reading" class="subject-chip bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200">Reading</a>
                        <a href="lessons.html?subject=science" class="subject-chip bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200">Science</a>
                    </div>
                </div>
                
                <!-- Stats Widget -->
                <div class="sidebar-section mt-6 p-4 bg-gradient-to-br from-primary-500 to-accent-500 rounded-xl text-white">
                    <div class="text-center">
                        <div class="text-3xl font-bold" id="sidebar-streak">0</div>
                        <div class="text-sm opacity-90">Day Streak 🔥</div>
                    </div>
                    <div class="mt-4 flex justify-between text-sm">
                        <div class="text-center">
                            <div class="font-semibold" id="sidebar-xp">0</div>
                            <div class="text-xs opacity-80">XP</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold" id="sidebar-level">1</div>
                            <div class="text-xs opacity-80">Level</div>
                        </div>
                        <div class="text-center">
                            <div class="font-semibold" id="sidebar-quizzes">0</div>
                            <div class="text-xs opacity-80">Quizzes</div>
                        </div>
                    </div>
                </div>
            </nav>
        </aside>
        
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"></div>
        
        <!-- Main Content Area -->
        <main id="main-content" class="flex-1 lg:ml-64 p-6 transition-all duration-300">
