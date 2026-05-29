<?php
require 'config.php';
requireAuth();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['posts'])) {
    $uploaded = 0;
    $failed = 0;
    
    foreach ($_FILES['posts']['tmp_name'] as $key => $tmpName) {
        $name = $_FILES['posts']['name'][$key];
        
        // Validate file extension
        if (!str_ends_with(strtolower($name), '.md')) {
            $failed++;
            continue;
        }
        
        // Sanitize filename
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '-', $name);
        $target = POSTS_DIR . $safeName;
        
        // Don't overwrite existing without confirmation
        if (file_exists($target) && !isset($_POST['overwrite'])) {
            $failed++;
            continue;
        }
        
        if (move_uploaded_file($tmpName, $target)) {
            $uploaded++;
        } else {
            $failed++;
        }
    }
    
    if ($uploaded > 0) {
        $message = "✅ Uploaded $uploaded post(s) successfully!";
    }
    if ($failed > 0) {
        $error = "❌ Failed to upload $failed file(s). Check file types (.md only) or enable overwrite.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Posts - PHPCoin Blog</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { font-size: 20px; }
        .header-nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            opacity: 0.9;
        }
        .header-nav a:hover { opacity: 1; }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .page-header { margin-bottom: 25px; }
        .page-header h2 { font-size: 24px; color: #333; }
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
        }
        .form-group { margin-bottom: 24px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            background: #fafafa;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover { background: #5a6268; }
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .help-text {
            font-size: 13px;
            color: #888;
            margin-top: 6px;
        }
        .deploy-box {
            background: #f0f4ff;
            border: 1px solid #c7d2fe;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        .deploy-box h3 {
            color: #4c51bf;
            margin-bottom: 12px;
        }
        code {
            background: #1a1a2e;
            color: #a5b3ce;
            padding: 15px;
            border-radius: 8px;
            display: block;
            font-family: 'Monaco', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
            .container { padding: 20px 15px; }
            .form-card { padding: 20px; }
            .form-actions { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>🦀 PHPCoin Blog Admin</h1>
            <nav class="header-nav">
                <a href="dashboard.php">← Dashboard</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h2>📤 Import Posts</h2>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="posts">Select Markdown File(s) (.md)</label>
                    <input type="file" id="posts" name="posts[]" accept=".md" multiple required>
                    <p class="help-text">Select one or more .md files. You can upload a single post or multiple at once. Filenames should follow format: YYYY-MM-DD-post-title.md</p>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="overwrite" name="overwrite" value="1">
                        <label for="overwrite" style="margin: 0;">Overwrite existing posts with same filename</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">📤 Upload Posts</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <div class="deploy-box">
            <h3>🚀 Deploy to Production</h3>
            <p>To transfer posts to your remote server, use this rsync command:</p>
            <code>rsync -avz -e "ssh -i ~/.ssh/id_ed25519_blog" posts/ root@phpcoin.net:/var/www/blog.phpcoin.net/web/posts/</code>
            <p style="font-size: 13px; color: #666; margin-top: 10px;">
                💡 <a href="export.php" style="color: #4c51bf;">Download all posts as ZIP</a> first, extract on remote, or run the rsync command above.
            </p>
        </div>
    </div>
</body>
</html>
