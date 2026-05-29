<?php
// Admin Configuration - CHANGE THIS PASSWORD!
// Default: admin / admin123
// Generate new hash with: echo password_hash('yourpassword', PASSWORD_DEFAULT);
define('ADMIN_PASSWORD_HASH', '$2y$10$tNtPEZPfi9DobS21tc0/XeNp0yZF5NwMLgiLTFsgkZkP2ESLAHxRK'); // h23527jr234D2742

define('POSTS_DIR', __DIR__ . '/../posts/');
define('ADMIN_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/admin/');

session_start();

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

function login($password) {
    if (password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}

function getPosts() {
    $files = glob(POSTS_DIR . '*.md');
    rsort($files);
    $posts = [];
    foreach ($files as $file) {
        $slug = basename($file, '.md');
        $content = file_get_contents($file);
        $lines = explode("\n", $content);
        
        // Find the title line (skip frontmatter if present)
        $title = 'Untitled';
        $inFrontmatter = false;
        $frontmatterEnded = false;
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            // Handle frontmatter
            if ($trimmed === '---') {
                if (!$inFrontmatter && !$frontmatterEnded) {
                    $inFrontmatter = true;
                    continue;
                } elseif ($inFrontmatter) {
                    $inFrontmatter = false;
                    $frontmatterEnded = true;
                    continue;
                }
            }
            
            // Skip frontmatter lines
            if ($inFrontmatter) {
                continue;
            }
            
            // Look for title (first # heading)
            if (strpos($trimmed, '# ') === 0) {
                $title = str_replace('# ', '', $trimmed);
                break;
            }
        }
        
        $date = 'Unknown';
        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $slug, $matches)) {
            $date = date("F j, Y", strtotime($matches[1]));
        }
        
        $posts[] = [
            'slug' => $slug,
            'title' => $title,
            'date' => $date,
            'file' => $file
        ];
    }
    return $posts;
}

function getPost($slug) {
    $file = POSTS_DIR . $slug . '.md';
    if (!file_exists($file)) return null;
    return file_get_contents($file);
}

function savePost($slug, $content) {
    $file = POSTS_DIR . $slug . '.md';
    return file_put_contents($file, $content) !== false;
}

function deletePost($slug) {
    $file = POSTS_DIR . $slug . '.md';
    if (file_exists($file)) {
        return unlink($file);
    }
    return false;
}

function generateSlug($title, $date) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $date . '-' . $slug;
}
?>
