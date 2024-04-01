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

// Route::prefix('productionapi')->group(function() {
//     Route::get('/', 'ProductionAPIController@index');
// });
Route::group(['middleware' => ['auth']], function () {
 Route::get('patient-registration', 'PatientRegistrationController@index')->name('patient-registration');
 Route::get('send-patient-registration', 'PatientRegistrationController@register')->name('send-patient-registration');
 Route::get('get-patient-registration', 'PatientRegistrationController@GetCount')->name('get-patient-registration');

//  BP

Route::get('patient-bp', 'PatientBpController@index')->name('patient-bp');
Route::get('get-patient-bp', 'PatientBpController@GetCount')->name('get-patient-bp');
Route::get('send-patient-bp', 'PatientBpController@register')->name('send-patient-bp');

//BS
Route::get('patient-bs', 'PatientBsController@index')->name('patient-bs');
Route::get('get-patient-bs', 'PatientBsController@GetCount')->name('get-patient-bs');
Route::get('send-patient-bs', 'PatientBsController@register')->name('send-patient-bs');

// Medicine
Route::get('patient-medicine', 'PatientMedicineController@index')->name('patient-medicine');
Route::get('get-patient-medicine', 'PatientMedicineController@GetCount')->name('get-patient-medicine');
Route::get('count-patient-medicine', 'PatientMedicineController@GetRowCount')->name('count-patient-medicine');
Route::get('send-patient-medicine', 'PatientMedicineController@register')->name('send-patient-medicine');

// Scheduled Status
Route::get('patient-visited', 'PatientAppointmentController@index')->name('patient-visited');
Route::get('get-patient-visited', 'PatientAppointmentController@GetCount')->name('get-patient-visited');
Route::get('send-patient-visited', 'PatientAppointmentController@register')->name('send-patient-visited');

// Condition
Route::get('patient-condition', 'PatientConditionController@index')->name('patient-condition');
Route::get('get-patient-condition', 'PatientConditionController@GetCount')->name('get-patient-condition');
Route::get('send-patient-condition', 'PatientConditionController@register')->name('send-patient-condition');
});
