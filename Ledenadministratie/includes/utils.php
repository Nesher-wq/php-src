<?php
/**
 * utils.php - Utility Functions for Ledenadministratie System
 * 
 * This file contains helper functions used throughout the application
 * for common operations like logging, data validation, and formatting.
 * 
 * Functions:
 * - writeLog(): Centralized logging function for debugging and error tracking
 * 
 * Design Patterns:
 * - Uses function_exists() check to prevent redefinition errors
 * - Implements consistent log format with timestamps
 * - Stores logs outside web root when possible for security
 */

/**
 * writeLog - Centralized Logging Function
 * 
 * Writes timestamped messages to the application log file for debugging,
 * error tracking, and audit trail purposes. All log entries include
 * timestamp and are appended to maintain chronological order.
 * 
 * Usage Examples:
 * - writeLog("User login attempt: " . $username);
 * - writeLog("Database error in Familie.delete(): " . $error_message);
 * - writeLog("Family deleted successfully: ID " . $familie_id);
 * 
 * Log Format: [YYYY-MM-DD HH:MM:SS] Message content
 * 
 * @param string $message The message to log (automatically timestamped)
 * @return void Writes to app.log file, no return value
 */
if (!function_exists('writeLog')) {
    function writeLog($message) {
        // Log File Path: Store in application root directory
        $log_file_path = __DIR__ . '/../app.log';
        
        // Timestamp Format: Create consistent timestamp for all log entries
        $timestamp = date('[Y-m-d H:i:s] ');
        
        // Write Operation: Append timestamped message to log file
        // FILE_APPEND ensures we don't overwrite existing log entries
        file_put_contents($log_file_path, $timestamp . $message . PHP_EOL, FILE_APPEND);
    }
}
