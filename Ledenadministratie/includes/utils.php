<?php
if (!function_exists('writeLog')) {
    function writeLog($message) {
        file_put_contents(__DIR__ . '/../app.log', date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
    }
}
