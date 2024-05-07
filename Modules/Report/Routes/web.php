<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
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
    Route::get('patientage', 'ReportController@index')->name('patientage');
    Route::get('datewisedx', 'ReportController@datewisedxindex')->name('datewisedx');
    Route::any('search-by-age', 'ReportController@SearchByAge')->name('search-by-age');
    Route::any('date-wise-dx', 'ReportController@SearchByDate')->name('date-wise-dx');
    Route::any('glucosegraph', 'ReportController@glucosegraphindex')->name('glucosegraph');
    Route::any('glucose-graph', 'ReportController@GlucoseGraph')->name('glucose-graph');
    Route::get('diseasechart', 'ReportController@diseaseindex')->name('diseasechart');

    Route::get('patient-blood-pressure-graph', 'ReportController@PatientBloodPressureGraph')->name('patientbloodpressuregraph');
    Route::get('ajax-patient-blood-pressure', 'ReportController@AjaxPatientBloodPressure')->name('ajaxpatientbloodpressure');

    Route::get('heart-rate-graph','ReportController@HeartRateGraph')->name('heartrategraph');
    Route::get('ajax-heart-rate-graph','ReportController@AjaxHeartRateGraph')->name('ajaxheartrategraph');

    Route::get('temperature-graph','ReportController@TemperatureGraph')->name('temperaturegraph');
    Route::get('ajax-temperature-graph','ReportController@AjaxTemperatureGraph')->name('ajaxtemperaturegraph');
    Route::get('disease-wise-patient','ReportController@DiseaseWisePatient')->name('diseasewisepatient');
    Route::any('diseasewise-patient', 'ReportController@SearchByDisease')->name('diseasewise-patient');

    Route::get('hc-wise-referral','ReportController@HCWiseReferral')->name('hcwisereferral');
    Route::any('hcwise-referral', 'ReportController@SearchByHC')->name('hcwise-referral');

    Route::get('top-ten-diseases','ReportController@TopTenDiseases')->name('toptendiseases');
    Route::get('ajax-top-ten-diseases', 'ReportController@AjaxTopTenDiseases')->name('ajaxtoptendiseases');

    Route::any('branch-wise-patients','ReportController@branchWisePatients')->name('branch-wise-patients');

    Route::any('disease-rate-date-range','ReportController@DiseaseRateDateRange')->name('diseaseRateDateRange');
    Route::get('ajax-disease-rate-date-range','ReportController@AjaxDiseaseRateDateRange')->name('AjaxDiseaseRateDateRange');

    Route::get('districtwise-patients','ReportController@DistrictwisePatientIndex')->name('districtwise-patients');
    Route::any('get-districtwise-patients','ReportController@GetDistrictwisePatient')->name('get-districtwise-patients');
    Route::get('get-upazillas/{dcId}','ReportController@GetUpazillas')->name('get-upazillas');
    Route::get('get-unions/{upId}','ReportController@GetUnions')->name('get-unions');

    Route::get('get-patients/{hc_id}','ReportController@GetPatients')->name('get-patients');




    Route::any('followupdate','ReportController@AjaxFupDate')->name('ajax-fupdate');
    Route::any('followupdate-report','ReportController@Ajaxfupdatereport')->name('Ajax-followupdate-report');

    Route::get('provisionaldx', 'ReportController@patientwisedxindex')->name('provisionaldx');
    Route::get('patient-wise-dx', 'ReportController@patientwisedxreport')->name('patient-wise-dx');

    Route::get('agewisedx', 'ReportController@patientagewisedxindex')->name('agewisedx');
    Route::get('patientcountagewise', 'ReportController@PatientCountAgeWise')->name('patientcountagewise');
    Route::get('agewisedxreport', 'ReportController@PatientagewisedxReport')->name('agewisedxreport');

    Route::get('treatment', 'ReportController@treatmentindex')->name('treatment');
    Route::get('treatment-report', 'ReportController@treatmentreport')->name('treatment-report');


    Route::get('customreport', 'ReportController@CustomReport')->name('customreport');
    Route::get('custom-report', 'ReportController@CustomReportData')->name('custom-report');


    Route::get('fulldatadump', 'ReportController@FullDataDump')->name('fulldatadump');
    Route::get('fulldatadumpreport', 'ReportController@FullDataDumpReport')->name('fulldatadumpreport');

    Route::get('fulldataexport', 'ReportController@FullDataExport')->name('fulldataexport');
    Route::get('fulldataexportreport', 'ReportController@FullDataExportReport')->name('fulldataexportreport');

    Route::get('hypertension', 'ReportController@HyperTension')->name('hypertension');
    Route::get('hypertension-report', 'ReportController@HyperTensionReport')->name('hypertension-report');

    Route::get('diabatesstatus', 'ReportController@DiabatesStatus')->name('diabatesstatus');
    Route::get('diabatesstatus-report', 'ReportController@DiabatesStatusReport')->name('diabatesstatus-report');

    
    // Route::get('hypertension-report', 'ReportController@HyperTensionReport')->name('hypertension-report');
    Route::get('hcanalysis', 'ReportController@hcanalysisindex')->name('hcanalysis');
    Route::get('hcanalysis-report', 'ReportController@hcanalysisreport')->name('hcanalysis-report');

    Route::get('chief-complains', 'ReportController@GetAllComplain')->name('chiefcomplains');
    Route::get('all-illnesses', 'ReportController@GetAllIllness')->name('allillnesses');
    Route::get('all-medicines', 'ReportController@GetAllMedicines')->name('allmedicines');
    Route::get('all-prodxs', 'ReportController@GetAllProDxs')->name('allprodxs');
    Route::get('all-genders', 'ReportController@GetAllGenders')->name('allgenders');

    Route::get('simple-patient', 'ReportController@GetSimplePatientList')->name('simplepatient');
    


    Route::group(['prefix' => 'patientage', 'as'=>'patientage.'], function () {
        Route::post('show', 'ReportController@show')->name('show');
    });
    Route::any('/serve', function () {
       Artisan::call('execute:master-batch');
    });
    Route::any('data-sync','ReportController@SyncDatabase')->name('executeBatchFile');
    Route::any('data-sync-perform','ReportController@SyncDatabasePerform');
});
