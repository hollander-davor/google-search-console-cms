<?php

/**
     * Google Search Console
     */
Route::middleware(['auth'])->group(function () {
    Route::middleware('can:gsc-cms')->name('google_search_console.')->prefix('/google-search-console')->group(function(){
        Route::get('/{activeWebsite?}', 'Hoks\CMSGSC\Controllers\GoogleSearchConsoleController@index')->name('index');
        Route::post('/datatable', 'Hoks\CMSGSC\Controllers\GoogleSearchConsoleController@datatable')->name('datatable');
        Route::post('/pages-datatable', 'Hoks\CMSGSC\Controllers\GoogleSearchConsoleController@pagesDatatable')->name('pages_datatable');
        Route::post('/exclude/{query}', 'Hoks\CMSGSC\Controllers\GoogleSearchConsoleController@exclude')->name('exclude');
        Route::get('/pages/{query}', 'Hoks\CMSGSC\Controllers\GoogleSearchConsoleController@pages')->name('pages');
    });
});