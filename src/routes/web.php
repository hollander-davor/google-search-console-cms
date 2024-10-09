<?php
use Hoks\CMSGSC\Controllers\GoogleSearchConsoleController;

/**
     * Google Search Console
     */
    Route::middleware('can:gsc-cms')->name('google_search_console.')->prefix('/google-search-console')->group(function(){
        Route::get('/{activeWebsite?}', [GoogleSearchConsoleController::class,'index'])->name('index');
        Route::post('/datatable', [GoogleSearchConsoleController::class,'datatable'])->name('datatable');
        Route::post('/pages-datatable', [GoogleSearchConsoleController::class,'pagesDatatable'])->name('pages_datatable');
        Route::post('/exclude/{query}', [GoogleSearchConsoleController::class,'exclude'])->name('exclude');
        Route::get('/pages/{query}', [GoogleSearchConsoleController::class,'pages'])->name('pages');
    });