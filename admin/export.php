<?php
require 'config.php';
requireAuth();

$timestamp = date('Y-m-d-His');

// Check if ZipArchive is available
if (class_exists('ZipArchive')) {
    // Create ZIP of all posts
    $zip = new ZipArchive();
    $zipName = 'phpcoin-blog-backup-' . $timestamp . '.zip';
    $zipPath = sys_get_temp_dir() . '/' . $zipName;

    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        $files = glob(POSTS_DIR . '*.md');
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();
        
        // Send ZIP to browser
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipName . '"');
        header('Content-Length: ' . filesize($zipPath));
        header('Cache-Control: no-cache, must-revalidate');
        
        readfile($zipPath);
        unlink($zipPath);
        exit;
    }
}

// Fallback: Create tar.gz archive
$tarName = 'phpcoin-blog-backup-' . $timestamp . '.tar.gz';
$tarPath = sys_get_temp_dir() . '/' . $tarName;

// Use PharData for tar.gz (built into PHP)
try {
    $phar = new PharData(str_replace('.tar.gz', '.tar', $tarPath));
    $files = glob(POSTS_DIR . '*.md');
    foreach ($files as $file) {
        $phar->addFile($file, basename($file));
    }
    $phar->compress(Phar::GZ);
    
    // Remove uncompressed tar
    unlink(str_replace('.tar.gz', '.tar', $tarPath));
    
    // Send tar.gz to browser
    header('Content-Type: application/gzip');
    header('Content-Disposition: attachment; filename="' . $tarName . '"');
    header('Content-Length: ' . filesize($tarPath));
    header('Cache-Control: no-cache, must-revalidate');
    
    readfile($tarPath);
    unlink($tarPath);
    exit;
} catch (Exception $e) {
    // Last resort: Redirect with error
    header('Location: dashboard.php?msg=export_failed');
    exit;
}
?>
