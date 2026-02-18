# New Admin Modules - Implementation Guide

## Overview
Creating 3 new modules by cloning New Spotters admin structure:
1. **New Exam Cases**
2. **New Table Viva**
3. **Theory Notes**

---

## Database Tables

### 1. New Exam Cases Tables

```sql
-- Categories Table
CREATE TABLE `new_exam_cases_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `is_premium` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Items Table
CREATE TABLE `new_exam_cases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `is_premium` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookmarks Table
CREATE TABLE `new_exam_cases_bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bookmark` (`user_id`, `item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. New Table Viva Tables

```sql
-- Categories Table
CREATE TABLE `new_table_viva_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `is_premium` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Items Table
CREATE TABLE `new_table_viva` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `is_premium` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookmarks Table
CREATE TABLE `new_table_viva_bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bookmark` (`user_id`, `item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Theory Notes Tables

```sql
-- Categories Table
CREATE TABLE `theory_notes_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `is_premium` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Items Table
CREATE TABLE `theory_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `is_premium` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookmarks Table
CREATE TABLE `theory_notes_bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bookmark` (`user_id`, `item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Files to Create

### For Each Module, Create:

#### 1. Admin Controllers (3 files per module × 3 modules = 9 files)
- `app/Http/Controllers/Admin/{ModuleName}Controller.php`
- `app/Http/Controllers/Admin/{ModuleName}CategoryController.php`
- `app/Http/Controllers/Api/{ModuleName}ApiController.php`

#### 2. Models (3 files per module × 3 modules = 9 files)
- `app/Models/{ModuleName}Category.php`
- `app/Models/{ModuleName}.php`
- `app/Models/{ModuleName}Bookmark.php`

#### 3. Views (3 files per module × 3 modules = 9 files)
- `resources/views/admin/{module-name}-category/index.blade.php`
- `resources/views/admin/{module-name}-category/create.blade.php`
- `resources/views/admin/{module-name}-category/edit.blade.php`
- `resources/views/admin/{module-name}/list.blade.php` (items list)
- `resources/views/admin/{module-name}/create.blade.php` (item create)
- `resources/views/admin/{module-name}/edit.blade.php` (item edit)

#### 4. Routes
- Add to `routes/admin.php`
- Add to `routes/api.php`

---

## Module Name Mapping

| Module Display Name | Route Prefix | Controller Name | Table Prefix | PDF Folder |
|-------------------|--------------|----------------|-------------|-----------|
| New Exam Cases | `new-exam-cases` | `NewExamCases` | `new_exam_cases` | `new_exam_cases_pdf` |
| New Table Viva | `new-table-viva` | `NewTableViva` | `new_table_viva` | `new_table_viva_pdf` |
| Theory Notes | `theory-notes` | `TheoryNotes` | `theory_notes` | `theory_notes_pdf` |

---

## Admin Panel URLs

### New Exam Cases
- Categories: `https://admin.droutlier.com/admin/new-exam-cases-category`
- Items: `https://admin.droutlier.com/admin/new-exam-cases/list`

### New Table Viva
- Categories: `https://admin.droutlier.com/admin/new-table-viva-category`
- Items: `https://admin.droutlier.com/admin/new-table-viva/list`

### Theory Notes
- Categories: `https://admin.droutlier.com/admin/theory-notes-category`
- Items: `https://admin.droutlier.com/admin/theory-notes/list`

---

## Implementation Steps

1. **Run SQL migrations** to create all database tables
2. **Create Models** for each module (9 model files)
3. **Create Controllers** for admin and API (9 controller files)
4. **Copy and modify Views** from New Spotters (18 view files)
5. **Add Routes** to admin.php and api.php
6. **Create PDF storage folders** in admin/assets/
7. **Test each module** in admin panel

---

## Route Configuration Examples

### In `routes/admin.php`:

```php
// New Exam Cases Routes
Route::controller('NewExamCasesController')->name('new-exam-cases.')->prefix('new-exam-cases')->group(function () {
    Route::get('/list', 'index')->name('new-exam-cases-index');
    Route::get('/create', 'create')->name('new-exam-cases-create');
    Route::post('/store', 'store')->name('new-exam-cases-store');
    Route::get('/edit/{id}', 'edit')->name('new-exam-cases-edit');
    Route::post('/update/{id}', 'update')->name('new-exam-cases-update');
    Route::post('/delete/{id}', 'delete')->name('new-exam-cases-delete');
});

Route::controller('NewExamCasesCategoryController')->name('new-exam-cases-category.')->prefix('new-exam-cases-category')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/delete/{id}', 'delete')->name('delete');
    Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
});

// Repeat similar pattern for New Table Viva and Theory Notes
```

### In `routes/api.php`:

```php
// New Exam Cases API Routes
Route::controller('NewExamCasesApiController')->name('new-exam-cases.')->prefix('new-exam-cases')->group(function(){
    Route::post('/categories', 'categories');
    Route::post('/chapters', 'chapters');
    Route::post('/items-by-chapter', 'itemsByChapter');
    Route::post('/get-item-by-id', 'getItemById');
    Route::post('/change-bookmark', 'changeBookmark');
    Route::post('/get-bookmarks', 'getBookmarks');
});

// Repeat for New Table Viva and Theory Notes
```

---

## Summary

**Total Files to Create: ~45 files**
- 9 Admin Controllers
- 9 API Controllers  
- 9 Models
- 18 View files
- Route additions in 2 files
- 3 PDF storage folders

**Database Tables: 9 tables total**
- 3 category tables
- 3 items tables
- 3 bookmarks tables

All modules follow the exact same structure as New Spotters, just with different names and table references.
