# PHPCoin Blog Admin

Simple admin panel for managing blog posts.

## Access

Visit: `http://your-server:8080/admin/`

## Default Login

- **Password:** `admin123`

## Change Password

1. Open `admin/config.php`
2. Replace the password hash with a new one:
   ```bash
   php -r "echo password_hash('your-new-password', PASSWORD_DEFAULT);"
   ```
3. Copy the output and update the `ADMIN_PASSWORD_HASH` constant

## Features

- ✅ Create new blog posts
- ✅ Edit existing posts  
- ✅ Delete posts
- ✅ Markdown support
- ✅ Date-based post organization
- ✅ **Export all posts** (ZIP or tar.gz download)
- ✅ **Import posts** (upload multiple .md files)
- ✅ Deploy helper with rsync command

## Post Format

Posts are saved as Markdown files in `/posts/` directory with naming:
```
YYYY-MM-DD-post-title-slug.md
```

Example:
```
2026-02-01-why-php-blockchain-matters.md
```

### Frontmatter (Optional)

Add metadata at the top of your posts:

```markdown
---
image: img/my-custom-image.jpg
---

# Your Post Title

Content here...
```

**Available fields:**
- `image` - Path to featured image (default: `img/blockchain-php.jpg`)

## Export / Import

### Export Posts
1. Go to Dashboard → click **📥 Export**
2. Downloads all posts as a ZIP (or tar.gz) file
3. Use this to backup or transfer to another server

### Import Posts
1. Go to Dashboard → click **📤 Import**
2. Select one or more `.md` files
3. Choose whether to overwrite existing posts
4. Files are saved to `/posts/` directory

### Deploy to Production
Use the built-in rsync command on the Import page, or run manually:
```bash
rsync -avz -e "ssh -i ~/.ssh/id_ed25519_blog" posts/ root@phpcoin.net:/var/www/blog.phpcoin.net/web/posts/
```

## Markdown Support

The blog uses [Parsedown](https://parsedown.org/) for Markdown rendering.

Basic syntax:
- `# Heading` - Main title
- `## Subheading` - Section headers
- `**bold**` - Bold text
- `*italic*` - Italic text
- `[link](url)` - Links
- `- item` - Bullet lists
- `` `code` `` - Inline code
- ````code block```` - Code blocks
