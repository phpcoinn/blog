<?php
require 'config.php';
requireAuth();

$posts = getPosts();
$message = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PHPCoin Blog</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 24px;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .page-header h2 {
            font-size: 28px;
            color: #333;
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
            font-size: 14px;
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
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
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
        .posts-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 16px 20px;
            text-align: left;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tr {
            border-bottom: 1px solid #eee;
        }
        tr:last-child {
            border-bottom: none;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .post-title {
            font-weight: 500;
            color: #333;
        }
        .post-date {
            color: #888;
            font-size: 14px;
        }
        .post-slug {
            color: #667eea;
            font-size: 13px;
            font-family: monospace;
        }
        .actions {
            display: flex;
            gap: 8px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .stat-card h3 {
            font-size: 14px;
            color: #888;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .header h1 {
                font-size: 18px;
            }
            .header-nav a {
                margin: 0 10px;
                font-size: 14px;
            }
            .container {
                padding: 20px 15px;
            }
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }
            .page-header h2 {
                font-size: 22px;
            }
            .stats {
                grid-template-columns: 1fr;
            }
            .stat-card {
                padding: 18px;
            }
            .stat-card .number {
                font-size: 28px;
            }
            /* Table as cards on mobile */
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead {
                display: none;
            }
            tr {
                margin-bottom: 15px;
                background: white;
                border-radius: 12px;
                padding: 15px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            }
            td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid #f0f0f0;
            }
            td:last-child {
                border-bottom: none;
            }
            td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #666;
                font-size: 12px;
                text-transform: uppercase;
            }
            .post-title {
                font-size: 16px;
            }
            .post-slug {
                font-size: 11px;
                word-break: break-all;
                max-width: 60%;
                text-align: right;
            }
            .actions {
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 6px;
            }
            .btn-sm {
                padding: 8px 14px;
                font-size: 13px;
            }
            .btn {
                padding: 12px 20px;
                font-size: 15px;
                justify-content: center;
            }
            .page-header div {
                width: 100%;
            }
            .page-header .btn {
                flex: 1;
                min-width: 100px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>🦀 PHPCoin Blog Admin</h1>
            <nav class="header-nav">
                <a href="../index.php" target="_blank">View Blog →</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($message === 'saved'): ?>
            <div class="alert alert-success">✅ Post saved successfully!</div>
        <?php elseif ($message === 'deleted'): ?>
            <div class="alert alert-success">🗑️ Post deleted successfully!</div>
        <?php elseif ($message === 'imported'): ?>
            <div class="alert alert-success">📤 Posts imported successfully!</div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <h3>Total Posts</h3>
                <div class="number"><?php echo count($posts); ?></div>
            </div>
            <div class="stat-card">
                <h3>Latest Post</h3>
                <div class="number" style="font-size: 18px; color: #333;">
                    <?php echo !empty($posts) ? $posts[0]['date'] : 'None'; ?>
                </div>
            </div>
        </div>

        <div class="page-header">
            <h2>All Posts</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="export.php" class="btn btn-secondary">📥 Export</a>
                <a href="import.php" class="btn btn-secondary">📤 Import</a>
                <a href="edit.php" class="btn btn-primary">➕ New Post</a>
            </div>
        </div>

        <div class="posts-table">
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📝</div>
                    <h3>No posts yet</h3>
                    <p>Create your first blog post to get started.</p>
                    <br>
                    <a href="edit.php" class="btn btn-primary">Create Post</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td data-label="Title">
                                    <div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
                                </td>
                                <td data-label="Date" class="post-date"><?php echo $post['date']; ?></td>
                                <td data-label="Slug" class="post-slug"><?php echo $post['slug']; ?></td>
                                <td data-label="Actions">
                                    <div class="actions">
                                        <a href="../post.php?id=<?php echo urlencode($post['slug']); ?>" target="_blank" class="btn btn-secondary btn-sm">View</a>
                                        <a href="download.php?slug=<?php echo urlencode($post['slug']); ?>" class="btn btn-secondary btn-sm" title="Download">💾</a>
                                        <a href="edit.php?slug=<?php echo urlencode($post['slug']); ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <a href="delete.php?slug=<?php echo urlencode($post['slug']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
