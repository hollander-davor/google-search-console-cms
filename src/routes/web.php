<?php

/**
     * Google Search Console
     */
Route::middleware(['auth'])->group(function () {
    Route::middleware('can:gsc-cms')->name('google_search_console.')->prefix('/google-search-console')->group(function(){
        Route::get('/{activeWebsite?}', 'GoogleSearchConsoleController@index')->name('index');
        Route::post('/datatable', 'GoogleSearchConsoleController@datatable')->name('datatable');
        Route::post('/pages-datatable', 'GoogleSearchConsoleController@pagesDatatable')->name('pages_datatable');
        Route::post('/exclude/{query}', 'GoogleSearchConsoleController@exclude')->name('exclude');
        Route::get('/pages/{query}', 'GoogleSearchConsoleController@pages')->name('pages');
    });
});