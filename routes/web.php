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


Route::group(['middleware'=>['auth']], function() {
    Route::get('/', [HomeController::class,'viewLogin']);
});


// 管理者ページ
Route::group(['prefix' => 'admin','middleware' => ['auth','can:isAdmin']], function () {
    Route::get('/store', [AdminController::class, 'viewStore'])->name('admin.store');
    Route::get('/store-add', [AdminController::class, 'viewAddStore'])->name('store.add.view');
    Route::post('/store-add', [AdminController::class, 'insertStore'])->name('store.add');
    Route::get('/store-edit', [AdminController::class, 'viewEditStore'])->name('store.edit.view');
    Route::post('/store-edit', [AdminController::class, 'updateStore'])->name('store.edit');
    Route::post('/store-del', [AdminController::class, 'deleteStore'])->name('store.del');
});


// オーナーページ
Route::group(['prefix' => 'dashboard', 'middleware'=>['auth','can:isOwner']], function () {
    Route::get('/schedule', [OwnerController::class, 'getTemplateOverview'])->name('owner.schedule');
    Route::get('/line-users', [OwnerController::class, 'viewLineUsers'])->name('owner.line_users');

    Route::get('/history', [OwnerController::class, 'viewPostHistory'])->name('owner.history');

    Route::get('/template-get', [ScheduleController::class, 'getTemplateDetail'])->name('template.get');
    Route::post('/message-add', [ScheduleController::class, 'insertTemplate'])->name('template.add');
    Route::patch('/template-edit', [ScheduleController::class, 'updateTemplate'])->name('template.edit');
    Route::post('/template-del', [ScheduleController::class, 'deleteTemplate'])->name('template.del');

    Route::get('/schedule-get', [ScheduleController::class, 'getSchedule'])->name('schedule.get');
    Route::post('/schedule-add', [ScheduleController::class, 'insertSchedule'])->name('schedule.add');
    Route::post('/schedule-edit', [ScheduleController::class, 'updateSchedule'])->name('schedule.edit');
    
    Route::post('/post', [ScheduleController::class, 'postMessage'])->name('post');

    Route::get('/testpost', [ScheduleController::class, 'testPost'])->name('testPost');
});


// 一般利用者登録ページ
Route::group(['prefix'=>'{url_name}'], function () {
    Route::get('/register', [LineNotifyController::class, 'register'])->name('line.register');
    Route::get('/auth', [LineNotifyController::class, 'viewLineAuth'])->name('line.auth');
    Route::post('/callback', [LineNotifyController::class, 'auth_callback']);
    Route::post('/send', [LineNotifyController::class, 'send']);
    Route::get('/aaa', [LineNotifyController::class, 'broadcastSend']);
    Route::post('/image', [LineNotifyController::class, 'sendImage']);
});



// // オーナーページ
// Route::prefix('dashboard')->group(function (){
//     Route::get('/schedule', [OwnerController::class, 'getTemplateOverview'])->name('owner.schedule');
//     Route::get('/lineusers', [OwnerController::class, 'line_users'])->name('owner.line_users');
//     // Route::post('/store/add', [OwnerController::class, 'add_store'])->name('owner.store.add');

//     // Route::get('/member', [OwnerController::class, 'member'])->name('owner.member');
//     // Route::post('/schedule', [OwnerController::class, 'schedule'])->name('owner.schedule');
//     Route::post('/send', [OwnerController::class, 'send'])->name('owner.send');


//     Route::get('/template-get', [ScheduleController::class, 'getTemplateDetail'])->name('template.get');
//     Route::post('/message-add', [ScheduleController::class, 'insertTemplate'])->name('template.add');
//     Route::patch('/template-edit', [ScheduleController::class, 'updateTemplate'])->name('template.edit');

    
//     Route::get('/schedule-get', [ScheduleController::class, 'getSchedule'])->name('schedule.get');
//     Route::post('/schedule-add', [ScheduleController::class, 'insertSchedule'])->name('schedule.add');
//     Route::post('/schedule-edit', [ScheduleController::class, 'updateSchedule'])->name('schedule.edit');
//     // Route::get('/eventinfo', [ScheduleController::class, 'getEventInfo'])->name('owner.getEventInfo');
//     // Route::post('/createSchedule', [ScheduleController::class, 'createSchedule'])->name('schedule.createSchedule');
// });






// // 一般利用者登録ページ
// Route::prefix('{url_name}')->group(function (){
//     Route::get('/register', [LineNotifyController::class, 'register'])->name('line.register');
//     Route::get('/auth', [LineNotifyController::class, 'auth'])->name('notify.auth');
//     Route::post('/callback', [LineNotifyController::class, 'auth_callback']);

//     Route::post('/send', [LineNotifyController::class, 'send']);
//     Route::get('/aaa', [LineNotifyController::class, 'broadcastSend']);
//     Route::post('/image', [LineNotifyController::class, 'sendImage']);
// });



// Route::prefix('notify')->group(function (){
//     Route::get('/register/{url_name}', [LineNotifyController::class, 'register']);

//     Route::get('/auth', [LineNotifyController::class, 'auth'])->name('notify.auth');
//     Route::post('/callback/{url_name}', [LineNotifyController::class, 'auth_callback']);

//     Route::post('/send', [LineNotifyController::class, 'send']);
//     Route::get('/aaa', [LineNotifyController::class, 'broadcastSend']);
//     Route::post('/image', [LineNotifyController::class, 'sendImage']);
// });



