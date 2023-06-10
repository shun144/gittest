Route::group(['prefix' => 'notify'], function () {
    Route::get('/', 'LineNotifyController@index');
    Route::get('/auth', 'LineNotifyController@redirectToProvider');
    Route::post('/callback', 'LineNotifyController@handleProviderCallback');
    Route::post('/send', 'LineNotifyController@send');
});