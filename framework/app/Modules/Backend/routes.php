<?php

Route::group([
    'module' => 'Backend',
    'namespace'=>'App\Modules\Backend\Controllers',
    'middleware' => ['web']

], function(){
    Route::get('/', ['uses' => 'DashboardController@welcome'])->name('dashboard');

    Route::get('/login', ['as' => 'backend-login', 'uses' => 'AuthController@getLogin']);

    Route::post('/login', ['as' => 'backend-post-login', 'uses' => 'AuthController@postLogin']);
    
    Route::get('/logout', ['as' => 'backend-logout', 'uses' => 'AuthController@getLogout']);

    //Admin User
    Route::group(
        [
            'prefix' => 'user',
            // 'middleware' => ['admin_permission'],
            // 'roles' => [700, 500]
            'skip_com_selection'=>true
        ], function(){

            Route::match(['get', 'post'], '/', ['uses' => 'UserController@getUser'])->name('user');

            Route::match(['get', 'post'], '/edit/{id}', ['uses' => 'UserController@getUser'])->name('user-edit');
            
            Route::match(['get', 'post'], '/assign-company/{id}', ['uses' => 'UserController@getAssignCompany'])->name('user-assign-company');

            Route::post('/create', ['uses' => 'UserController@postCreateUser'])->name('user-post-create');
 
            Route::put('/edit/{id?}', ['uses' => 'UserController@putEditUser'])->name('user-put-edit');

            Route::get('/status/{id}', ['uses' => 'UserController@getChangeStatus'])->name('user-get-status');

            Route::post('/assign-company/{id?}', ['uses' => 'UserController@postAssignCompany'])->name('user-post-assign');


            // Route::get('/create', ['uses' => 'AdminController@getUserForm']);
            // Route::post('/create', ['uses' => 'AdminController@postUserCreate']);

            // Route::get('/edit/{id}', ['uses' => 'AdminController@getUserForm']);
            // Route::put('/edit/{id}', ['uses' => 'AdminController@putUserEdit']);

            // Route::get('/delete/{id}', ['uses' => 'AdminController@getDelete']);
    });

    //Company
    Route::group(
        [
            'prefix' => 'company',
            // 'middleware' => ['admin_permission'],
            // 'roles' => [700, 500]
        ], function(){

            Route::match(['get', 'post'], '/', ['uses' => 'CompanyController@getCompany'])->name('company');

            Route::match(['get', 'post'], '/edit/{id}', ['uses' => 'CompanyController@getCompany'])->name('company-edit');
            
            // Route::match(['get', 'post'], '/assign-user/{id}', ['uses' => 'CompanyController@getAssignUser'])->name('user-assign-company');

            Route::post('/create', ['uses' => 'CompanyController@postCreateCompany'])->name('company-post-create');
 
            Route::put('/edit/{id?}', ['uses' => 'CompanyController@putEditCompany'])->name('company-put-edit');

            Route::get('/status/{id}', ['uses' => 'CompanyController@getChangeStatus'])->name('company-get-status');

            Route::get('/select', ['skip_com_selection'=>true, 'uses' => 'CompanyController@getSelectCompany'])->name('company-get-select');

            Route::post('/select', ['skip_com_selection'=>true, 'uses' => 'CompanyController@postSelectCompany'])->name('company-post-select');

            // Route::post('/assign-company/{id?}', ['uses' => 'UserController@postAssignCompany'])->name('user-post-assign');


           
    });

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

        Route::get('/mount', ['uses' => 'MappingsItemController@getMountAccount'])->name('ledger-mount');

        Route::post('/mount', ['uses' => 'MappingsItemController@postMountAccountItem'])->name('ledger-post-mount');
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

    // Dimension
    Route::group(
        [
            'prefix' => 'dimension',
            //'middleware' => ['admin_permission'],
      
            //'roles' => [700]
        ], function(){

        
        Route::match(['get', 'post'], '/', ['uses' => 'DimensionController@getDimension'])->name('dimension');

        Route::match(['get', 'post'], 'edit/{id?}', ['uses' => 'DimensionController@getDimension'])->name('dimension-edit');

        Route::post('/create', ['uses' => 'DimensionController@postCreateDimension'])->name('dimension-post-create');
 
        Route::put('/edit/{id}', ['uses' => 'DimensionController@putEditDimension'])->name('dimension-put-edit');

        


    });

    // Ledger
    Route::group(
        [
            'prefix' => 'ledger',
            //'middleware' => ['admin_permission'],
            
            //'roles' => [700]
        ], function(){

        
        Route::match(['get', 'post'], '/', ['uses' => 'LedgerController@getLedger'])->name('ledger');
        
        Route::get('/import/{step?}', ['uses' => 'LedgerController@getImportLedger'])->name('import-ledger');

        Route::post('/import', ['uses' => 'LedgerController@postImportLedger'])->name('ledger-post-import');

        Route::match(['get', 'post'], '/revision', ['uses' => 'LedgerController@getRevision'])->name('revision');

        Route::get('/revision/delete/{id}', ['uses' => 'LedgerController@getDeleteRevision'])->name('revision-delete');

        Route::get('/revision/download/{id}', ['uses' => 'LedgerController@getDownloadRevisionFile'])->name('revision-download');
       


    });

    

    // Route::get('setting/dimension', ['uses' => 'SettingController@getDimension']);

    // Route::get('setting/mappings-item', ['uses' => 'SettingController@getMappingsItem']);
});