# Hostinger Git Deployment - Fix Guide

## The Problem
Your deployment log showed:
```
Looking for composer.lock file
composer.lock file was not found
Looking for composer.json file
composer.json file was not found
```

This happens because Hostinger's Git deployment script looks in the **root directory**, but your composer files are in subdirectories.

## The Solution

### Option 1: Root composer.json (RECOMMENDED)
I've added a root `composer.json` that orchestrates both admin and frontend deployments.

**What it does:**
- Runs `composer install` in `admin/application/`
- Runs `composer install` in `frontend/`
- Caches Laravel routes/views/config

### Option 2: Custom Deploy Script
Use the included `deploy.sh` script in your Hostinger webhook settings.

---

## Hostinger Configuration

### Step 1: Update Webhook Deploy Script

In Hostinger Dashboard → Git:
1. Go to your repository settings
2. Find "Deploy Script" or "Post-receive Hook"
3. Replace with:

```bash
#!/bin/bash
cd $HOME/public_html

# Pull latest code
git pull origin main

# Run deployment
bash deploy.sh
```

OR if using root composer.json:

```bash
#!/bin/bash
cd $HOME/public_html

# Pull latest code  
git pull origin main

# Run composer (will trigger admin & frontend installs)
composer install --no-dev --optimize-autoloader

# Laravel optimization
cd admin/application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Directory Structure

```
public_html/                    ← droutlier.com (root)
├── admin/                      ← admin.droutlier.com (subdomain)
│   └── application/            ← Laravel admin
│       └── composer.json       ← Laravel dependencies
├── frontend/                   ← PHP frontend (not deployed yet)
│   └── composer.json           ← Frontend dependencies
├── src/                        ← React/Next.js (current frontend)
├── composer.json               ← Root orchestrator (NEW)
└── deploy.sh                   ← Deployment script (NEW)
```

---

## When You're Ready to Switch from React to PHP:

### Current Setup (Now):
- **droutlier.com** → Serves `src/` (React/Next.js)
- **admin.droutlier.com** → Serves `admin/` (Laravel)

### Future Setup (After Migration):
- **droutlier.com** → Serves `frontend/public/` (PHP)
- **admin.droutlier.com** → Serves `admin/` (Laravel)

### How to Switch in Hostinger:
1. Go to **Domains** → droutlier.com
2. Change **Document Root** from `/public_html` to `/public_html/frontend/public`
3. Or create a symlink: `ln -s frontend/public/index.php public_html/index.php`

---

## Testing the Fix

After pushing these changes:

```bash
git add composer.json deploy.sh .gitignore
git commit -m "Fix Hostinger deployment: Add root composer orchestration"
git push origin main
```

Watch the Hostinger deployment log - it should now show:
```
✅ composer.json file found
✅ Installing dependencies...
✅ Deployment successful
```

---

## Both Frontends Running Simultaneously

You can test the PHP frontend before switching by:

1. **Subdomain approach:**
   - Create subdomain: `new.droutlier.com`
   - Point to: `/public_html/frontend/public`

2. **URL path approach:**
   - Access via: `droutlier.com/frontend/public`
   - (Requires .htaccess config)

3. **Local testing:**
   - Keep React on live server
   - Test PHP frontend locally
   - Switch when ready
