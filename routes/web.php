<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LineNotifyController;
// use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ScheduleController;
use App\Http\Middleware\ValidatePkMiddleware;

// Auth::routes();
Auth::routes([
    'register' => false // ユーザ登録機能をオフに切替
]);


// 認証によるアクセスの制限が行われる
Route::group(['middleware' => ['auth']], function() {
    Route::get('/', [HomeController::class, 'showLogin']);
});



// Route::prefix('login')->group(function (){
//     Route::get('/', [AdminController::class, 'store'])->name('admin.store');
// });

// Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
// Route::post('login', 'Auth\LoginController@login');
// Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// 管理者ページ
Route::prefix('admin')->group(function (){
    Route::get('/store', [AdminController::class, 'store'])->name('admin.store');
});

// オーナーページ
Route::prefix('dashboard')->group(function (){
    Route::get('/schedule', [OwnerController::class, 'getTemplateOverview'])->name('owner.schedule');
    Route::get('/lineusers', [OwnerController::class, 'line_users'])->name('owner.line_users');
    // Route::post('/store/add', [OwnerController::class, 'add_store'])->name('owner.store.add');

    // Route::get('/member', [OwnerController::class, 'member'])->name('owner.member');
    // Route::post('/schedule', [OwnerController::class, 'schedule'])->name('owner.schedule');
    Route::post('/send', [OwnerController::class, 'send'])->name('owner.send');


    Route::get('/template-get', [ScheduleController::class, 'getTemplateDetail'])->name('template.get');
    Route::post('/message-add', [ScheduleController::class, 'insertTemplate'])->name('template.add');
    Route::patch('/template-edit', [ScheduleController::class, 'updateTemplate'])->name('template.edit');

    
    Route::get('/schedule-get', [ScheduleController::class, 'getSchedule'])->name('schedule.get');
    Route::post('/schedule-add', [ScheduleController::class, 'insertSchedule'])->name('schedule.add');
    Route::post('/schedule-edit', [ScheduleController::class, 'updateSchedule'])->name('schedule.edit');
    // Route::get('/eventinfo', [ScheduleController::class, 'getEventInfo'])->name('owner.getEventInfo');
    // Route::post('/createSchedule', [ScheduleController::class, 'createSchedule'])->name('schedule.createSchedule');
});



// 一般利用者登録ページ
Route::prefix('{url_name}')->group(function (){
    Route::get('/register', [LineNotifyController::class, 'register']);

    Route::get('/auth', [LineNotifyController::class, 'auth'])->name('notify.auth');
    Route::post('/callback', [LineNotifyController::class, 'auth_callback']);

    Route::post('/send', [LineNotifyController::class, 'send']);
    Route::get('/aaa', [LineNotifyController::class, 'broadcastSend']);
    Route::post('/image', [LineNotifyController::class, 'sendImage']);
});



// Route::prefix('notify')->group(function (){
//     Route::get('/register/{url_name}', [LineNotifyController::class, 'register']);

//     Route::get('/auth', [LineNotifyController::class, 'auth'])->name('notify.auth');
//     Route::post('/callback/{url_name}', [LineNotifyController::class, 'auth_callback']);

//     Route::post('/send', [LineNotifyController::class, 'send']);
//     Route::get('/aaa', [LineNotifyController::class, 'broadcastSend']);
//     Route::post('/image', [LineNotifyController::class, 'sendImage']);
// });



