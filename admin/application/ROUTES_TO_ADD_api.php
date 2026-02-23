/**
 * ADD THESE ROUTES TO: admin/application/routes/api.php
 * Insert after the existing New Spotters API routes section
 */

// ========================================
// NEW EXAM CASES API ROUTES
// ========================================
Route::controller('NewExamCasesApiController')->name('new-exam-cases.')->prefix('new-exam-cases')->group(function(){
    Route::post('/categories', 'categories');
    Route::post('/chapters', 'chapters');
    Route::post('/items-by-chapter', 'itemsByChapter');
    Route::post('/get-item-by-id', 'getItemById');
    Route::post('/change-bookmark', 'changeBookmark');
    Route::post('/get-bookmarks', 'getBookmarks');
});

// ========================================
// NEW TABLE VIVA API ROUTES
// ========================================
Route::controller('NewTableVivaApiController')->name('new-table-viva.')->prefix('new-table-viva')->group(function(){
    Route::post('/categories', 'categories');
    Route::post('/chapters', 'chapters');
    Route::post('/items-by-chapter', 'itemsByChapter');
    Route::post('/get-item-by-id', 'getItemById');
    Route::post('/change-bookmark', 'changeBookmark');
    Route::post('/get-bookmarks', 'getBookmarks');
});

// ========================================
// THEORY NOTES API ROUTES
// ========================================
Route::controller('TheoryNotesApiController')->name('theory-notes.')->prefix('theory-notes')->group(function(){
    Route::post('/categories', 'categories');
    Route::post('/chapters', 'chapters');
    Route::post('/items-by-chapter', 'itemsByChapter');
    Route::post('/get-item-by-id', 'getItemById');
    Route::post('/change-bookmark', 'changeBookmark');
    Route::post('/get-bookmarks', 'getBookmarks');
});
