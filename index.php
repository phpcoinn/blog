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

$pageTitle = "Blog - PHPCoin";
include 'header.php';
?>

<div class="container">
    <div class="intro" style="text-align: center; margin-bottom: 50px;">
        <h1>News from the PHPCoin ecosystem.</h1>
    </div>

    <div class="grid">
        <?php
        $files = glob("posts/*.md");
        if ($files) {
            rsort($files); // Newest first
            
            foreach ($files as $file) {
                $slug = basename($file, '.md');
                $content = file_get_contents($file);
                
                // Parse frontmatter
                list($frontmatter, $body) = parseFrontmatter($content);
                
                // Get image (default fallback)
                $image = isset($frontmatter['image']) ? $frontmatter['image'] : 'img/blockchain-php.jpg';
                
                // Extract title from body
                $lines = explode("\n", $body);
                $title = 'Untitled';
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if (strpos($trimmed, '# ') === 0) {
                        $title = str_replace('# ', '', $trimmed);
                        break;
                    }
                }
                
                // Get date from filename
                $date = "Latest";
                if (preg_match('/(\d{4}-\d{2}-\d{2})/', $slug, $matches)) {
                    $date = date("F j, Y", strtotime($matches[1]));
                }

                // Preview: first 3 non-empty lines after title
                $previewText = "";
                $foundTitle = false;
                $lineCount = 0;
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if (!$foundTitle && strpos($trimmed, '# ') === 0) {
                        $foundTitle = true;
                        continue;
                    }
                    if ($foundTitle && !empty($trimmed) && $lineCount < 3) {
                        $previewText .= $line . " ";
                        $lineCount++;
                    }
                }
                $previewText = trim(strip_tags((new Parsedown())->text($previewText)));
                $previewText = substr($previewText, 0, 150) . "...";

                echo '
                <article class="article">
                    <div class="article__img" style="background-image: url(\'' . htmlspecialchars($image) . '\'); background-size: cover; background-position: center; color: transparent;">PHP</div>
                    <div class="article__content">
                        <span class="article__meta">' . $date . '</span>
                        <h3 class="article__title"><a href="post.php?id=' . $slug . '">' . htmlspecialchars($title) . '</a></h3>
                        <p class="article__preview">' . $previewText . '</p>
                        <a href="post.php?id=' . $slug . '" class="btn">Read More</a>
                    </div>
                </article>';
            }
        } else {
            echo "<p>No posts found.</p>";
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>
