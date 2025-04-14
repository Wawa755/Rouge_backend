<?php
// If the request is for a file that exists in the public folder, serve it directly.
if (file_exists(__DIR__ . '/public' . $_SERVER['REQUEST_URI'])) {
    return false;
}

// Otherwise, hand off to Laravel’s front controller.
require_once __DIR__ . '/public/index.php';
