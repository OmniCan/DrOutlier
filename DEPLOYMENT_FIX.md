# Git Deployment Fix Guide

## Issues Found in Deployment Log

### ❌ Problem 1: Composer files not found
**Cause:** Deployment script looking in root directory instead of `admin/application/`  
**Status:** ✅ FIXED with `.cpanel.yml` configuration

### ❌ Problem 2: 200+ uncommitted changes
**Cause:** Modified files not committed to git  
**Status:** ⚠️ ACTION REQUIRED (see below)

### ❌ Problem 3: Untracked important files
**Cause:** Database migrations and other files not in git  
**Status:** ⚠️ ACTION REQUIRED (see below)

---

## Quick Fix Steps

### Step 1: Update .gitignore (DONE ✅)
Updated to exclude:
- Laravel logs
- Storage cache
- Firebase credentials
- Bootstrap cache
- Error logs

### Step 2: Add Important Files to Git

```bash
# Navigate to project
cd h:\droutlier-main

# Add database migrations (IMPORTANT!)
git add admin/application/database/migrations/

# Add the new .cpanel.yml deployment config
git add .cpanel.yml

# Add frontend folder
git add frontend/

# Check what will be committed
git status
```

### Step 3: Commit ALL Changes

```bash
# Add all modified files
git add admin/application/.

# Commit with message
git commit -m "Fix deployment: Add migrations, cpanel config, and frontend"
```

### Step 4: Update .cpanel.yml with YOUR cPanel Username

Edit `.cpanel.yml` and replace `USERNAME` with your actual cPanel username:

```yaml
- export DEPLOYPATH=/home/YOUR_CPANEL_USERNAME/public_html/
```

### Step 5: Push to GitHub

```bash
git push origin main
```

### Step 6: Trigger Deployment

In cPanel:
1. Go to **Git Version Control**
2. Click **Manage** on your repository
3. Click **Update from Remote**
4. Click **Deploy HEAD Commit**

---

## What .cpanel.yml Does (Fixed)

```yaml
# Copies files to server
- /bin/cp -R admin $DEPLOYPATH
- /bin/cp -R frontend $DEPLOYPATH

# Installs composer dependencies in CORRECT location
- cd $DEPLOYPATH/admin/application
- /usr/bin/composer install

# Runs Laravel optimization
- php artisan config:cache
- php artisan route:cache
```

---

## Expected Deployment Output (After Fix)

```
✅ Looking for composer.lock file
✅ composer.lock file found at admin/application/composer.lock
✅ Looking for composer.json file  
✅ composer.json file found at admin/application/composer.json
✅ Installing dependencies...
✅ Deployment successful
```

---

## File Organization

### ✅ Include in Git
- `admin/application/` (Laravel code)
- `admin/application/database/migrations/` (Database structure)
- `frontend/` (New PHP frontend)
- `.cpanel.yml` (Deployment config)
- `*.md` documentation files

### ❌ Exclude from Git (in .gitignore)
- `admin/application/storage/logs/` (Log files)
- `admin/application/bootstrap/cache/` (Cache)
- `admin/application/vendor/` (Composer packages)
- `frontend/vendor/` (Composer packages)
- `frontend/storage/cache/` (Twig cache)
- `.env` files (Secrets)
- `error_log` (Error logs)
- Uploaded images/PDFs

---

## Next Steps After Deployment

1. **Set up .env on server:**
   ```bash
   # In cPanel File Manager or SSH
   cd /home/USERNAME/public_html/admin/application
   cp .env.example .env
   php artisan key:generate
   ```

2. **Set correct permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chmod -R 775 admin/assets/
   ```

3. **Run migrations:**
   ```bash
   php artisan migrate
   ```

4. **Frontend setup:**
   ```bash
   cd /home/USERNAME/public_html/frontend
   cp .env.example .env
   # Edit .env with correct API_BASE_URL
   ```

---

## Troubleshooting

### If composer still not found:
Check cPanel PHP version and composer path:
```bash
which composer
# Usually: /usr/bin/composer or /usr/local/bin/composer
```

Update `.cpanel.yml` with correct path.

### If permissions denied:
```bash
chmod -R 755 admin/application/storage
chmod -R 755 admin/application/bootstrap/cache
```

### If migrations fail:
Check database credentials in `admin/application/.env`
