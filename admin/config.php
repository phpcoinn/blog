<?php
// Admin Configuration
// Preferred secret source order:
// 1) BLOG_ADMIN_PASSWORD_HASH environment variable
// 2) ADMIN_PASSWORD_HASH_LOCAL constant in admin/config.local.php (gitignored)
// 3) ADMIN_PASSWORD_HASH fallback below (legacy compatibility)
// Generate hash with: php -r "echo password_hash('yourpassword', PASSWORD_DEFAULT);"
define('ADMIN_PASSWORD_HASH', '$2y$10$tNtPEZPfi9DobS21tc0/XeNp0yZF5NwMLgiLTFsgkZkP2ESLAHxRK'); // legacy fallback hash

define('POSTS_DIR', __DIR__ . '/../posts/');
define('ADMIN_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/admin/');
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_SECONDS', 900); // 15 min

session_start();

$localConfigFile = __DIR__ . '/config.local.php';
if (file_exists($localConfigFile)) {
    require_once $localConfigFile;
}

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
    $adminHash = getAdminPasswordHash();
    if (password_verify($password, $adminHash)) {
        $_SESSION['admin_logged_in'] = true;
        clearLoginThrottle();
        return true;
    }
    return false;
}

function getAdminPasswordHash() {
    $envHash = getenv('BLOG_ADMIN_PASSWORD_HASH');
    if (!empty($envHash)) {
        return $envHash;
    }
    if (defined('ADMIN_PASSWORD_HASH_LOCAL') && !empty(ADMIN_PASSWORD_HASH_LOCAL)) {
        return ADMIN_PASSWORD_HASH_LOCAL;
    }
    return ADMIN_PASSWORD_HASH;
}

function isLoginLocked() {
    $attempts = $_SESSION['admin_login_attempts'] ?? 0;
    $lockUntil = $_SESSION['admin_login_lock_until'] ?? 0;

    if ($lockUntil > time()) {
        return true;
    }
    if ($lockUntil > 0 && $lockUntil <= time()) {
        clearLoginThrottle();
    }
    return false;
}

function getLoginLockRemainingSeconds() {
    $lockUntil = $_SESSION['admin_login_lock_until'] ?? 0;
    if ($lockUntil <= time()) {
        return 0;
    }
    return $lockUntil - time();
}

function registerFailedLogin() {
    $attempts = $_SESSION['admin_login_attempts'] ?? 0;
    $attempts++;
    $_SESSION['admin_login_attempts'] = $attempts;

    if ($attempts >= LOGIN_MAX_ATTEMPTS) {
        $_SESSION['admin_login_lock_until'] = time() + LOGIN_LOCKOUT_SECONDS;
    }
}

function clearLoginThrottle() {
    unset($_SESSION['admin_login_attempts'], $_SESSION['admin_login_lock_until']);
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
