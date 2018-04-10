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

    // Topic
    Route::group(
        [
            'prefix' => 'topic',
            //'middleware' => ['admin_permission'],
     
            //'roles' => [700]
        ], function(){

        
        Route::match(['get', 'post'], '/', ['uses' => 'TopicController@getTopic'])->name('topic');

        Route::match(['get', 'post'], 'edit/{id?}', ['uses' => 'TopicController@getTopic'])->name('topic-edit');

        Route::post('/create', ['uses' => 'TopicController@postCreateTopic'])->name('topic-post-create');
 
        Route::put('/edit/{id}', ['uses' => 'TopicController@putEditTopic'])->name('topic-put-edit');

        Route::get('/dimension', ['uses' => 'TopicController@getDimension'])->name('topic-dimension');
        
        Route::get('/dimension-edit/{id?}', ['uses' => 'TopicController@getDimension'])->name('topic-dimension-edit');

        Route::get('/dimension-mount', ['uses' => 'TopicController@getMountDimension'])->name('topic-dimension-mount');

        Route::post('/dimension-mount', ['uses' => 'TopicController@postMountDimension'])->name('topic-dimension-post-mount');

        Route::post('/dimension-create', ['uses' => 'TopicController@postCreateDimension'])->name('topic-dimension-post-create');

        Route::put('/dimension-edit', ['uses' => 'TopicController@postEditDimension'])->name('topic-dimension-put-edit');

        Route::get('/ledger-mount', ['uses' => 'TopicController@getMountLedger'])->name('ledger-mount');

        Route::post('/ledger-mount', ['uses' => 'TopicController@postMountLedger'])->name('ledger-post-mount');
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

        Route::get('/status/{id}', ['uses' => 'AccountController@getChangeStatus'])->name('account-get-status');

        Route::get('/dimension', ['uses' => 'AccountController@getDimension'])->name('account-dimension');
        
        Route::get('/dimension-edit/{id?}', ['uses' => 'AccountController@getDimension'])->name('account-dimension-edit');

        Route::get('/dimension-mount', ['uses' => 'AccountController@getMountDimension'])->name('account-dimension-mount');

        Route::post('/dimension-mount', ['uses' => 'AccountController@postMountDimension'])->name('account-dimension-post-mount');

        Route::post('/dimension-create', ['uses' => 'AccountController@postCreateDimension'])->name('account-dimension-post-create');

        Route::put('/dimension-edit', ['uses' => 'AccountController@postEditDimension'])->name('account-dimension-put-edit');


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

        Route::get('/status/{id}', ['uses' => 'DimensionController@getChangeStatus'])->name('dimension-get-status');


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

    // Setting
    Route::group(
        [
            'prefix' => 'setting',
            //'middleware' => ['admin_permission'],
            
            //'roles' => [700]
        ], function(){

        
            Route::get('/truncate', ['uses' => 'SettingController@getTruncateTable'])->name('truncate-table');
            Route::post('/truncate', ['uses' => 'SettingController@postTruncateTable'])->name('truncate-table-post');
       


    });

    

    // Route::get('setting/dimension', ['uses' => 'SettingController@getDimension']);

    // Route::get('setting/mappings-item', ['uses' => 'SettingController@getMappingsItem']);
});