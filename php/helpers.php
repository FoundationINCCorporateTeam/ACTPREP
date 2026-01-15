<?php
/**
 * ACT Test Prep Application - Helper Functions
 * 
 * Utility functions for content processing, validation, and formatting
 */

require_once __DIR__ . '/config.php';

/**
 * Sanitize HTML content to prevent XSS
 */
function sanitizeHtml($content) {
    return htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Sanitize content while preserving safe HTML
 */
function sanitizeContent($content) {
    // Allow specific safe tags
    $allowedTags = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><code><pre><blockquote><table><thead><tbody><tr><th><td><hr><span><div>';
    
    // Strip disallowed tags
    $content = strip_tags($content, $allowedTags);
    
    // Remove dangerous attributes
    $content = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/', '', $content);
    $content = preg_replace('/\s+style\s*=\s*["\'][^"\']*["\']/', '', $content);
    
    return $content;
}

/**
 * Validate email format
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate required fields
 */
function validateRequired($data, $fields) {
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    return $errors;
}

/**
 * Format date for display
 */
function formatDate($timestamp, $format = 'M j, Y') {
    return date($format, $timestamp);
}

/**
 * Format date/time for display
 */
function formatDateTime($timestamp, $format = 'M j, Y g:i A') {
    return date($format, $timestamp);
}

/**
 * Format time duration (seconds to human readable)
 */
function formatDuration($seconds) {
    if ($seconds < 60) {
        return $seconds . ' sec';
    } elseif ($seconds < 3600) {
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        return $minutes . ' min' . ($secs > 0 ? ' ' . $secs . ' sec' : '');
    } else {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return $hours . ' hr' . ($minutes > 0 ? ' ' . $minutes . ' min' : '');
    }
}

/**
 * Format study time (minutes to human readable)
 */
function formatStudyTime($minutes) {
    if ($minutes < 60) {
        return $minutes . ' min';
    } else {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours . ' hr' . ($mins > 0 ? ' ' . $mins . ' min' : '');
    }
}

/**
 * Calculate percentage
 */
function calculatePercentage($value, $total) {
    return $total > 0 ? round(($value / $total) * 100, 1) : 0;
}

/**
 * Convert raw score to ACT scale (1-36)
 */
function convertToActScale($correct, $total) {
    if ($total <= 0) return 1;
    
    $percentage = ($correct / $total) * 100;
    
    // Approximate ACT scale conversion
    if ($percentage >= 100) return 36;
    if ($percentage >= 97) return 35;
    if ($percentage >= 94) return 34;
    if ($percentage >= 91) return 33;
    if ($percentage >= 88) return 32;
    if ($percentage >= 85) return 31;
    if ($percentage >= 82) return 30;
    if ($percentage >= 79) return 29;
    if ($percentage >= 76) return 28;
    if ($percentage >= 73) return 27;
    if ($percentage >= 70) return 26;
    if ($percentage >= 67) return 25;
    if ($percentage >= 64) return 24;
    if ($percentage >= 61) return 23;
    if ($percentage >= 58) return 22;
    if ($percentage >= 55) return 21;
    if ($percentage >= 52) return 20;
    if ($percentage >= 49) return 19;
    if ($percentage >= 46) return 18;
    if ($percentage >= 43) return 17;
    if ($percentage >= 40) return 16;
    if ($percentage >= 37) return 15;
    if ($percentage >= 34) return 14;
    if ($percentage >= 31) return 13;
    if ($percentage >= 28) return 12;
    if ($percentage >= 25) return 11;
    if ($percentage >= 22) return 10;
    if ($percentage >= 19) return 9;
    if ($percentage >= 16) return 8;
    if ($percentage >= 13) return 7;
    if ($percentage >= 10) return 6;
    if ($percentage >= 7) return 5;
    if ($percentage >= 4) return 4;
    if ($percentage >= 2) return 3;
    if ($percentage >= 1) return 2;
    return 1;
}

/**
 * Calculate composite ACT score
 */
function calculateCompositeScore($scores) {
    if (empty($scores)) return 0;
    return round(array_sum($scores) / count($scores));
}

/**
 * Get percentile for ACT score
 */
function getPercentile($score) {
    $percentiles = [
        36 => 99, 35 => 99, 34 => 99, 33 => 98, 32 => 97,
        31 => 95, 30 => 93, 29 => 90, 28 => 87, 27 => 84,
        26 => 80, 25 => 76, 24 => 72, 23 => 67, 22 => 62,
        21 => 56, 20 => 50, 19 => 44, 18 => 38, 17 => 32,
        16 => 26, 15 => 21, 14 => 16, 13 => 12, 12 => 9,
        11 => 6, 10 => 4, 9 => 2, 8 => 1, 7 => 1,
        6 => 1, 5 => 1, 4 => 1, 3 => 1, 2 => 1, 1 => 1
    ];
    
    return $percentiles[$score] ?? 1;
}

/**
 * Get score color class
 */
function getScoreColorClass($score, $maxScore = 36) {
    $percentage = ($score / $maxScore) * 100;
    
    if ($percentage >= 85) return 'text-green-600';
    if ($percentage >= 70) return 'text-blue-600';
    if ($percentage >= 55) return 'text-yellow-600';
    return 'text-red-600';
}

/**
 * Get score badge class
 */
function getScoreBadgeClass($score, $maxScore = 36) {
    $percentage = ($score / $maxScore) * 100;
    
    if ($percentage >= 85) return 'bg-green-100 text-green-800';
    if ($percentage >= 70) return 'bg-blue-100 text-blue-800';
    if ($percentage >= 55) return 'bg-yellow-100 text-yellow-800';
    return 'bg-red-100 text-red-800';
}

/**
 * Generate table of contents from markdown headings
 */
function generateTableOfContents($content) {
    $toc = [];
    
    preg_match_all('/^(#{1,6})\s+(.+)$/m', $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $level = strlen($match[1]);
        $title = $match[2];
        $slug = slugify($title);
        
        $toc[] = [
            'level' => $level,
            'title' => $title,
            'slug' => $slug
        ];
    }
    
    return $toc;
}

/**
 * Create URL-friendly slug
 */
function slugify($text) {
    // Convert to lowercase
    $text = strtolower($text);
    // Remove special characters
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    // Replace spaces with hyphens
    $text = preg_replace('/[\s-]+/', '-', $text);
    // Trim hyphens
    $text = trim($text, '-');
    return $text;
}

/**
 * Truncate text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Extract first paragraph from content
 */
function extractExcerpt($content, $length = 200) {
    // Remove markdown headers
    $content = preg_replace('/^#{1,6}\s+.+$/m', '', $content);
    // Remove markdown formatting
    $content = preg_replace('/[*_~`#]/', '', $content);
    // Get first paragraph
    $paragraphs = preg_split('/\n\s*\n/', trim($content));
    $excerpt = $paragraphs[0] ?? '';
    
    return truncateText($excerpt, $length);
}

/**
 * Count words in text
 */
function countWords($text) {
    return str_word_count(strip_tags($text));
}

/**
 * Estimate reading time
 */
function estimateReadingTime($text, $wordsPerMinute = 200) {
    $wordCount = countWords($text);
    return max(1, ceil($wordCount / $wordsPerMinute));
}

/**
 * Generate random ID
 */
function generateId($prefix = '') {
    return $prefix . uniqid('', true);
}

/**
 * Generate share link
 */
function generateShareLink($type, $id) {
    $token = bin2hex(random_bytes(8));
    return APP_URL . "share.php?type={$type}&id={$id}&token={$token}";
}

/**
 * Parse markdown content (basic)
 */
function parseMarkdownBasic($content) {
    // Headers
    $content = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $content);
    $content = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $content);
    $content = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $content);
    
    // Bold and italic
    $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);
    $content = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $content);
    
    // Code
    $content = preg_replace('/`(.+?)`/', '<code>$1</code>', $content);
    
    // Lists
    $content = preg_replace('/^\* (.+)$/m', '<li>$1</li>', $content);
    $content = preg_replace('/^- (.+)$/m', '<li>$1</li>', $content);
    
    // Paragraphs
    $content = preg_replace('/\n\s*\n/', '</p><p>', $content);
    $content = '<p>' . $content . '</p>';
    
    return $content;
}

/**
 * Get subject color
 */
function getSubjectColor($subject) {
    $colors = [
        'english' => 'blue',
        'math' => 'green',
        'reading' => 'purple',
        'science' => 'orange',
        'writing' => 'pink'
    ];
    
    return $colors[$subject] ?? 'gray';
}

/**
 * Get difficulty badge
 */
function getDifficultyBadge($difficulty) {
    $badges = [
        'beginner' => '<span class="badge badge-green">Beginner</span>',
        'intermediate' => '<span class="badge badge-blue">Intermediate</span>',
        'advanced' => '<span class="badge badge-orange">Advanced</span>',
        'expert' => '<span class="badge badge-red">Expert</span>'
    ];
    
    return $badges[$difficulty] ?? '<span class="badge badge-gray">Unknown</span>';
}

/**
 * Format JSON for export
 */
function formatJsonExport($data) {
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Generate CSV from array
 */
function arrayToCsv($data, $headers = null) {
    $output = fopen('php://temp', 'r+');
    
    if ($headers) {
        fputcsv($output, $headers);
    } elseif (!empty($data)) {
        fputcsv($output, array_keys($data[0]));
    }
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    rewind($output);
    $csv = stream_get_contents($output);
    fclose($output);
    
    return $csv;
}

/**
 * Log activity
 */
function logActivity($userId, $action, $details = []) {
    $logFile = DATA_DIR . 'activity.log';
    $entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $userId,
        'action' => $action,
        'details' => $details,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    file_put_contents($logFile, json_encode($entry) . "\n", FILE_APPEND);
}

/**
 * Log error
 */
function logError($message, $context = []) {
    $logFile = DATA_DIR . 'error.log';
    $entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'context' => $context
    ];
    
    file_put_contents($logFile, json_encode($entry) . "\n", FILE_APPEND);
}

/**
 * Check rate limit
 */
function checkRateLimit($identifier, $limit = RATE_LIMIT_REQUESTS, $window = RATE_LIMIT_WINDOW) {
    $rateLimitFile = DATA_DIR . 'rate_limits.json';
    
    $limits = [];
    if (file_exists($rateLimitFile)) {
        $limits = json_decode(file_get_contents($rateLimitFile), true) ?? [];
    }
    
    $now = time();
    
    // Clean old entries
    $limits = array_filter($limits, function($entry) use ($now, $window) {
        return $entry['timestamp'] > ($now - $window);
    });
    
    // Count requests from this identifier
    $count = 0;
    foreach ($limits as $entry) {
        if ($entry['identifier'] === $identifier) {
            $count++;
        }
    }
    
    if ($count >= $limit) {
        return false;
    }
    
    // Add new entry
    $limits[] = [
        'identifier' => $identifier,
        'timestamp' => $now
    ];
    
    file_put_contents($rateLimitFile, json_encode($limits));
    
    return true;
}

/**
 * Get client IP
 */
function getClientIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $allowedTypes, $maxSize = 5242880) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload failed'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['error' => 'File too large (max ' . round($maxSize / 1048576) . 'MB)'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['error' => 'Invalid file type'];
    }
    
    return ['success' => true, 'mime' => $mimeType];
}

/**
 * Save uploaded file
 */
function saveUploadedFile($file, $directory, $customName = null) {
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $customName ? $customName . '.' . $extension : uniqid() . '.' . $extension;
    $path = $directory . '/' . $filename;
    
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $path)) {
        return ['success' => true, 'path' => $path, 'filename' => $filename];
    }
    
    return ['error' => 'Failed to save file'];
}

/**
 * Generate chart data format
 */
function formatChartData($data, $labels, $label = 'Data') {
    return [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => $label,
                'data' => $data
            ]
        ]
    ];
}

/**
 * Get time ago string
 */
function timeAgo($timestamp) {
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
    
    return formatDate($timestamp);
}

/**
 * Get greeting based on time of day
 */
function getGreeting() {
    $hour = date('G');
    
    if ($hour < 12) return 'Good morning';
    if ($hour < 17) return 'Good afternoon';
    return 'Good evening';
}
