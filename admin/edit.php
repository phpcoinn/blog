<?php
require 'config.php';
requireAuth();

// Helper function to parse frontmatter
function parseFrontmatter($content) {
    $frontmatter = [];
    $body = $content;
    
    if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $content, $matches)) {
        $yaml = $matches[1];
        $body = $matches[2];
        
        foreach (explode("\n", $yaml) as $line) {
            if (preg_match('/^(\w+):\s*(.+)$/', trim($line), $kv)) {
                $frontmatter[$kv[1]] = trim($kv[2]);
            }
        }
    }
    
    return [$frontmatter, $body];
}

// Helper function to build frontmatter
function buildFrontmatter($data) {
    if (empty($data)) return '';
    
    $yaml = "---\n";
    foreach ($data as $key => $value) {
        if (!empty($value)) {
            $yaml .= "$key: $value\n";
        }
    }
    $yaml .= "---\n\n";
    return $yaml;
}

$slug = $_GET['slug'] ?? '';
$isEditing = !empty($slug);
$post = null;
$content = '';
$title = '';
$date = date('Y-m-d');
$image = ''; // Featured image
$error = '';
$success = '';

if ($isEditing) {
    $fullContent = getPost($slug);
    if ($fullContent === null) {
        header('Location: dashboard.php');
        exit;
    }
    
    // Parse frontmatter
    list($frontmatter, $body) = parseFrontmatter($fullContent);
    $image = $frontmatter['image'] ?? '';
    
    // Extract title and content from body
    $lines = explode("\n", $body);
    $title = '';
    $contentLines = [];
    $foundTitle = false;
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (!$foundTitle && strpos($trimmed, '# ') === 0) {
            $title = str_replace('# ', '', $trimmed);
            $foundTitle = true;
            continue;
        }
        if ($foundTitle) {
            $contentLines[] = $line;
        }
    }
    $content = implode("\n", $contentLines);
    $content = trim($content);
    
    // Extract date from slug
    if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $slug, $matches)) {
        $date = $matches[1];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');
    $slug = $_POST['slug'] ?? '';
    $image = trim($_POST['image'] ?? '');
    
    if (empty($title)) {
        $error = 'Title is required';
    } elseif (empty($content)) {
        $error = 'Content is required';
    } else {
        // Generate slug if creating new post
        if (empty($slug)) {
            $slug = generateSlug($title, $date);
        }
        
        // Build frontmatter
        $frontmatterData = [];
        if (!empty($image)) {
            $frontmatterData['image'] = $image;
        }
        
        // Build markdown content with frontmatter
        $markdown = buildFrontmatter($frontmatterData);
        $markdown .= "# " . $title . "\n\n" . $content;
        
        if (savePost($slug, $markdown)) {
            header('Location: dashboard.php?msg=saved');
            exit;
        } else {
            $error = 'Failed to save post';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEditing ? 'Edit' : 'New'; ?> Post - PHPCoin Blog</title>
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
        .header h1 {
            font-size: 20px;
        }
        .header-nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            opacity: 0.9;
        }
        .header-nav a:hover {
            opacity: 1;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .page-header {
            margin-bottom: 25px;
        }
        .page-header h2 {
            font-size: 24px;
            color: #333;
        }
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
        }
        .form-group {
            margin-bottom: 24px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        .required {
            color: #dc3545;
        }
        input[type="text"], input[type="date"], textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, input[type="date"]:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            min-height: 400px;
            resize: vertical;
            line-height: 1.6;
            font-family: 'Monaco', 'Menlo', monospace;
        }
        .help-text {
            font-size: 13px;
            color: #888;
            margin-top: 6px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
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
        .btn-secondary:hover {
            background: #5a6268;
        }
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
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .two-col {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .two-col {
                grid-template-columns: 1fr;
            }
        }
        .slug-preview {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 14px;
            color: #667eea;
            word-break: break-all;
        }
        .markdown-help {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .markdown-help h4 {
            margin-bottom: 12px;
            color: #555;
        }
        .markdown-help code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 13px;
        }
        .markdown-help ul {
            list-style: none;
            font-size: 14px;
        }
        .markdown-help li {
            margin-bottom: 8px;
            color: #666;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
            .header h1 {
                font-size: 16px;
            }
            .header-nav a {
                margin: 0 8px;
                font-size: 13px;
            }
            .container {
                padding: 20px 15px;
            }
            .page-header h2 {
                font-size: 20px;
            }
            .form-card {
                padding: 20px;
            }
            .two-col {
                grid-template-columns: 1fr;
            }
            textarea {
                min-height: 250px;
                font-size: 14px;
            }
            .form-actions {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
            .markdown-help {
                padding: 15px;
            }
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
            <h2><?php echo $isEditing ? '✏️ Edit Post' : '📝 New Post'; ?></h2>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" action="">
                <input type="hidden" name="slug" value="<?php echo htmlspecialchars($slug); ?>">
                
                <div class="two-col">
                    <div class="form-group">
                        <label for="title">Post Title <span class="required">*</span></label>
                        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="Enter post title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Date <span class="required">*</span></label>
                        <input type="date" id="date" name="date" value="<?php echo $date; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Featured Image</label>
                    <input type="text" id="image" name="image" value="<?php echo htmlspecialchars($image); ?>" placeholder="e.g., img/my-image.jpg">
                    <p class="help-text">Path to featured image (optional). Default: img/blockchain-php.jpg</p>
                </div>

                <div class="form-group">
                    <label for="content">Content (Markdown) <span class="required">*</span></label>
                    <textarea id="content" name="content" placeholder="Write your post content here..." required><?php echo htmlspecialchars($content); ?></textarea>
                    <p class="help-text">Supports Markdown formatting. First line will be the title.</p>
                </div>

                <div class="markdown-help">
                    <h4>📝 Markdown Quick Reference</h4>
                    <ul>
                        <li><code># Heading</code> - Large heading</li>
                        <li><code>## Subheading</code> - Smaller heading</li>
                        <li><code>**bold**</code> - Bold text</li>
                        <li><code>*italic*</code> - Italic text</li>
                        <li><code>[link](url)</code> - Link</li>
                        <li><code>- item</code> - Bullet list</li>
                        <li><code>```code```</code> - Code block</li>
                    </ul>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?php echo $isEditing ? '💾 Save Changes' : '📝 Create Post'; ?></button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
