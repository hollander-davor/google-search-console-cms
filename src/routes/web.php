<?php

/**
 * URLs - always kebab-cased ( http://wiki.c2.com/?KebabCase ), always begining with a '/' 
 *      - order of words in a route goes as follows: 
 *          #1 name of entity in plural (ex. "entities")
 *          #2 (optional) {entity} (if action is related to one entity) this parameter is the primary key of entity model
 *          #3 action name
 * 
 *      - entity's ID is always represented as {entity}, because of policies applied in controller
 * Names - underscore case, always
 * 
 * Grouping - by controller - minimum. In each group, a url prefix & name prefix 
 * are a must, the namspace prefix is optional if the controller is in a 
 * sub-folder.
 * 
 * example:
 */
Auth::routes();

Route::middleware(['auth'])->group(function () {
/**
     * Google Search Console
     */
    Route::middleware('can:gsc-cms')->name('google_search_console.')->prefix('/google-search-console')->group(function(){
        Route::get('/{activeWebsite?}', 'GoogleSearchConsoleController@index')->name('index');
        Route::post('/datatable', 'GoogleSearchConsoleController@datatable')->name('datatable');
        Route::post('/pages-datatable', 'GoogleSearchConsoleController@pagesDatatable')->name('pages_datatable');
        Route::post('/exclude/{query}', 'GoogleSearchConsoleController@exclude')->name('exclude');
        Route::get('/pages/{query}', 'GoogleSearchConsoleController@pages')->name('pages');
    });
});