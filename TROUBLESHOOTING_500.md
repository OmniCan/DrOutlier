# HTTP 500 Error Troubleshooting Guide

## The Error
```
HTTP ERROR 500
www.droutlier.com is currently unable to handle this request.
```

This means PHP is crashing. Here's how to fix it:

---

## Step 1: Check Error Logs

### Via Hostinger Dashboard:
1. Go to **Dashboard → Advanced → Error Logs**
2. Look for the latest PHP error
3. Share the error message to pinpoint the issue

### Via SSH:
```bash
tail -f ~/public_html/error_log
# or
tail -f /var/log/apache2/error.log
```

---

## Step 2: Run Diagnostics

### Access the diagnostic page:
👉 **https://www.droutlier.com/check.php**

This will show:
- ✓ PHP version
- ✓ Vendor directory status
- ✓ .env file status  
- ✓ Storage permissions
- ✓ Required PHP extensions

---

## Step 3: Common Fixes

### Fix #1: Missing Composer Dependencies

**Problem:** Vendor directory doesn't exist

**Solution via SSH:**
```bash
cd ~/public_html
composer install --no-dev --optimize-autoloader
```

**Or via Hostinger File Manager:**
- Trigger git deployment again (it should run composer install)

---

### Fix #2: Missing .env File

**Problem:** .env file not found

**Solution via SSH:**
```bash
cd ~/public_html
cp .env.frontend.example .env
nano .env
```

**Update .env with:**
```env
APP_NAME="DrOutlier"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.droutlier.com

# Laravel Admin API
API_BASE_URL=https://admin.droutlier.com/api
API_TIMEOUT=30

SESSION_LIFETIME=120
SESSION_COOKIE_NAME=droutlier_session
```

**Or via File Manager:**
1. Copy `.env.frontend.example` to `.env`
2. Edit and update `API_BASE_URL`

---

### Fix #3: Storage Permissions

**Problem:** storage/cache not writable

**Solution via SSH:**
```bash
chmod -R 755 ~/public_html/storage
chmod -R 755 ~/public_html/storage/cache
```

---

### Fix #4: Path Issues in index.php

Check if paths are correct:

**File:** `public_html/index.php`

Should have:
```php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv::createImmutable(__DIR__);
require_once __DIR__ . '/routes/web.php';
```

NOT:
```php
require_once __DIR__ . '/../vendor/autoload.php';  // ✗ WRONG
```

---

### Fix #5: .htaccess Conflicts

If you have multiple .htaccess files, they might conflict.

**Check:**
```bash
cd ~/public_html
ls -la .htaccess
```

**Our .htaccess should have:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Exclude admin from PHP routing
    RewriteCond %{REQUEST_URI} ^/admin [NC]
    RewriteRule ^ - [L]
    
    # Allow static files
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]
    
    # Route to PHP frontend
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

---

## Step 4: Enable Debug Mode (Temporarily)

Edit `.env`:
```env
APP_DEBUG=true  # Change to true
```

Then visit droutlier.com - you'll see the actual error message.

**⚠️ IMPORTANT:** Set back to `false` after fixing!

---

## Step 5: Deploy Checklist

After pushing code, verify on server:

```bash
# 1. Navigate to directory
cd ~/public_html

# 2. Pull latest code
git pull origin main

# 3. Install dependencies
composer install --no-dev --optimize-autoloader

# 4. Verify .env exists
ls -la .env

# 5. Set permissions
chmod -R 755 storage
chmod 644 .env

# 6. Clear cache (if needed)
rm -rf storage/cache/*
```

---

## Most Likely Issues:

### 1. **Vendor directory missing** (90% chance)
   - Run: `composer install`

### 2. **.env file missing** (80% chance)
   - Run: `cp .env.frontend.example .env`

### 3. **Wrong paths in code** (50% chance)
   - Check `index.php` paths

### 4. **Storage not writable** (30% chance)
   - Run: `chmod -R 755 storage`

---

## Quick Fix Script

Create this file: `~/public_html/fix.sh`

```bash
#!/bin/bash
echo "Fixing DrOutlier PHP Frontend..."

cd ~/public_html

# Install dependencies
composer install --no-dev --optimize-autoloader

# Create .env if missing
if [ ! -f .env ]; then
    cp .env.frontend.example .env
    echo "Created .env file - EDIT IT NOW!"
fi

# Fix permissions
chmod -R 755 storage
chmod -R 755 app
chmod 644 .env

echo "Done! Now edit .env and try droutlier.com"
```

Run: `bash fix.sh`

---

## Contact Support

If none of this works, share:
1. The output of `check.php`
2. The error log contents
3. Output of: `ls -la ~/public_html`

---

## After It Works

1. Delete `check.php`
2. Set `APP_DEBUG=false` in `.env`
3. Clear browser cache
