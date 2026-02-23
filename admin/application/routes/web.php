<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use App\Lib\Router;
use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('user/verify/{token}', [RegisterController::class, 'verifyEmail'])->name('user.verify');  

Route::controller('CKEditorController')->group(function () {

    Route::post('/ckeditor/upload', 'upload')->name('ckeditor.image-upload');

});


Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog/{slug}/{id}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    // blog
    Route::get('/blog','blog')->name('blog');
    // plan
    Route::get('/plan','plan')->name('plans');

    // services
    Route::get('/services','services')->name('services');
    Route::get('service/{slug}/{id}', 'serviceDetails')->name('service.details');
    Route::get('service/filter', 'serviceFilter')->name('service.filtered');

    // portfolio
    Route::get('portfolio/{slug}/{id}', 'portfolioDetails')->name('portfolio.details');
    Route::get('portfolio', 'fetchPortfolio')->name('portfolio');
    Route::get('portfolio/filter', 'portfolioFilter')->name('portfolio.filtered');

    // subscriber
    Route::post('/subscribe','subscribe')->name('subscribe');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');

});
 