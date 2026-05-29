<?php
require 'config.php';
requireAuth();

$slug = $_GET['slug'] ?? '';

if (!empty($slug)) {
    deletePost($slug);
}

header('Location: dashboard.php?msg=deleted');
exit;
?>
