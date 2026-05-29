<?php
require 'Parsedown.php';

// Helper function to parse frontmatter from markdown
function parseFrontmatter($content) {
    $frontmatter = [];
    $body = $content;
    
    // Check for YAML frontmatter (--- at start)
    if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $content, $matches)) {
        $yaml = $matches[1];
        $body = $matches[2];
        
        // Parse simple key: value pairs
        foreach (explode("\n", $yaml) as $line) {
            if (preg_match('/^(\w+):\s*(.+)$/', trim($line), $kv)) {
                $frontmatter[$kv[1]] = trim($kv[2]);
            }
        }
    }
    
    return [$frontmatter, $body];
}

$slug = isset($_GET['id']) ? basename($_GET['id']) : '';
$file = "posts/$slug.md";

if (!$slug || !file_exists($file)) {
    header("HTTP/1.0 404 Not Found");
    die("<h1>404 Post Not Found</h1><a href='index.php'>Go Back</a>");
}

$content = file_get_contents($file);

// Parse frontmatter
list($frontmatter, $body) = parseFrontmatter($content);

// Get image (default fallback)
$image = isset($frontmatter['image']) ? $frontmatter['image'] : 'img/blockchain-php.jpg';

// Extract Title from body for <title> tag
$lines = explode("\n", $body);
$title = 'Untitled';
foreach ($lines as $line) {
    $trimmed = trim($line);
    if (strpos($trimmed, '# ') === 0) {
        $title = str_replace('# ', '', $trimmed);
        break;
    }
}
$pageTitle = $title . " - PHPCoin Blog";

// Parse Body (skip frontmatter)
$Parsedown = new Parsedown();
$html = $Parsedown->text($body);

include 'header.php';
?>

<div class="single-post">
    <div class="container">
        <a href="index.php" style="font-size: 14px; color: #999;">&larr; Back to Blog</a>
        
        <img src="<?php echo htmlspecialchars($image); ?>" style="width: 100%; heigh: auto; border-radius: 20px; margin-top: 20px; "/>

        <div class="markdown-body">
            <?php echo $html; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
