<?php

use App\Http\Controllers\Api\QuizResponseController;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");



Route::namespace('Api')->name('api.')->group(function(){
    Route::controller('UserController')->group(function(){
        Route::post('profile-setting', 'submitProfile');
        Route::post('change-password', 'submitPassword');
        Route::post('user-details', 'userDetails');
        Route::post('google-login', 'requestTokenGoogle');
        Route::post('delete-account', 'deleteAccount');
        Route::post('search', 'Search');


    });
    Route::group(['middleware' => 'cors'], function () {
        Route::get('general-setting',function()
        {
            $general = GeneralSetting::first();
            $notify[] = 'General setting data';
            return response()->json([
                'remark'=>'general_setting',
                'status'=>'success',
                'message'=>['success'=>$notify],
                'data'=>[
                    'general_setting'=>$general,
                ],
            ]);
        });

        Route::get('get-countries',function(){
            $c = json_decode(file_get_contents(resource_path('views/includes/country.json')));
            $notify[] = 'General setting data';
            foreach($c as $k => $country){
                $countries[] = [
                    'country'=>$country->country,
                    'dial_code'=>$country->dial_code,
                    'country_code'=>$k,
                ];
            }
            return response()->json([
                'remark'=>'country_data',
                'status'=>'success',
                'message'=>['success'=>$notify],
                'data'=>[
                    'countries'=>$countries,
                ],
            ]);
        });

        Route::namespace('Auth')->group(function(){
            Route::post('login', 'LoginController@login');
            Route::post('register', 'RegisterController@register');

            Route::controller('ForgotPasswordController')->group(function(){
                Route::post('password/email', 'sendResetCodeEmail')->name('password.email');
                Route::post('password/verify-code', 'verifyCode')->name('password.verify.code');
                Route::post('password/reset', 'reset')->name('password.update');
            });
        });

        // Public Navigation (no auth required)
        Route::controller('NavigationController')->prefix('navigation')->group(function() {
            Route::get('/public', 'getPublicNavigation');
        });


        Route::middleware('auth:sanctum')->group(function () {

            // Navigation (authenticated)
            Route::controller('NavigationController')->prefix('navigation')->group(function() {
                Route::get('/', 'getNavigation');
            });

            // Subscription Routes
            Route::controller('SubscriptionController')->prefix('subscription')->group(function() {
                Route::get('/plans', 'getPlans');
                Route::post('/create-order', 'createOrder');
                Route::post('/verify-payment', 'verifyPayment');
                Route::get('/my-subscription', 'getMySubscription');
                Route::get('/history', 'getSubscriptionHistory');
                Route::post('/check-access', 'checkModuleAccess');
                Route::get('/accessible-modules', 'getAccessibleModules');
            });

            //News Route
            Route::middleware('module.access:notes')->controller('BlogsController')->name('blogs.')->prefix('news')->group(function(){
                Route::post('/list', 'index')->name('blog-index');
                Route::post('/show', 'show')->name('blog-show');
            });

            Route::middleware('module.access:quizora')->controller('QuizController')->name('quiz.')->prefix('quiz')->group(function(){
                Route::post('categories', 'categoryList');
                Route::get('/{quizId}', 'getQuizById');
                Route::post('question/{questionId}', 'getQuestionById');
                Route::post('change-bookmark', 'changeQuizBookmark');
                Route::post('bookmarks', 'getQuizBookmarks');
                // Route::post('change-status', 'changeQuizStatus');
            });

            Route::middleware('module.access:quizora')->post('quiz/submit-response', [QuizResponseController::class, 'submitQuizResponse']);
            Route::middleware('module.access:quizora')->post('quiz/result', [QuizResponseController::class, 'getQuizResult']);
            Route::middleware('module.access:quizora')->post('quiz/change-status', [QuizResponseController::class,'changeQuizStatus']);


            Route::middleware('module.access:practical-essentials')->controller('BasicsController')->name('basic.')->prefix('basic')->group(function(){
                Route::post('/list', 'index')->name('blog-index');
                Route::post('/show', 'show')->name('blog-show');
            });

            Route::middleware('module.access:watch-and-learn')->controller('WatchController')->name('watch-and-learn.')->prefix('watch-and-learn')->group(function(){
                Route::post('/list', 'index')->name('watch-index');
                Route::post('/show', 'show')->name('watch-show');
            });

            Route::middleware('module.access:ai-rad')->controller('MunchiesController')->name('munchies.')->prefix('munchies')->group(function(){
                Route::post('/list', 'index')->name('munchies-index');
                Route::post('/show', 'show')->name('munchies-show');
                Route::post('/munchies-details', 'munchieDetails')->name('munchies-details');

            });

            Route::middleware('module.access:osce')->controller('OsceController')->name('osce.')->prefix('osce')->group(function(){
                Route::post('/list', 'categoryList')->name('osce-category-list');
                Route::post('/category-osce', 'categoryOsces')->name('category-osce');
                Route::post('/get-osce-by-id', 'getOsceById')->name('get-osce-by-id');
                Route::post('/change-osce-bookmark', 'changeBookOsce')->name('change-osce-bookmark');
                Route::post('/get-osce-bookmark', 'getBookOsce')->name('get-osce-bookmark');
                Route::post('/osce-details', 'osceDetails')->name('osce-details');
                Route::get('/osce-show/{id}', 'showOsceDetails')->name('osce-details.show');
                Route::post('/list-all', 'indexAll')->name('osce-index.all');
            });

            Route::middleware('module.access:notes')->controller('CategoryController')->name('category.')->prefix('note')->group(function(){
                Route::post('/list', 'index')->name('index');
                Route::post('/category-notes', 'Notes')->name('category-notes');
                Route::post('/change-note-bookmark-status', 'changeNoteBookStatus')->name('change-bookmark-status');
                Route::post('/get-note-bookmark', 'getBookBlogStatus')->name('get-bookmark');
                Route::post('/read-status', 'ReadStatus')->name('read-status');
                Route::post('/note-details', 'noteDetails')->name('note-details');
            });

            Route::middleware('module.access:ai-rad')->controller('MunchiesCategoryController')->name('category-munchie.')->prefix('category-munchie')->group(function(){
                Route::post('/list', 'index')->name('index');
                Route::post('/category-munchie', 'Notes')->name('category-munchie');
                Route::post('/change-munchie-bookmark-status', 'changeNoteBookStatus')->name('change-bookmark-status');
                Route::post('/get-munchie-bookmark', 'getBookBlogStatus')->name('get-bookmark');
                Route::post('/read-status', 'ReadStatus')->name('read-status');
            });

            Route::middleware('module.access:practical-essentials')->controller('BasicCategoryController')->name('basic-category.')->prefix('basic-category')->group(function(){
                Route::post('/list', 'index')->name('index');
                Route::post('/category-basic', 'Notes')->name('category-basic');
                Route::post('/change-basic-bookmark-status', 'changeNoteBookStatus')->name('change-bookmark-status');
                Route::post('/get-basic-bookmark', 'getBookBlogStatus')->name('get-bookmark');
                Route::post('/read-status', 'ReadStatus')->name('read-status');
            });

            Route::middleware('module.access:watch-and-learn')->controller('WatchAndLearnCategoryController')->name('watch-and-learn-category.')->prefix('watch-and-learn-category')->group(function(){
                Route::post('/list', 'index')->name('index');
                Route::post('/category-watch', 'Notes')->name('category-watch');
                Route::post('/change-watch-bookmark-status', 'changeNoteBookStatus')->name('change-bookmark-status');
                Route::post('/get-watch-bookmark', 'getBookBlogStatus')->name('get-bookmark');
                Route::post('/read-status', 'ReadStatus')->name('read-status');
            });


            Route::middleware('module.access:spotters')->controller('SpottersController')->name('spotters.')->prefix('spotters')->group(function(){
                Route::post('/list', 'index')->name('index');
                Route::post('/category-spotters', 'spotters')->name('category-spotters');
                Route::post('/spotters-details', 'spottersDetails')->name('spotters-details');

                Route::post('/list-all', 'allList')->name('index.all');
                Route::post('/change-bookmark-status', 'changeBookStatus')->name('change-bookmark-status');
                Route::post('/get-bookmark', 'getBookStatus')->name('get-bookmark');

                // New routes for categories and chapters
                Route::post('/categories', 'categories')->name('categories');
                Route::post('/chapters', 'chapters')->name('chapters');
                Route::post('/list-by-category', 'listByCategory')->name('list-by-category');
            });

            // New Spotters API Routes
            Route::controller('NewSpottersApiController')->name('new-spotters.')->prefix('new-spotters')->group(function(){
                Route::post('/categories', 'getCategories')->name('categories');
                Route::post('/chapters', 'getChapters')->name('chapters');
                Route::post('/items-by-chapter', 'getItemsByChapter')->name('items-by-chapter');
                Route::post('/item', 'getItem')->name('item');
            });

            // New OSCE API Routes
            Route::controller('NewOsceApiController')->name('new-osce.')->prefix('new-osce')->group(function(){
                Route::post('/categories', 'getCategories')->name('categories');
                Route::post('/chapters', 'getChapters')->name('chapters');
                Route::post('/items-by-chapter', 'getItemsByChapter')->name('items-by-chapter');
                Route::post('/item', 'getItem')->name('item');
            });

            // New Exam Cases API Routes
            Route::controller('NewExamCasesApiController')->name('new-exam-cases.')->prefix('new-exam-cases')->group(function(){
                Route::post('/categories', 'getCategories')->name('categories');
                Route::post('/chapters', 'getChapters')->name('chapters');
                Route::post('/items-by-chapter', 'getItemsByChapter')->name('items-by-chapter');
                Route::post('/get-item-by-id', 'getItem')->name('item');
                Route::post('/change-bookmark', 'changeBookmark')->name('change-bookmark');
                Route::post('/get-bookmarks', 'getBookmarks')->name('get-bookmarks');
            });

            // New Table Viva API Routes
            Route::controller('NewTableVivaApiController')->name('new-table-viva.')->prefix('new-table-viva')->group(function(){
                Route::post('/categories', 'getCategories')->name('categories');
                Route::post('/chapters', 'getChapters')->name('chapters');
                Route::post('/items-by-chapter', 'getItemsByChapter')->name('items-by-chapter');
                Route::post('/get-item-by-id', 'getItem')->name('item');
                Route::post('/change-bookmark', 'changeBookmark')->name('change-bookmark');
                Route::post('/get-bookmarks', 'getBookmarks')->name('get-bookmarks');
            });

            // Theory Notes API Routes
            Route::controller('TheoryNotesApiController')->name('theory-notes.')->prefix('theory-notes')->group(function(){
                Route::post('/categories', 'getCategories')->name('categories');
                Route::post('/chapters', 'getChapters')->name('chapters');
                Route::post('/items-by-chapter', 'getItemsByChapter')->name('items-by-chapter');
                Route::post('/get-item-by-id', 'getItem')->name('item');
                Route::post('/change-bookmark', 'changeBookmark')->name('change-bookmark');
                Route::post('/get-bookmarks', 'getBookmarks')->name('get-bookmarks');
            });

            // Practical Essentials API Routes
            Route::controller('PracticalEssentialsApiController')->name('practical-essentials.')->prefix('practical-essentials')->group(function(){
                Route::post('/categories', 'getCategories')->name('categories');
                Route::post('/chapters', 'getChapters')->name('chapters');
                Route::post('/items-by-chapter', 'getItemsByChapter')->name('items-by-chapter');
                Route::post('/item', 'getItem')->name('item');
            });

            // AI Rads API Routes
            Route::controller('AiRadsApiController')->name('ai-rads.')->prefix('ai-rads')->group(function(){
                Route::post('/categories', 'getCategories')->name('categories');
                Route::post('/chapters', 'getChapters')->name('chapters');
                Route::post('/items-by-chapter', 'getItemsByChapter')->name('items-by-chapter');
                Route::post('/item', 'getItem')->name('item');
            });

            Route::controller('ProfileController')->group(function(){
                Route::post('profile-update', 'submitProfile')->name('profile-update');
                Route::post('token-update', 'tokenStore')->name('token-update');
            });

            Route::controller('UserController')->group(function(){
                Route::post('user-data', 'userDetailsData')->name('user-data');
            });

            //authorization
            Route::controller('AuthorizationController')->group(function(){
                Route::get('authorization', 'authorization')->name('authorization');
                Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
                Route::post('verify-email', 'emailVerification')->name('verify.email');
                Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
                Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
            });

            Route::middleware(['check.status'])->group(function () {
                Route::post('user-data-submit', 'UserController@userDataSubmit')->name('data.submit');

                Route::middleware('registration.complete')->group(function(){
                    Route::get('dashboard',function(){
                        return auth()->user();
                    });

                    Route::get('user-info',function(){
                        $notify[] = 'User information';
                        return response()->json([
                            'remark'=>'user_info',
                            'status'=>'success',
                            'message'=>['success'=>$notify],
                            'data'=>[
                                'user'=>auth()->user()
                            ]
                        ]);
                    });

                    Route::controller('UserController')->group(function(){

                        //Report
                        Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                        Route::get('transactions','transactions')->name('transactions');

                    });

                    //Profile setting



                });
            });

            Route::get('logout', 'Auth\LoginController@logout');


        });
    });
});
