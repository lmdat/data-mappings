<?php


Route::post('/login', ['uses' => 'AuthController@postLogin']);
Route::post('/logout', ['uses' => 'AuthController@postLogout']);
Route::post('/refresh', ['uses' => 'AuthController@postRefresh']);
Route::post('/payload', ['uses' => 'AuthController@postPayload']);

// API
Route::group(
    [
        'prefix' => 'fetch',
        'middleware' => ['jwt.token-auth'],
    ], function(){

        Route::post('/ledgers', ['uses' => 'V1Controller@getLedgers'])->name('get-ledgers');
});

