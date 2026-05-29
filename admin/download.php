<?php
require 'config.php';
requireAuth();

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: dashboard.php');
    exit;
}

$file = POSTS_DIR . $slug . '.md';

if (!file_exists($file)) {
    header('Location: dashboard.php');
    exit;
}

// Send file as download
header('Content-Type: text/markdown');
header('Content-Disposition: attachment; filename="' . $slug . '.md"');
header('Content-Length: ' . filesize($file));
header('Cache-Control: no-cache, must-revalidate');

readfile($file);
exit;
?>
