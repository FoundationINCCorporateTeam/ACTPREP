# ACT Test Prep Application

A complete, production-ready, multi-page ACT test preparation web application with advanced AI tutoring capabilities.

## Features

- **AI-Powered Lesson Generation**: Generate comprehensive lessons on any ACT topic
- **Practice Quizzes**: Take customizable quizzes with instant feedback
- **Full Practice Tests**: Complete ACT-style practice tests with realistic timing
- **AI Tutor Chat**: Get help from an AI tutor on any topic
- **Study Plans**: Generate personalized study plans based on your goals
- **Essay Practice**: Practice ACT writing with AI grading
- **Flashcards**: Study with AI-generated flashcards
- **Progress Tracking**: Track your progress with detailed analytics
- **Gamification**: Earn XP, level up, and unlock achievements

## Technology Stack

- **Frontend**: HTML5, CSS3, Tailwind CSS (CDN), Vanilla JavaScript (ES6+)
- **Backend**: PHP 8.0+
- **Database**: JSON file-based storage
- **AI Integration**: nano-gpt.com API
- **Math Rendering**: MathJax 3.x
- **Markdown**: Marked.js

## Installation

### Prerequisites

- PHP 8.0 or higher
- Web server (Apache, Nginx, or PHP built-in server)

### Quick Start

1. Clone the repository:
```bash
git clone https://github.com/your-repo/act-test-prep.git
cd act-test-prep
```

2. Configure the API key:
   - Edit `php/config.php` and update `NANO_GPT_API_KEY` with your API key

3. Ensure the `data/` directory is writable:
```bash
chmod 755 data/
```

4. Start a PHP development server:
```bash
php -S localhost:8000
```

5. Open your browser and navigate to `http://localhost:8000`

### Production Deployment

For production deployment:

1. Configure your web server (Apache/Nginx) to serve the application
2. Set up HTTPS for secure connections
3. Ensure proper file permissions for the `data/` directory
4. Configure PHP settings for optimal performance

## File Structure

```
/act-test-prep/
├── index.html              # Login/Register page
├── dashboard.html          # Main dashboard
├── lessons.html            # Lesson generation
├── lesson-view.html        # Lesson viewing
├── quiz.html               # Quiz interface
├── quiz-results.html       # Quiz results
├── practice-test.html      # Practice test
├── test-results.html       # Test results
├── chat.html               # AI tutor chat
├── study-plan.html         # Study plan generator
├── essay.html              # Essay practice
├── progress.html           # Progress tracking
├── flashcards.html         # Flashcard study
├── settings.html           # User settings
├── profile.html            # User profile
├── /css/
│   ├── styles.css          # Global styles
│   ├── components.css      # UI components
│   ├── animations.css      # Animations
│   ├── pages.css           # Page-specific
│   ├── responsive.css      # Media queries
│   ├── print.css           # Print styles
│   └── dark-mode.css       # Dark theme
├── /js/
│   ├── app.js              # Main initialization
│   ├── api.js              # API communication
│   ├── auth.js             # Authentication
│   ├── ui.js               # UI interactions
│   ├── poll.js             # Poll job manager
│   ├── math.js             # MathJax integration
│   ├── markdown.js         # Markdown rendering
│   ├── charts.js           # Data visualization
│   ├── notifications.js    # Toast notifications
│   ├── validation.js       # Form validation
│   ├── storage.js          # LocalStorage
│   └── utils.js            # Utilities
├── /php/
│   ├── api.php             # API router
│   ├── config.php          # Configuration
│   ├── database.php        # Database manager
│   ├── ai-client.php       # AI API client
│   ├── poll-jobs.php       # Background jobs
│   ├── auth.php            # Authentication
│   ├── helpers.php         # Utility functions
│   ├── header.php          # Header component
│   ├── footer.php          # Footer component
│   └── sidebar.php         # Sidebar component
├── /data/
│   ├── users.json          # User accounts
│   ├── lessons.json        # Generated lessons
│   ├── quizzes.json        # Quiz data
│   ├── tests.json          # Practice tests
│   ├── chat_history.json   # Chat conversations
│   ├── study_plans.json    # Study plans
│   ├── essays.json         # Essay submissions
│   ├── flashcards.json     # Flashcard sets
│   ├── progress.json       # User progress
│   ├── settings.json       # User settings
│   ├── poll_jobs.json      # Background jobs
│   └── analytics.json      # Usage analytics
└── README.md
```

## API Configuration

The application uses the nano-gpt.com API for AI features. Configure in `php/config.php`:

```php
define('NANO_GPT_API_ENDPOINT', 'https://nano-gpt.com/api/v1/chat/completions');
define('NANO_GPT_API_KEY', 'your-api-key-here');
```

## Available AI Models

- GLM 4.7 Thinking
- DeepSeek V3.2 Thinking
- Llama 3.1 Large
- MiniMax M2.1
- Mistral Large 3
- Kimi K2 Thinking

## ACT Subjects

The application covers all ACT test sections:

- **English**: Grammar, punctuation, sentence structure, style
- **Math**: Pre-algebra through trigonometry
- **Reading**: Comprehension, analysis, inference
- **Science**: Data interpretation, scientific reasoning
- **Writing**: Essay structure, argumentation

## License

This project is provided for educational purposes.

## Support

For issues or feature requests, please open an issue on GitHub.