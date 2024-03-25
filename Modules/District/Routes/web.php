<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::group(['middleware' => ['auth']], function () {
    Route::get('district', 'DistrictController@index')->name('district');
    Route::group(['prefix' => 'district', 'as'=>'district.'], function () {
        Route::post('datatable-data', 'DistrictController@get_datatable_data')->name('datatable.data');
        Route::post('store-or-update', 'DistrictController@store_or_update_data')->name('store.or.update');
        Route::post('edit', 'DistrictController@edit')->name('edit');
        Route::post('delete', 'DistrictController@delete')->name('delete');
        Route::post('bulk-delete', 'DistrictController@bulk_delete')->name('bulk.delete');
        Route::post('change-status', 'DistrictController@change_status')->name('change.status');
    });
});

