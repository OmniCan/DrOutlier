/**
 * ADD THESE ROUTES TO: admin/application/routes/admin.php
 * Insert after the existing New Spotters routes section
 */

// ========================================
// NEW EXAM CASES ROUTES
// ========================================
Route::controller('NewExamCasesCategoryController')->name('new-exam-cases-category.')->prefix('new-exam-cases-category')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/delete/{id}', 'delete')->name('delete');
    Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
    Route::post('/toggle-status/{id}', 'toggleStatus')->name('toggle-status');
});

Route::controller('NewExamCasesController')->name('new-exam-cases.')->prefix('new-exam-cases')->group(function () {
    Route::get('/list', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/delete/{id}', 'delete')->name('delete');
    Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
});

// ========================================
// NEW TABLE VIVA ROUTES
// ========================================
Route::controller('NewTableVivaCategoryController')->name('new-table-viva-category.')->prefix('new-table-viva-category')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/delete/{id}', 'delete')->name('delete');
    Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
    Route::post('/toggle-status/{id}', 'toggleStatus')->name('toggle-status');
});

Route::controller('NewTableVivaController')->name('new-table-viva.')->prefix('new-table-viva')->group(function () {
    Route::get('/list', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/delete/{id}', 'delete')->name('delete');
    Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
});

// ========================================
// THEORY NOTES ROUTES
// ========================================
Route::controller('TheoryNotesCategoryController')->name('theory-notes-category.')->prefix('theory-notes-category')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/delete/{id}', 'delete')->name('delete');
    Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
    Route::post('/toggle-status/{id}', 'toggleStatus')->name('toggle-status');
});

Route::controller('TheoryNotesController')->name('theory-notes.')->prefix('theory-notes')->group(function () {
    Route::get('/list', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/delete/{id}', 'delete')->name('delete');
    Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
});
