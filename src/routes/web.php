<?php
use Hoks\CMSGSC\Controllers\GoogleSearchConsoleController;
use Hoks\CMSGSC\Controllers\GoogleSearchConsoleUserController;

/**
     * Google Search Console
     */
    Route::middleware('can:gsc-cms')->name('google_search_console.')->prefix('/google-search-console')->group(function () {
        Route::get('/{activeWebsite?}', [GoogleSearchConsoleController::class, 'index'])->name('index');
        Route::post('/datatable', [GoogleSearchConsoleController::class, 'datatable'])->name('datatable');
        Route::post('/pages-datatable', [GoogleSearchConsoleController::class, 'pagesDatatable'])->name('pages_datatable');
        Route::post('/exclude/{query}', [GoogleSearchConsoleController::class, 'toggleExclude'])->name('toggle_exclude');
        Route::post('/mark-as-fixed/{query}', [GoogleSearchConsoleController::class, 'toggleFixed'])->name('toggle_fixed');
        Route::post('/delegate/{query}', [GoogleSearchConsoleController::class, 'toggleDelegated'])->name('toggle_delegate');
        Route::get('/pages/{query}', [GoogleSearchConsoleController::class, 'pages'])->name('pages');
        Route::post('/{status}/{query}', [GoogleSearchConsoleController::class, 'newStatus'])->name('new_status');
        Route::get('/get-users/list', [GoogleSearchConsoleController::class, 'getUsers'])->name('get_users');
        Route::post('/ajax-delegate', [GoogleSearchConsoleController::class, 'storeDelegate'])->name('store_delegate');
    });
    Route::middleware('can:gsc-user')->name('google_search_console_user.')->prefix('/google-search-console-user')->group(function () {
        Route::get('/{activeWebsite?}', [GoogleSearchConsoleUserController::class, 'index'])->name('index');
        Route::post('/datatable', [GoogleSearchConsoleUserController::class, 'datatable'])->name('datatable');
        Route::post('/pages-datatable', [GoogleSearchConsoleUserController::class, 'pagesDatatable'])->name('pages_datatable');
        Route::get('/pages/{query}', [GoogleSearchConsoleUserController::class, 'pages'])->name('pages');
        Route::post('/send-comment/{query}', [GoogleSearchConsoleController::class, 'sendComment'])->name('send_comment');
        Route::post('/ajax-store-comment', [GoogleSearchConsoleUserController::class, 'storeComment'])->name('store_comment');
        Route::get('ajax/ajax-check-new-queries', [GoogleSearchConsoleUserController::class, 'ajaxNewQueries'])->name('check_new_queries');
    });