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
    'register' => false // ユーザ登録機能をオフ
]);

Route::group(['middleware'=>['auth']], function() {
    Route::get('/', [HomeController::class,'viewLogin']);
});


// 管理者ページ
Route::group(['prefix' => 'admin','middleware' => ['auth','can:isAdmin']], function () {
    Route::get('/log-list', [AdminController::class, 'viewLog'])->name('admin.log');
    Route::get('/store', [AdminController::class, 'viewStore'])->name('admin.store');
    Route::get('/store-add', [AdminController::class, 'viewAddStore'])->name('store.add.view');
    Route::post('/store-add', [AdminController::class, 'insertStore'])->name('store.add');
    Route::get('/store-edit', [AdminController::class, 'viewEditStore'])->name('store.edit.view');
    Route::post('/store-edit', [AdminController::class, 'updateStore'])->name('store.edit');
    Route::post('/store-del', [AdminController::class, 'deleteStore'])->name('store.del');
});


// オーナーページ
Route::group(['prefix' => 'dashboard', 'middleware'=>['auth','can:isOwner']], function () {
    
    // 配信
    Route::get('/delivery', [OwnerController::class, 'viewDelivery'])->name('owner.delivery');
    

    // // スケジュール配信
    // Route::get('/schedule', [OwnerController::class, 'viewSchedule'])->name('owner.schedule');
    // Route::post('/schedule-get', [ScheduleController::class, 'getSchedule'])->name('schedule.get');    
    // Route::post('/schedule-add', [ScheduleController::class, 'insertSchedule'])->name('schedule.add');
    // Route::post('/schedule-edit', [ScheduleController::class, 'updateSchedule'])->name('schedule.edit');
    // Route::post('/schedule-del', [ScheduleController::class, 'deleteSchedule'])->name('schedule.del');

    Route::post('/post', [ScheduleController::class, 'postMessage'])->name('post');

    // 連携LINE友だち一覧
    Route::get('/line-users', [OwnerController::class, 'viewLineUsers'])->name('owner.line_users');
    Route::post('/line-users-edit', [OwnerController::class, 'updateLineUser'])->name('line_users.edit');
    Route::get('/line-users-upd-status', [OwnerController::class, 'updateStatusLineUser'])->name('line_users.upd.status');


    // 配信履歴一覧
    Route::get('/history', [OwnerController::class, 'viewPostHistory'])->name('owner.history');
    Route::get('/history-info', [OwnerController::class, 'viewPostHistoryInfo'])->name('owner.history.info');

    Route::get('/template-get', [ScheduleController::class, 'getTemplateDetail'])->name('template.get');
    Route::post('/message-add', [ScheduleController::class, 'insertTemplate'])->name('template.add');
    Route::patch('/template-edit', [ScheduleController::class, 'updateTemplate'])->name('template.edit');
    Route::post('/template-del', [ScheduleController::class, 'deleteTemplate'])->name('template.del');


    // Route::get('/greeting', [OwnerController::class, 'viewGreeting'])->name('owner.greeting');
    // Route::post('/greeting-link-edit', [OwnerController::class, 'updateLinkGreeting'])->name('greeting.link.edit');

    // Route::get('/graph', [OwnerController::class, 'viewGraph'])->name('owner.graph');
    // Route::get('/graph-change-friend', [OwnerController::class, 'changeFriendGraph'])->name('owner.graph.change.friend');

    // Route::get('/movie', [OwnerController::class, 'viewMovie'])->name('owner.movie');
    // Route::post('/movie-add', [OwnerController::class, 'insertMovie'])->name('owner.movie.add');
});


// 一般利用者登録ページ
Route::group(['prefix'=>'{url_name}'], function () {
    Route::get('/entry', [LineNotifyController::class, 'entry'])->name('line.entry');
    Route::get('/auth', [LineNotifyController::class, 'viewLineAuth'])->name('line.auth');
    Route::post('/callback', [LineNotifyController::class, 'auth_callback']);
});
