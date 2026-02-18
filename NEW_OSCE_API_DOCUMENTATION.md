# New OSCE Module - Backend API Documentation

## Overview
This document details the backend API endpoints required for the New OSCE module at `/new-osce`.

## Database Tables

### Table: `new_osce_categories`
```sql
CREATE TABLE `new_osce_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: inactive, 1: active',
  `is_premium` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: free, 1: premium',
  `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Structure:**
- **parent_id = 0**: Top-level categories (displayed on main page)
- **parent_id > 0**: Chapters within a category

### Table: `new_osce`
```sql
CREATE TABLE `new_osce` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category` bigint(20) NOT NULL COMMENT 'Chapter ID from new_osce_categories',
  `title` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL COMMENT 'PDF file path',
  `is_premium` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: free, 1: premium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Notes:**
- `category` field links to chapter ID in `new_osce_categories` table
- `pdf_file` stores the PDF file path

## Required API Endpoints

### 1. Get Categories (Main Page)
**Endpoint:** `POST /api/new-osce/categories`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Request Body:** 
```json
{}
```

**Response:**
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "parent_id": 0,
      "name": "Category Name",
      "color": "0deg",
      "status": 1,
      "is_premium": 0,
      "created_at": "2024-01-01 00:00:00",
      "updated_at": "2024-01-01 00:00:00"
    }
  ]
}
```

**Implementation:**
```php
public function categories(Request $request)
{
    $categories = NewOsceCategory::where('parent_id', 0)
                                  ->where('status', 1)
                                  ->orderBy('id', 'asc')
                                  ->get();
    
    return response()->json([
        'status' => true,
        'data' => $categories
    ]);
}
```

---

### 2. Get Chapters
**Endpoint:** `POST /api/new-osce/chapters`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "category_id": 1
}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "category_name": "Category Name",
    "chapters": [
      {
        "id": 2,
        "parent_id": 1,
        "name": "Chapter 1",
        "color": "120deg",
        "status": 1,
        "is_premium": 0
      }
    ]
  }
}
```

**Implementation:**
```php
public function chapters(Request $request)
{
    $categoryId = $request->input('category_id');
    
    $category = NewOsceCategory::find($categoryId);
    $chapters = NewOsceCategory::where('parent_id', $categoryId)
                                ->where('status', 1)
                                ->orderBy('id', 'asc')
                                ->get();
    
    return response()->json([
        'status' => true,
        'data' => [
            'category_name' => $category->name ?? 'Chapters',
            'chapters' => $chapters
        ]
    ]);
}
```

---

### 3. Get Items by Chapter
**Endpoint:** `POST /api/new-osce/items-by-chapter`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "chapter_id": 2
}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "chapter_name": "Chapter 1",
    "items": [
      {
        "id": 1,
        "category": 2,
        "title": "OSCE Item 1",
        "sort_order": 1,
        "image": "image.jpg",
        "description": "Description",
        "pdf_file": "osce1.pdf",
        "is_premium": 0
      }
    ]
  }
}
```

**Implementation:**
```php
public function itemsByChapter(Request $request)
{
    $chapterId = $request->input('chapter_id');
    
    $chapter = NewOsceCategory::find($chapterId);
    $items = NewOsce::where('category', $chapterId)
                    ->orderBy('sort_order', 'asc')
                    ->get();
    
    return response()->json([
        'status' => true,
        'data' => [
            'chapter_name' => $chapter->name ?? 'Items',
            'items' => $items
        ]
    ]);
}
```

---

### 4. Get Item by ID
**Endpoint:** `POST /api/new-osce/get-item-by-id`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "item_id": 1
}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "id": 1,
    "category": 2,
    "title": "OSCE Item 1",
    "sort_order": 1,
    "image": "image.jpg",
    "description": "Description",
    "pdf_file": "osce1.pdf",
    "is_premium": 0,
    "created_at": "2024-01-01 00:00:00",
    "updated_at": "2024-01-01 00:00:00"
  }
}
```

**Implementation:**
```php
public function getItemById(Request $request)
{
    $itemId = $request->input('item_id');
    $item = NewOsce::find($itemId);
    
    if (!$item) {
        return response()->json([
            'status' => false,
            'message' => 'Item not found'
        ], 404);
    }
    
    return response()->json([
        'status' => true,
        'data' => $item
    ]);
}
```

---

### 5. Toggle Bookmark
**Endpoint:** `POST /api/new-osce/change-bookmark`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "user_id": 1,
  "item_id": 1
}
```

**Response:**
```json
{
  "status": true,
  "message": "Bookmark added" // or "Bookmark removed"
}
```

**Database Table Required:**
```sql
CREATE TABLE `new_osce_bookmarks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bookmark` (`user_id`, `item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Implementation:**
```php
public function changeBookmark(Request $request)
{
    $userId = $request->input('user_id');
    $itemId = $request->input('item_id');
    
    $bookmark = NewOsceBookmark::where('user_id', $userId)
                                ->where('item_id', $itemId)
                                ->first();
    
    if ($bookmark) {
        $bookmark->delete();
        $message = 'Bookmark removed';
    } else {
        NewOsceBookmark::create([
            'user_id' => $userId,
            'item_id' => $itemId
        ]);
        $message = 'Bookmark added';
    }
    
    return response()->json([
        'status' => true,
        'message' => $message
    ]);
}
```

---

### 6. Get User Bookmarks
**Endpoint:** `POST /api/new-osce/get-bookmarks`

**Request Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "user_id": 1
}
```

**Response:**
```json
{
  "status": true,
  "data": {
    "list": {
      "data": [
        {
          "id": 1,
          "category": 2,
          "title": "OSCE Item 1",
          "pdf_file": "osce1.pdf",
          "is_premium": 0
        }
      ]
    }
  }
}
```

**Implementation:**
```php
public function getBookmarks(Request $request)
{
    $userId = $request->input('user_id');
    
    $bookmarks = NewOsceBookmark::where('user_id', $userId)
                                 ->with('item')
                                 ->get()
                                 ->pluck('item');
    
    return response()->json([
        'status' => true,
        'data' => [
            'list' => [
                'data' => $bookmarks
            ]
        ]
    ]);
}
```

---

## Laravel Routes Configuration

Add these routes to `admin/application/routes/api.php`:

```php
// New OSCE Routes
Route::prefix('new-osce')->group(function () {
    Route::post('/categories', [NewOsceController::class, 'categories']);
   Route::post('/chapters', [NewOsceController::class, 'chapters']);
    Route::post('/items-by-chapter', [NewOsceController::class, 'itemsByChapter']);
    Route::post('/get-item-by-id', [NewOsceController::class, 'getItemById']);
    Route::post('/change-bookmark', [NewOsceController::class, 'changeBookmark']);
    Route::post('/get-bookmarks', [NewOsceController::class, 'getBookmarks']);
});
```

---

## Laravel Models

### NewOsceCategory Model
Create: `admin/application/app/Models/NewOsceCategory.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewOsceCategory extends Model
{
    protected $table = 'new_osce_categories';
    
    protected $fillable = [
        'parent_id',
        'name',
        'color',
        'status',
        'is_premium'
    ];
    
    public function children()
    {
        return $this->hasMany(NewOsceCategory::class, 'parent_id');
    }
    
    public function parent()
    {
        return $this->belongsTo(NewOsceCategory::class, 'parent_id');
    }
    
    public function items()
    {
        return $this->hasMany(NewOsce::class, 'category');
    }
}
```

### NewOsce Model
Create: `admin/application/app/Models/NewOsce.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewOsce extends Model
{
    protected $table = 'new_osce';
    
    protected $fillable = [
        'category',
        'title',
        'sort_order',
        'image',
        'description',
        'pdf_file',
        'is_premium'
    ];
    
    public function chapter()
    {
        return $this->belongsTo(NewOsceCategory::class, 'category');
    }
    
    public function bookmarks()
    {
        return $this->hasMany(NewOsceBookmark::class, 'item_id');
    }
}
```

### NewOsceBookmark Model
Create: `admin/application/app/Models/NewOsceBookmark.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewOsceBookmark extends Model
{
    protected $table = 'new_osce_bookmarks';
    
    protected $fillable = [
        'user_id',
        'item_id'
    ];
    
    public function item()
    {
        return $this->belongsTo(NewOsce::class, 'item_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```

---

## Laravel Controller

Create: `admin/application/app/Http/Controllers/Api/NewOsceController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewOsceCategory;
use App\Models\NewOsce;
use App\Models\NewOsceBookmark;
use Illuminate\Http\Request;

class NewOsceController extends Controller
{
    // Get all parent categories
    public function categories(Request $request)
    {
        $categories = NewOsceCategory::where('parent_id', 0)
                                      ->where('status', 1)
                                      ->orderBy('id', 'asc')
                                      ->get();
        
        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }
    
    // Get chapters for a category
    public function chapters(Request $request)
    {
        $categoryId = $request->input('category_id');
        
        $category = NewOsceCategory::find($categoryId);
        $chapters = NewOsceCategory::where('parent_id', $categoryId)
                                    ->where('status', 1)
                                    ->orderBy('id', 'asc')
                                    ->get();
        
        return response()->json([
            'status' => true,
            'data' => [
                'category_name' => $category->name ?? 'Chapters',
                'chapters' => $chapters
            ]
        ]);
    }
    
    // Get items for a chapter
    public function itemsByChapter(Request $request)
    {
        $chapterId = $request->input('chapter_id');
        
        $chapter = NewOsceCategory::find($chapterId);
        $items = NewOsce::where('category', $chapterId)
                        ->orderBy('sort_order', 'asc')
                        ->get();
        
        return response()->json([
            'status' => true,
            'data' => [
                'chapter_name' => $chapter->name ?? 'Items',
                'items' => $items
            ]
        ]);
    }
    
    // Get single item by ID
    public function getItemById(Request $request)
    {
        $itemId = $request->input('item_id');
        $item = NewOsce::find($itemId);
        
        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Item not found'
            ], 404);
        }
        
        return response()->json([
            'status' => true,
            'data' => $item
        ]);
    }
    
    // Toggle bookmark
    public function changeBookmark(Request $request)
    {
        $userId = $request->input('user_id');
        $itemId = $request->input('item_id');
        
        $bookmark = NewOsceBookmark::where('user_id', $userId)
                                    ->where('item_id', $itemId)
                                    ->first();
        
        if ($bookmark) {
            $bookmark->delete();
            $message = 'Bookmark removed';
        } else {
            NewOsceBookmark::create([
                'user_id' => $userId,
                'item_id' => $itemId
            ]);
            $message = 'Bookmark added';
        }
        
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
    
    // Get user bookmarks
    public function getBookmarks(Request $request)
    {
        $userId = $request->input('user_id');
        
        $bookmarks = NewOsceBookmark::where('user_id', $userId)
                                     ->with('item')
                                     ->get()
                                     ->pluck('item')
                                     ->filter(); // Remove nulls
        
        return response()->json([
            'status' => true,
            'data' => [
                'list' => [
                    'data' => $bookmarks
                ]
            ]
        ]);
    }
}
```

---

## PDF Storage Path

PDFs should be stored in:
```
admin/assets/new_osce_pdf/
```

**Example paths:**
- `admin/assets/new_osce_pdf/osce1.pdf`
- `admin/assets/new_osce_pdf/chapter1/osce2.pdf`

The frontend will access them via:
```
https://admin.droutlier.com/assets/new_osce_pdf/{filename}.pdf
```

---

## Testing Checklist

- [ ] Create sample categories (parent_id = 0)
- [ ] Create sample chapters (parent_id = category_id)
- [ ] Create sample OSCE items with PDFs
- [ ] Test `/api/new-osce/categories` endpoint
- [ ] Test `/api/new-osce/chapters` endpoint
- [ ] Test `/api/new-osce/items-by-chapter` endpoint
- [ ] Test `/api/new-osce/get-item-by-id` endpoint
- [ ] Test `/api/new-osce/change-bookmark` endpoint
- [ ] Test `/api/new-osce/get-bookmarks` endpoint
- [ ] Verify PDF files are accessible
- [ ] Test frontend at `/new-osce`

---

## Frontend URLs

- Main Page: `https://www.droutlier.com/new-osce`
- Chapters: `https://www.droutlier.com/new-osce/chapters?id={categoryId}`
- Items: `https://www.droutlier.com/new-osce/category?id={chapterId}&parentId={categoryId}`
- Viewer: `https://www.droutlier.com/new-osce/view?id={chapterId}&itemId={itemId}&parentId={categoryId}`

---

## Summary

The New OSCE module is a complete copy of the New Spotters module with the following changes:
- Frontend URLs: `/new-spotters` → `/new-osce`
- API endpoints: `/api/new-spotters/` → `/api/new-osce/`
- Database tables: `new_spotters_*` → `new_osce_*`
- PDF path: `new_spotters_pdf` → `new_osce_pdf`

All functionality remains identical, including:
- PDF viewer with zoom controls
- Bookmark functionality
- Chapter navigation
- Sidebar with all modules visible
- Mobile responsive design
