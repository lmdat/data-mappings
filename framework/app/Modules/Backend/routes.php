<?php

Route::group([
    'module' => 'Backend',
    'namespace'=>'App\Modules\Backend\Controllers',
    'middleware' => ['web']

], function(){
    Route::get('/', ['uses' => 'DashboardController@welcome']);

    // Mappings Item
    Route::group(
        [
            'prefix' => 'mappings-item',
            //'middleware' => ['admin_permission'],
            //'roles' => [700]
        ], function(){

        
        Route::match(['get', 'post'], '/', ['uses' => 'MappingsItemController@getItem'])->name('mappings-item');

        Route::match(['get', 'post'], 'edit/{id?}', ['uses' => 'MappingsItemController@getItem'])->name('mappings-item-edit');

        Route::post('/create', ['uses' => 'MappingsItemController@postCreateItem'])->name('mappings-item-post-create');
 
        Route::put('/edit/{id}', ['uses' => 'MappingsItemController@putEditItem'])->name('mappings-item-put-edit');

        Route::match(['get', 'post'], '/mount/{id?}', ['uses' => 'MappingsItemController@getMountAccount'])->name('account-mount');

        Route::post('/mount/{id?}', ['uses' => 'MappingsItemController@postMountAccountItem'])->name('account-post-mount');
        // Route::post('/ordering', ['uses' => 'CategoryController@postOrderingCategory']);

        // Route::get('/published/{id}', ['uses' => 'CategoryController@getPublishedCategory']);

    });

    // Account
    Route::group(
        [
            'prefix' => 'account',
            //'middleware' => ['admin_permission'],
            //'roles' => [700]
        ], function(){

        
        Route::match(['get', 'post'], '/', ['uses' => 'AccountController@getAccount'])->name('account');

        Route::match(['get', 'post'], 'edit/{id?}', ['uses' => 'AccountController@getAccount'])->name('account-edit');

        Route::post('/create', ['uses' => 'AccountController@postCreateAccount'])->name('account-post-create');
 
        Route::put('/edit/{id}', ['uses' => 'AccountController@putEditAccount'])->name('account-put-edit');

        


    });

    

    Route::get('setting/dimension', ['uses' => 'SettingController@getDimension']);

    Route::get('setting/mappings-item', ['uses' => 'SettingController@getMappingsItem']);
});