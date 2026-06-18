<?php

Route::group([
    'middleware' => ['web', 'auth'],
    'prefix' => \Helper::getSubdirectory(),
    'namespace' => 'Modules\GlobalMailbox\Http\Controllers',
], function () {
    Route::get('/global', 'GlobalMailboxController@index')->name('globalmailbox.index');
});
