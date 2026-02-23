<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;




Route::namespace('Auth')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('/', 'login')->name('login');
        Route::get('logout', 'logout')->name('logout');
    });

    // Admin Password Reset
    Route::controller('ForgotPasswordController')->group(function () {
        Route::get('password/reset', 'showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'sendResetCodeEmail');
        Route::get('password/code-verify', 'codeVerify')->name('password.code.verify');
        Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'reset')->name('password.change');
    });
});

Route::middleware('admin')->group(function () {
    Route::get('/cc', function () {

        Artisan::call('cache:clear');
        Artisan::call('optimize:clear');
        Artisan::call('route:clear');


        Artisan::call('route:clear');

        return 'All caches cleared and application optimized!';
    });

    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request/report', 'requestReport')->name('request.report');
        Route::post('request/report', 'reportSubmit');

        Route::get('download/attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });

    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('manage/users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email/verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email/unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile/unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile/verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('mobile/verified', 'mobileVerifiedUsers')->name('mobile.verified');
        Route::get('with/balance', 'usersWithBalance')->name('with.balance');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('add/sub/balance/{id}', 'addSubBalance')->name('add.sub.balance');
        Route::get('send/notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send/notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send/notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send/notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('notification/log/{id}', 'notificationLog')->name('notification.log');
    });



    // service
    Route::controller('ServiceController')->name('service.')->prefix('service')->group(function () {

        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('update', 'update')->name('update');
        Route::get('delete', 'delete')->name('delete');

        Route::get('approved/orders', 'getApprovedorders')->name('approved.orders');
        Route::get('pending/orders', 'getPendingdorders')->name('pending.orders');
    });
    // service
    Route::controller('CategoryController')->name('category.')->prefix('category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::get('/data', 'categoryData');

    });

    // spotter category
    Route::controller('SpotterCategoryController')->name('spotters-category.')->prefix('spotters-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
         Route::get('/data', 'spotterCategoryData');
    });


    // Report
    Route::controller('ReportController')->group(function () {
        Route::get('report/transaction', 'transaction')->name('report.transaction');
        Route::get('report/login/history', 'loginHistory')->name('report.login.history');
        Route::get('report/login/ipHistory/{ip}', 'loginIpHistory')->name('report.login.ipHistory');
        Route::get('report/notification/history', 'notificationHistory')->name('report.notification.history');
        Route::get('report/email/detail/{id}', 'emailDetails')->name('report.email.details');
    });



    // Language Manager
    Route::controller('LanguageController')->prefix('manage')->group(function () {
        Route::get('languages', 'langManage')->name('language.manage');
        Route::post('language', 'langStore')->name('language.manage.store');
        Route::post('language/delete/{id}', 'langDelete')->name('language.manage.delete');
        Route::post('language/update/{id}', 'langUpdate')->name('language.manage.update');
        Route::get('language/edit/{id}', 'langEdit')->name('language.key');
        Route::post('language/import', 'langImport')->name('language.import.lang');
        Route::post('language/store/key/{id}', 'storeLanguageJson')->name('language.store.key');
        Route::post('language/delete/key/{id}', 'deleteLanguageJson')->name('language.delete.key');
        Route::post('language/update/key/{id}', 'updateLanguageJson')->name('language.update.key');
    });

    Route::controller('GeneralSettingController')->group(function () {
        // General Setting
        Route::get('global/settings', 'index')->name('setting.index');
        Route::post('global/settings', 'update')->name('setting.update');

        //configuration
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');

        // Logo-Icon
        Route::get('setting/logo', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo', 'logoIconUpdate')->name('setting.logo.icon');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');

        //socialite credentials
        Route::get('setting/social/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::post('setting/social/credentials/update/{key}', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
        Route::post('setting/social/credentials/status/{key}', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status.update');
    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notifications')->group(function () {
        //Template Setting
        Route::get('global', 'global')->name('global');
        Route::post('global/update', 'globalUpdate')->name('global.update');
        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');
    });

    // Plugin
    Route::controller('ExtensionController')->group(function () {
        Route::get('extensions', 'index')->name('extensions.index');
        Route::post('extensions/update/{id}', 'update')->name('extensions.update');
        Route::post('extensions/status/{id}', 'status')->name('extensions.status');
    });

    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');

    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('themes', 'templates')->name('templates');
            Route::post('themes', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->prefix('manage')->group(function () {
            Route::get('pages', 'managePages')->name('manage.pages');
            Route::post('pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('section/{id}', 'manageSection')->name('manage.section');
            Route::post('section/{id}', 'manageSectionUpdate')->name('manage.section.update');
        });
    });


    //note Route
    Route::controller('BlogsController')->name('blogs.')->prefix('note')->group(function () {
        Route::get('/list', 'index')->name('blog-index');
        Route::get('/create', 'create')->name('blog-create');
        Route::post('/store', 'store')->name('blog-store');
        Route::get('/edit/{id}', 'edit')->name('blog-edit');
        Route::post('/update/{id}', 'update')->name('blog-update');
        Route::post('/delete/{id}', 'delete')->name('blog-delete');
        Route::post('/fcmToken', 'updateToken')->name('fcmToken');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
        Route::get('/data', 'blogData');
    });

    //basics
    Route::controller('BasicsController')->name('basic.')->prefix('basic')->group(function () {
        Route::get('/list', 'index')->name('basic-index');
        Route::get('/create', 'create')->name('basic-create');
        Route::post('/store', 'store')->name('basic-store');
        Route::get('/edit/{id}', 'edit')->name('basic-edit');
        Route::post('/update/{id}', 'update')->name('basic-update');
        Route::post('/delete/{id}', 'delete')->name('basic-delete');
        Route::post('/fcmToken', 'updateToken')->name('fcmToken');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
        Route::get('/data', 'basicData');
    });
    //basic category
    Route::controller('BasicCategoryController')->name('basic-category.')->prefix('basic-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::get('/data', 'basicCategoryData');
    });


    //munchies
    Route::controller('MunchiesController')->name('munchies.')->prefix('munchies')->group(function () {
        Route::get('/list', 'index')->name('munchies-index');
        Route::get('/create', 'create')->name('munchies-create');
        Route::post('/store', 'store')->name('munchies-store');
        Route::get('/edit/{id}', 'edit')->name('munchies-edit');
        Route::post('/update/{id}', 'update')->name('munchies-update');
        Route::post('/delete/{id}', 'delete')->name('munchies-delete');
        Route::post('/fcmToken', 'updateToken')->name('fcmToken');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
        Route::get('/data', 'munchiesData');
    });
    //munchies category
    Route::controller('MunchiesCategoryController')->name('munchies-category.')->prefix('munchies-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::get('/data', 'munchiesCategoryData');
    });

    //watch and learn
    Route::controller('WatchController')->name('watch-and-learn.')->prefix('watch-and-learn')->group(function () {
        Route::get('/list', 'index')->name('watch-index');
        Route::get('/create', 'create')->name('watch-create');
        Route::post('/store', 'store')->name('watch-store');
        Route::get('/edit/{id}', 'edit')->name('watch-edit');
        Route::post('/update/{id}', 'update')->name('watch-update');
        Route::post('/delete/{id}', 'delete')->name('watch-delete');
        Route::post('/fcmToken', 'updateToken')->name('fcmToken');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
         Route::get('/data', 'watchData');
    });
    //watch and learn category
    Route::controller('WatchCategoryController')->name('watch-and-learn-category.')->prefix('watch-and-learn-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
          Route::get('/data', 'watchlearnCategoryData');
    });


    //spotters Route
    Route::controller('SpottersController')->name('spotters.')->prefix('spotters')->group(function () {
        Route::get('/list', 'index')->name('spotters-index');
        Route::get('/create', 'create')->name('spotters-create');
        Route::post('/store', 'store')->name('spotters-store');
        Route::get('/edit/{id}', 'edit')->name('spotters-edit');
        Route::post('/update/{id}', 'update')->name('spotters-update');
        Route::post('/delete/{id}', 'delete')->name('spotters-delete');
        Route::post('/fcmToken', 'updateToken')->name('fcmToken');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
        Route::get('/data', 'spotterData');
    });
    //spotters category
    Route::controller('SpotterCategoryController')->name('spotters-category.')->prefix('spotters-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::get('/data', 'spotterCategoryData');
    });

    //new spotters Route
    Route::controller('NewSpottersController')->name('new-spotters.')->prefix('new-spotters')->group(function () {
        Route::get('/list', 'index')->name('new-spotters-index');
        Route::get('/create', 'create')->name('new-spotters-create');
        Route::post('/store', 'store')->name('new-spotters-store');
        Route::get('/edit/{id}', 'edit')->name('new-spotters-edit');
        Route::post('/update/{id}', 'update')->name('new-spotters-update');
        Route::post('/delete/{id}', 'delete')->name('new-spotters-delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::post('/fcmToken', 'updateToken')->name('fcmToken');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
        Route::get('/data', 'newSpotterData');
    });
    //new spotters category
    Route::controller('NewSpotterCategoryController')->name('new-spotters-category.')->prefix('new-spotters-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::get('/data', 'newSpotterCategoryData');
    });

    //new exam cases Route
    Route::controller('NewExamCasesController')->name('new-exam-cases.')->prefix('new-exam-cases')->group(function () {
        Route::get('/list', 'index')->name('new-exam-cases-index');
        Route::get('/create', 'create')->name('new-exam-cases-create');
        Route::post('/store', 'store')->name('new-exam-cases-store');
        Route::get('/edit/{id}', 'edit')->name('new-exam-cases-edit');
        Route::post('/update/{id}', 'update')->name('new-exam-cases-update');
        Route::post('/delete/{id}', 'delete')->name('new-exam-cases-delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
    });
    //new exam cases category
    Route::controller('NewExamCasesCategoryController')->name('new-exam-cases-category.')->prefix('new-exam-cases-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
    });

    //new table viva Route
    Route::controller('NewTableVivaController')->name('new-table-viva.')->prefix('new-table-viva')->group(function () {
        Route::get('/list', 'index')->name('new-table-viva-index');
        Route::get('/create', 'create')->name('new-table-viva-create');
        Route::post('/store', 'store')->name('new-table-viva-store');
        Route::get('/edit/{id}', 'edit')->name('new-table-viva-edit');
        Route::post('/update/{id}', 'update')->name('new-table-viva-update');
        Route::post('/delete/{id}', 'delete')->name('new-table-viva-delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
    });
    //new table viva category
    Route::controller('NewTableVivaCategoryController')->name('new-table-viva-category.')->prefix('new-table-viva-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
    });

    //theory notes Route
    Route::controller('TheoryNotesController')->name('theory-notes.')->prefix('theory-notes')->group(function () {
        Route::get('/list', 'index')->name('theory-notes-index');
        Route::get('/create', 'create')->name('theory-notes-create');
        Route::post('/store', 'store')->name('theory-notes-store');
        Route::get('/edit/{id}', 'edit')->name('theory-notes-edit');
        Route::post('/update/{id}', 'update')->name('theory-notes-update');
        Route::post('/delete/{id}', 'delete')->name('theory-notes-delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
    });
    //theory notes category
    Route::controller('TheoryNotesCategoryController')->name('theory-notes-category.')->prefix('theory-notes-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
    });

    //new osce Route
    Route::controller('NewOsceController')->name('new-osce.')->prefix('new-osce')->group(function () {
        Route::get('/list', 'index')->name('new-osce-index');
        Route::get('/create', 'create')->name('new-osce-create');
        Route::post('/store', 'store')->name('new-osce-store');
        Route::get('/edit/{id}', 'edit')->name('new-osce-edit');
        Route::post('/update/{id}', 'update')->name('new-osce-update');
        Route::post('/delete/{id}', 'delete')->name('new-osce-delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::post('/fcmToken', 'updateToken')->name('fcmToken');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
        Route::get('/data', 'newOsceData');
    });
    //new osce category
    Route::controller('NewOsceCategoryController')->name('new-osce-category.')->prefix('new-osce-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::get('/data', 'newOsceCategoryData');
    });













    //practical essentials Route
    Route::controller('PracticalEssentialsController')->name('practical-essentials.')->prefix('practical-essentials')->group(function () {
        Route::get('/list', 'index')->name('practical-essentials-index');
        Route::get('/create', 'create')->name('practical-essentials-create');
        Route::post('/store', 'store')->name('practical-essentials-store');
        Route::get('/edit/{id}', 'edit')->name('practical-essentials-edit');
        Route::post('/update/{id}', 'update')->name('practical-essentials-update');
        Route::post('/delete/{id}', 'delete')->name('practical-essentials-delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::post('/fcmToken', 'updateToken')->name('fcmToken');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
        Route::get('/data', 'practicalEssentialsData');
    });
    //practical essentials category
    Route::controller('PracticalEssentialsCategoryController')->name('practical-essentials-category.')->prefix('practical-essentials-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::get('/data', 'practicalEssentialsCategoryData');
    });

    //ai rads Route
    Route::controller('AiRadsController')->name('ai-rads.')->prefix('ai-rads')->group(function () {
        Route::get('/list', 'index')->name('ai-rads-index');
        Route::get('/create', 'create')->name('ai-rads-create');
        Route::post('/store', 'store')->name('ai-rads-store');
        Route::get('/edit/{id}', 'edit')->name('ai-rads-edit');
        Route::post('/update/{id}', 'update')->name('ai-rads-update');
        Route::post('/delete/{id}', 'delete')->name('ai-rads-delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::post('/fcmToken', 'updateToken')->name('fcmToken');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
        Route::get('/data', 'aiRadsData');
    });
    //ai rads category
    Route::controller('AiRadsCategoryController')->name('ai-rads-category.')->prefix('ai-rads-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/toggle-premium/{id}', 'togglePremium')->name('toggle-premium');
        Route::get('/data', 'practicalEssentialsCategoryData');
    });

    //osce category
    Route::controller('OsceCategoryController')->name('osce-category.')->prefix('osce-category')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::get('/data', 'osceCategoryData');
    });

    //osce Route
    Route::controller('OsceController')->name('osce.')->prefix('osce')->group(function () {
        Route::get('/list', 'index')->name('osce-index');
        Route::get('/create', 'create')->name('osce-create');
        Route::post('/store', 'store')->name('osce-store');
        Route::get('/edit/{id}', 'edit')->name('osce-edit');
        Route::post('/update/{id}', 'update')->name('osce-update');
        Route::post('/delete/{id}', 'delete')->name('osce-delete');
        Route::post('/fcmToken', 'updateToken')->name('fcmToken');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
         Route::get('/data', 'osceData');
    });

    // Help Center Faq
    Route::controller('FaqController')->name('faq.')->prefix('help-center')->group(function () {
        Route::get('/list', 'index')->name('faq-index');
        Route::get('/create', 'create')->name('faq-create');
        Route::post('/store', 'store')->name('faq-store');
        Route::get('/edit/{id}', 'edit')->name('faq-edit');
        Route::post('/update/{id}', 'update')->name('faq-update');
        Route::post('/delete/{id}', 'delete')->name('faq-delete');
    });
    Route::controller('QuizController')->name('quiz.')->prefix('quiz')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/page', 'quizIndex')->name('quiz.index');
        Route::get('page/create', 'quizCreate')->name('quiz.create');
        Route::post('page/store', 'quizStore')->name('quiz.store');
        Route::get('page/edit/{id}', 'quizEdit')->name('quiz.edit');
        Route::post('page/update/{id}', 'quizUpdate')->name('quiz.update');
        Route::post('page/delete/{id}', 'quizDelete')->name('quiz.delete');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/update-sort-order', 'UpdateSortOrder')->name('update-sort-order');
        Route::get('/data', 'quiz');
        Route::get('/data', 'questionData');
    });

    // for quiz categories
    Route::controller('QuizCategoryController')->name('quiz.')->prefix('quiz')->group(function () {

        Route::get('category/', 'index')->name('category.index');
        Route::get('category/create', 'create')->name('category.create');
        Route::post('category/store', 'store')->name('category.store');
        Route::get('category/edit/{id}', 'edit')->name('category.edit');
        Route::post('category/update/{id}', 'update')->name('category.update');
        Route::post('category/delete/{id}', 'delete')->name('category.delete');
        Route::get('/data', 'quizData');
    });

    // Subscription Management
    Route::controller('ModuleController')->name('modules.')->prefix('modules')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/status/{id}', 'status')->name('status');
        Route::get('/data', 'moduleData')->name('data');
    });

    Route::controller('PlanController')->name('plans.')->prefix('plans')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/status/{id}', 'status')->name('status');
        Route::get('/data', 'planData')->name('data');
    });

    Route::controller('SubscriptionController')->name('subscriptions.')->prefix('subscriptions')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/active', 'active')->name('active');
        Route::get('/expired', 'expired')->name('expired');
        Route::get('/cancelled', 'cancelled')->name('cancelled');
        Route::get('/pending', 'pending')->name('pending');
        Route::get('/detail/{id}', 'detail')->name('detail');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::post('/cancel/{id}', 'cancel')->name('cancel');
        Route::post('/extend/{id}', 'extend')->name('extend');
        Route::get('/data', 'subscriptionData')->name('data');
    });

    // Navigation Manager
    Route::controller('NavigationController')->name('navigation.')->prefix('navigation')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'delete')->name('delete');
        Route::post('/status/{id}', 'status')->name('status');
        Route::post('/update-order', 'updateOrder')->name('update-order');
    });


    Route::controller('EmailTemplateController')->name('email.')->prefix('email')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/newsletter', 'newsletterForm')->name('newsletter.form');
        Route::post('/newsletter/send', 'sendNewsletter')->name('newsletter.send');
    });
});
