<?php

namespace Modules\Report\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\BarcodeFormat\Entities\BarcodeFormat;
use Modules\Patient\Entities\Patient;
use Modules\RefDiseaseGroups\Entities\RefDiseaseGroups;
use Modules\HealthCenter\Entities\HealthCenter;
use Modules\RefReferral\Entities\RefReferral;
use Modules\Report\Entities\BPHostFemale;
use Modules\Report\Entities\BPRohingyaFemale;
use Modules\Report\Entities\BPRohingyaMale;
use Modules\Report\Entities\DMHostFemale;
use Modules\Report\Entities\DMHostMale;
use Modules\Report\Entities\DMRohingyaFemale;
use Modules\Report\Entities\DMRohingyaMale;
use Modules\Report\Entities\GlucoseHostFemale;
use Modules\Report\Entities\GlucoseHostMale;
use Modules\Report\Entities\GlucoseRohingyaFemale;
use Modules\Report\Entities\GlucoseRohingyaMale;
use Modules\Report\Entities\HTNHostFemale;
use Modules\Report\Entities\HTNHostMale;
use Modules\Report\Entities\Htnmalehost;
use Modules\Report\Entities\Htnmalerohingya;
use Modules\Report\Entities\HTNRohingyaFemale;
use Modules\Report\Entities\HTNRohingyaMale;
use Modules\Report\Entities\MDataPatientObsGynae;
use Modules\Report\Entities\MDataPatientObsGynaeView;
use Modules\Report\Entities\PatientDandHTN;
use Modules\Report\Entities\Report;
use Modules\Report\Entities\District;
use Illuminate\Routing\Controller;
use Modules\Base\Http\Controllers\BaseController;
use Illuminate\Support\Str;
use Modules\Patient\Entities\Address;
use Modules\Report\Entities\SyncRecord;
use Modules\Report\Entities\Union;
use Modules\Report\Entities\ChiefComplain;
use Modules\Report\Entities\Upazilla;
use Modules\Report\Entities\SimplePatient;
use Modules\Report\Entities\FollowUpDate;
use Illuminate\Support\Facades\Log; 
use Modules\Report\Entities\ViewDumpData;
use Modules\Report\Entities\ViewExportData;
use Modules\RefIllness\Entities\RefIllness;
use Modules\RefDrug\Entities\RefDrug;
use Modules\RefProvisionalDiagnosis\Entities\RefProvisionalDiagnosis;
use Modules\RefGender\Entities\RefGender;
use Symfony\Component\Process\Process;
use App\Jobs\SyncJob; // Import the job class
use Illuminate\Support\Facades\Queue; // Import Queue facade

class ReportController extends BaseController
{
    public function __construct(Report $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        $this->setPageData('Patient Age Count Report','Patient Age Count Report','fas fa-th-list');
        return view('report::index');
    }
    public function datewisedxindex(){
        // $healthcenters=BarcodeFormat::with('healthCenter')->get();
        
         $loginPrefix = session('login_prefix');
        if($loginPrefix == 'admin'){
            $branches=BarcodeFormat::with('healthCenter')->get(); 
        }else{
            $branches=BarcodeFormat::with('healthCenter')->where('barcode_prefix', $loginPrefix)->get(); 
        }
        $this->setPageData('Provisional Diagnosis Datewise','Provisional Diagnosis Datewise','fas fa-th-list');
        return view('report::datewisedx',compact('branches'));
    }
    public function patientwisedxindex(){
        // $healthcenters=BarcodeFormat::with('healthCenter')->get();
        
       $loginPrefix = session('login_prefix');
        if($loginPrefix == 'admin'){
            $branches=BarcodeFormat::with('healthCenter')->get(); 
        }else{
            $branches=BarcodeFormat::with('healthCenter')->where('barcode_prefix', $loginPrefix)->get(); 
        }
        
        $this->setPageData('Provisional Diagnosis','Provisional Diagnosis','fas fa-th-list');
        return view('report::provisionaldx',compact('branches'));
    }
    
public function diseaseindex()
    {
        $this->setPageData('Patient Age Count Report','Patient Age Count Report','fas fa-th-list');
        return view('report::diseasechart');
    }
    public function glucosegraphindex(){
        $loginPrefix = session('login_prefix');
        if($loginPrefix == 'admin'){
            $branches=BarcodeFormat::with('healthCenter')->get(); 
        }else{
            $branches=BarcodeFormat::with('healthCenter')->where('barcode_prefix', $loginPrefix)->get(); 
        }
          
       
        $this->setPageData('Diabetes Mellitus','Diabetes Mellitus','fas fa-th-list');
        return view('report::glucosegraph',compact('branches'));

    }
     public function DiseaseWisePatient(){
        $healthcenters=HealthCenter::get(['HealthCenterId','HealthCenterName']);
        $refcases=RefReferral::get(['RId','Description']);
        $this->setPageData('Number of Patient By Disease','Number of Patient By Disease','fas fa-th-list');
        return view('report::diseasewisepatient',compact('healthcenters','refcases'));

    }
     public function patientagewisedxindex(){
        $loginPrefix = session('login_prefix');
        if($loginPrefix == 'admin'){
            $branches=BarcodeFormat::with('healthCenter')->get(); 
        }else{
            $branches=BarcodeFormat::with('healthCenter')->where('barcode_prefix', $loginPrefix)->get(); 
        }
        
        $this->setPageData('Provisional Diagnosis Age wise','Provisional Diagnosis Age wise','fas fa-th-list');
        return view('report::agewisedx',compact('branches'));

    }
       public function PatientCountAgeWise(){
        $loginPrefix = session('login_prefix');
        if($loginPrefix == 'admin'){
            $branches=BarcodeFormat::with('healthCenter')->get(); 
        }else{
            $branches=BarcodeFormat::with('healthCenter')->where('barcode_prefix', $loginPrefix)->get(); 
        } 
        $this->setPageData('Provisional Diagnosis Patient Count Age wise','Provisional Diagnosis Patient Count Age wise','fas fa-th-list');
        return view('report::patientcountagewise',compact('branches'));

    }

    
      public function HCWiseReferral(){
        $healthcenters=HealthCenter::get(['HealthCenterId','HealthCenterName']);
        $refcases=RefReferral::get(['RId','Description']);
        $this->setPageData('Referrad Cases','Referrad Cases','fas fa-th-list');
        return view('report::hcwisereferral',compact('healthcenters','refcases'));

    }
     public function TopTenDiseases(){
        $healthcenters=BarcodeFormat::with('healthCenter')->get();
        $this->setPageData('Chart of Diseases by Branch','Chart of Diseases by Branch','fas fa-th-list');

        return view('report::toptendiseases',compact('healthcenters'));

    }
      public function DistrictwisePatientIndex(Request $request){
        $districts=District::get(['id','name']);
        $this->setPageData('Districtwise Patients','Districtwise Patients','fas fa-th-list');
        return view('report::districtwisepatients',compact('districts'));

    }
     public function FollowUpDate(Request $request){
       $branches=BarcodeFormat::with('healthCenter')->get(); 
        $this->setPageData('Follow Up Date','Follow Up Date','fa fa-medical');
        return view('report::followupdate_index',compact('branches'));

    }

    public function treatmentindex(){
        // $healthcenters=BarcodeFormat::with('healthCenter')->get();
        
       $branches=BarcodeFormat::with('healthCenter')->get(); 
        $this->setPageData('Treatment Suggestions','Treatment Suggestions','fas fa-th-list');
        return view('report::treatment',compact('branches'));
    }
     public function CustomReport(){
        // $healthcenters=BarcodeFormat::with('healthCenter')->get();
        
        $loginPrefix = session('login_prefix');
        if($loginPrefix == 'admin'){
            $branches=BarcodeFormat::with('healthCenter')->get(); 
        }else{
            $branches=BarcodeFormat::with('healthCenter')->where('barcode_prefix', $loginPrefix)->get(); 
        }
        $this->setPageData('Custom Report','Custom Report','fas fa-th-list');
        return view('report::customreport',compact('branches'));
    }
      public function FullDataDump(){
 
       $branches=BarcodeFormat::with('healthCenter')->get(); 
        $this->setPageData('Full Data Dump','Full Data Dump','fas fa-th-list');
        return view('report::fulldatadump',compact('branches'));
    }
     public function FullDataExport(){
 
       $branches=BarcodeFormat::with('healthCenter')->get(); 
        $this->setPageData('Full Data Export','Full Data Export','fas fa-th-list');
        return view('report::fulldataexport',compact('branches'));
    }
     public function HyperTension(){
 
       $branches=BarcodeFormat::with('healthCenter')->get(); 
        $this->setPageData('Number of Patients Diagnosed with HTN','Number of Patients Diagnosed with HTN','fas fa-th-list');
        return view('report::hypertension',compact('branches'));
    }
    public function DiabatesStatus(){
 
       $branches=BarcodeFormat::with('healthCenter')->get(); 
        $this->setPageData('Number of Patients Diagnosed with DM','Number of Patients Diagnosed with DM','fas fa-th-list');
        return view('report::diabatesstatus',compact('branches'));
    }

     public function hcanalysisindex(){
 
       $branches=BarcodeFormat::with('healthCenter')->get(); 
       $regs=Patient::get('RegistrationId');
       $genders = RefGender::get('GenderCode');
       $illnesses=RefIllness::get(['IllnessCode']);
    //    $drugs=RefDrug::get(['DrugCode']);
    //    $dxs=RefProvisionalDiagnosis::get(['ProvisionalDiagnosisName']);

        $this->setPageData('Report Analysis','Report Analysis','fas fa-th-list');
        return view('report::hcanalysis',compact('branches','regs','genders','illnesses'));
    }

  public function GetAllIllness(Request $request){
    $searchTerm = $request->input('search') ?? '';
    $illnesses = RefIllness::where('IllnessCode', 'like', '%' . $searchTerm . '%')
                              ->get()
                              ->map(function ($illnesse) {
                                  return ['id' => $illnesse->IllnessCode, 'text' => $illnesse->IllnessCode];
                              });

    return response()->json(['results' => $illnesses]);
}

  public function GetAllComplain(Request $request){
    $searchTerm = $request->input('search') ?? '';
    $complains = ChiefComplain::where('CCCode', 'like', '%' . $searchTerm . '%')
                              ->get()
                              ->map(function ($complain) {
                                  return ['id' => $complain->CCCode, 'text' => $complain->CCCode];
                              });

    return response()->json(['results' => $complains]);
}

  public function GetAllMedicines(Request $request){
    $searchTerm = $request->input('search') ?? '';
    $medicines = RefDrug::where('DrugCode', 'like', '%' . $searchTerm . '%')
                              ->get()
                              ->map(function ($medicine) {
                                  return ['id' => $medicine->DrugCode, 'text' => $medicine->DrugCode];
                              });

    return response()->json(['results' => $medicines]);
}

  public function GetAllProDxs(Request $request){
    $searchTerm = $request->input('search') ?? '';
    $prodxs = RefProvisionalDiagnosis::where('ProvisionalDiagnosisName', 'like', '%' . $searchTerm . '%')
                              ->get()
                              ->map(function ($prodx) {
                                  return ['id' => $prodx->ProvisionalDiagnosisName, 'text' => $prodx->ProvisionalDiagnosisName];
                              });

    return response()->json(['results' => $prodxs]);

    
}
  public function GetAllGenders(Request $request){
    $searchTerm = $request->input('search') ?? '';
    $genders = RefGender::where('GenderCode', 'like', '%' . $searchTerm . '%')
                              ->get()
                              ->map(function ($gender) {
                                  return ['id' => $gender->GenderCode, 'text' => $gender->GenderCode];
                              });

    return response()->json(['results' => $genders]);
    
    
}

public function GetSimplePatientList(){
      $patients=SimplePatient::get();   
       
        $this->setPageData('Simple Patient','Simple Patient','fas fa-th-list');
        return view('report::simplepatient',compact('patients'));
}
    public function hcanalysisreport(Request $request){
        $first_date = $request->fdate;
        $last_date = $request->ldate;
        $barcode_prefix = $request->hc_id;
        $starting_age = intval($request->starting_age);
        $ending_age = intval($request->ending_age);
        // dd( $starting_age,$ending_age);
       
        $hcname=HealthCenter::where('HealthCenterCode',$barcode_prefix )->first('HealthCenterName');
        $data_dump = ViewDumpData::whereBetween(DB::raw('CONVERT(date, CollectionDates)'), [$first_date, $last_date])
        ->where(function ($query) use ($barcode_prefix, $starting_age, $ending_age) {
            if ($barcode_prefix) {
                $query->where('RegistrationId', 'LIKE', $barcode_prefix . '%');
            }

            if ($starting_age !== null && $ending_age !== null) {
                // Filter records with ages within the specified range
                $query->whereRaw('CAST(Age AS BIGINT) BETWEEN ? AND ?', [$starting_age, $ending_age]);
            } elseif ($starting_age !== null) {
                // Filter records with ages greater than or equal to the starting age
                $query->whereRaw('CAST(Age AS BIGINT) >= ?', [$starting_age]);
            } elseif ($ending_age !== null) {
                // Filter records with ages less than or equal to the ending age
                $query->whereRaw('CAST(Age AS BIGINT) <= ?', [$ending_age]);
            }
        })->get();

        // $data_dump = ViewDumpData::whereBetween(DB::raw('CONVERT(date, CollectionDates)'), [$first_date, $last_date])
        //     ->where(function ($query) use ($barcode_prefix, $starting_age, $ending_age) {
        //         if ($barcode_prefix) {
        //             $query->where('RegistrationId', 'LIKE', $barcode_prefix . '%');
        //         }

        //         if ($starting_age && $ending_age) {
        //            $query->whereBetween(DB::raw('CAST(Age AS BIGINT)'), [$starting_age, $ending_age]);
        //         }
        //     })->get();

        return response()->json([
            'data_dump' => $data_dump,
            'healthcenter' => $hcname
        ]);

    }



      public function HyperTensionReport(Request $request){
 
       $barcode_prefix = $request->hc_id;
        $first_date = $request->fdate;
        $last_date = $request->ldate;

        if($barcode_prefix){
            $barcode_tbl = BarcodeFormat::with('healthCenter') 
                ->where('barcode_prefix',$barcode_prefix)
                ->first();
            $branchName = $barcode_tbl->HealthCenterName??'';
        }else{
            $branchName = 'ALL';
        }

                $illnesses = DB::table('MDataPatientIllnessHistory')
            ->select([
                DB::raw('COUNT(MDataPatientIllnessHistory.PatientId) as TotalPatientId'),
                DB::raw('CONVERT(date, MDataPatientIllnessHistory.CreateDate) as IllCreateDate'),
                DB::raw("
                    SUM(
                        CASE 
                            WHEN (
                                MaxBP.BPSystolic1 > 130 AND MaxBP.BPDiastolic1 > 80
                            ) OR (
                                MaxBP.BPSystolic2 > 130 AND MaxBP.BPDiastolic2 > 80
                            ) THEN 1
                            ELSE 0
                        END
                    ) as Uncontrolled
                "),
                DB::raw("
                    SUM(
                        CASE 
                            WHEN (
                                MaxBP.BPSystolic1 <= 130 AND MaxBP.BPDiastolic1 <= 80
                            ) OR (
                                MaxBP.BPSystolic2 <= 130 AND MaxBP.BPDiastolic2 <= 80
                            ) THEN 1
                            ELSE 0
                        END
                    ) as Controlled
                "),
            ])
            ->join('Patient', 'MDataPatientIllnessHistory.PatientId', '=', 'Patient.PatientId')
            ->leftJoin(DB::raw('(
                SELECT 
                    PatientId,
                    MAX(CAST(BPSystolic1 AS Float)) AS BPSystolic1,
                    MAX(CAST(BPSystolic2 AS Float)) AS BPSystolic2,
                    MAX(CAST(BPDiastolic1 AS Float)) AS BPDiastolic1,
                    MAX(CAST(BPDiastolic2 AS Float)) AS BPDiastolic2
                FROM MDataBP
                GROUP BY PatientId
            ) AS MaxBP'), 'MaxBP.PatientId', '=', 'MDataPatientIllnessHistory.PatientId')
            ->where('IllnessId', '=', 'A69382EF-905C-4FC1-BA32-53E86FC50E35')
            ->where('MDataPatientIllnessHistory.Status', 'LIKE', 'yes')
            ->whereBetween('MDataPatientIllnessHistory.CreateDate', [$first_date, $last_date])
            ->where(function($query) use ($barcode_prefix) {
            if ($barcode_prefix) {
                $query->where('RegistrationId', 'LIKE', $barcode_prefix . '%');
            }  
           })
            ->groupBy(DB::raw('CONVERT(date, MDataPatientIllnessHistory.CreateDate)'))
            ->get();



        $response = [
            'healthcenter' => $branchName,
            'data' => $illnesses,
        ];
        return response()->json($response);
    }

    public function DiabatesStatusReport(Request $request){
 
       $barcode_prefix = $request->hc_id;
        $first_date = $request->fdate;
        $last_date = $request->ldate;

        if($barcode_prefix){
            $barcode_tbl = BarcodeFormat::with('healthCenter') 
                ->where('barcode_prefix',$barcode_prefix)
                ->first();
            $branchName = $barcode_tbl->HealthCenterName??'';
        }else{
            $branchName = 'ALL';
        }

                $illnesses = DB::table('MDataPatientIllnessHistory')
            ->select([
                DB::raw('COUNT(DISTINCT MDataPatientIllnessHistory.PatientId) as TotalPatientId'),
                DB::raw('CONVERT(date, MDataPatientIllnessHistory.CreateDate) as IllCreateDate'),
                DB::raw("
                    SUM(
                        CASE 
                            WHEN (
                                MaxBP.FBG > 7 
                            ) THEN 1
                            ELSE 0
                        END
                    ) as Uncontrolled
                "),
                DB::raw("
                    SUM(
                        CASE 
                            WHEN (
                                MaxBP.FBG <= 7 AND MaxBP.FBG > 0
                            ) THEN 1
                            ELSE 0
                        END
                    ) as Controlled
                "),
            ])
            ->join('Patient', 'MDataPatientIllnessHistory.PatientId', '=', 'Patient.PatientId')
            ->leftJoin(DB::raw('(
                SELECT DISTINCT
                    PatientId,
                    MAX(CAST(FBG AS Float)) AS FBG
                
                FROM MDataGlucoseHb
                GROUP BY  PatientId
            ) AS MaxBP'), 'MaxBP.PatientId', '=', 'MDataPatientIllnessHistory.PatientId')
            ->where('IllnessId', '=', '81F1CECB-8B22-41D3-AE18-54CC2D79065F')
            ->where('MDataPatientIllnessHistory.Status', 'LIKE', 'yes')
            ->whereBetween('MDataPatientIllnessHistory.CreateDate', [$first_date, $last_date])
            ->where(function($query) use ($barcode_prefix) {
            if ($barcode_prefix) {
                $query->where('RegistrationId', 'LIKE', $barcode_prefix . '%');
            }  
           })
            ->groupBy(DB::raw('CONVERT(date, MDataPatientIllnessHistory.CreateDate)'))
            ->get();



        $response = [
            'healthcenter' => $branchName,
            'data' => $illnesses,
        ];
        return response()->json($response);
    }

    public function FullDataDumpReport(Request $request){
        $barcode_prefix = $request->hc_id;
        $first_date = $request->fdate;
        $last_date = $request->ldate;
        $hcname=HealthCenter::where('HealthCenterCode',$barcode_prefix )->first('HealthCenterName');
        $data_dump=ViewDumpData::whereBetween(DB::raw('CONVERT(date, CollectionDates)'), [$first_date, $last_date])
        ->where(function($query) use ($barcode_prefix) {
            if ($barcode_prefix) {
                $query->where('RegistrationId', 'LIKE', $barcode_prefix . '%');
            }  
        })->get(); 

        return response()->json([
            'data_dump'=>$data_dump,
            'healthcenter'=>$hcname
        ]);
      
    }
      public function FullDataExportReport(Request $request){
        $barcode_prefix = $request->hc_id;
        $first_date = $request->fdate;
        $last_date = $request->ldate;

        $hcname=HealthCenter::where('HealthCenterCode',$barcode_prefix )->first('HealthCenterName');

        $data_dump=ViewExportData::whereBetween(DB::raw('CONVERT(date, CollectionDates)'), [$first_date, $last_date])
        ->where(function($query) use ($barcode_prefix) {
            if ($barcode_prefix) {
                $query->where('RegistrationId', 'LIKE', $barcode_prefix . '%');
            }  
        })->get(); 

        
        return response()->json([
            'data_dump'=>$data_dump,
            'healthcenter'=>$hcname
        ]);
      
    }
      public function CustomReportData(Request $request){
        // $healthcenters=BarcodeFormat::with('healthCenter')->get();
        $barcode_prefix = $request->hc_id;
        $first_date = $request->fdate;
        $last_date = $request->ldate;
        
        $cc_name = BarcodeFormat::with('healthCenter')->where('barcode_prefix', $barcode_prefix)->first();
        $bf=BarcodeFormat::where('barcode_prefix',$barcode_prefix)->with(['union','upazila'])->first();
        $union=$bf->union->name ?? '';
        $upazila=$bf->upazila->name ?? '';

 $total_diabetes = PatientDandHTN::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
    ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
        $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
            ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
            ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
            ->where('Diabetes', 'yes')
              ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
            ->from('PatientDandHTN');
    }, 'subquery')
    ->first()
    ->TotalDistinctPatientCount;

$total_htn = PatientDandHTN::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
    ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
        $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
            ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
            ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
            ->where('Hypertension', 'yes')
              ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
            ->from('PatientDandHTN');
    }, 'subquery')
    ->first()
    ->TotalDistinctPatientCount;


$pregnant = MDataPatientObsGynaeView::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
    ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
        $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
            ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
            ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
             ->where('IsPregnant', 'True')
              ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
            ->from('MDataPatientObsGynaeView');
    }, 'subquery')
    ->first()
    ->TotalDistinctPatientCount;


       
$total_bp_male_host = Htnmalehost::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
    ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
        $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
            ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
            ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
              ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
            ->from('BPHostMale');
    }, 'subquery')
    ->first()
    ->TotalDistinctPatientCount;




    $total_bp_male_rohingya = BPRohingyaMale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
    ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
        $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
            ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
            ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
              ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
            ->from('BPRohingyaMale');
    }, 'subquery')
    ->first()
    ->TotalDistinctPatientCount;

    $total_bp_female_host = BPHostFemale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
    ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
        $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
            ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
            ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
              ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
            ->from('BPHostFemale');
    }, 'subquery')
    ->first()
    ->TotalDistinctPatientCount;

    
    $total_bp_female_rohingya = BPRohingyaFemale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
    ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
        $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
            ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
            ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
              ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
            ->from('BPRohingyaFemale');
    }, 'subquery')
    ->first()
    ->TotalDistinctPatientCount;


    $total_htn_male_host = HTNHostMale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('HTNHostMale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;


     $total_htn_male_rohingya = HTNRohingyaMale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('HTNRohingyaMale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;


      $total_htn_female_host = HTNHostFemale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('HTNHostFemale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;

      $total_htn_female_rohingya = HTNRohingyaFemale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('HTNRohingyaFemale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;


       $total_glucose_male_host = GlucoseHostMale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('GlucoseHostMale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;

    
       $total_glucose_female_host = GlucoseHostFemale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('GlucoseHostFemale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;

      $total_glucose_male_rohingya = GlucoseRohingyaMale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('GlucoseRohingyaMale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;

    
      $total_glucose_female_rohingya = GlucoseRohingyaFemale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('GlucoseRohingyaFemale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;

      $total_dm_male_host = DMHostMale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('DMHostMale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount; 

      $total_dm_female_host = DMHostFemale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('DMHostFemale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;  

        $total_dm_male_rohingya = DMRohingyaMale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('DMRohingyaMale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;

     $total_dm_female_rohingya = DMRohingyaFemale::selectRaw('SUM(DistinctPatientCount) as TotalDistinctPatientCount')
        ->fromSub(function ($query) use ($first_date, $last_date, $barcode_prefix) {
            $query->selectRaw('CONVERT(date, CreateDate) as CreateDate, COUNT(DISTINCT PatientId) as DistinctPatientCount')
                ->whereBetween(DB::raw('CONVERT(date, CreateDate)'), [$first_date, $last_date])
                ->where('RegistrationId', 'LIKE', $barcode_prefix . '%')
                ->groupBy(DB::raw('CONVERT(date, CreateDate)'))// Group by the CreateDate column
                ->from('DMRohingyaFemale');
        }, 'subquery')
        ->first()
        ->TotalDistinctPatientCount;

    

    

        return response()->json([
            'total_bp_male_host' => $total_bp_male_host,
            'total_bp_male_rohingya' => $total_bp_male_rohingya,
            'total_bp_female_host' => $total_bp_female_host,
            'total_bp_female_rohingya' => $total_bp_female_rohingya,
            'total_htn_male_host' => $total_htn_male_host,
            'total_htn_male_rohingya' => $total_htn_male_rohingya,
            'total_htn_female_host' => $total_htn_female_host,
            'total_htn_female_rohingya' => $total_htn_female_rohingya,
            'total_glucose_male_host' => $total_glucose_male_host,
            'total_glucose_female_host' => $total_glucose_female_host,
            'total_glucose_male_rohingya' => $total_glucose_male_rohingya,
            'total_glucose_female_rohingya' => $total_glucose_female_rohingya,
            'total_dm_male_host' => $total_dm_male_host,
            'total_dm_male_rohingya' => $total_dm_male_rohingya,
            'total_dm_female_host' => $total_dm_female_host,
            'total_dm_female_rohingya' => $total_dm_female_rohingya,    
            'pregnant' => $pregnant,
            'total_htn' => $total_htn,
            'total_diabetes' => $total_diabetes,
            'cc_name' => $cc_name->healthCenter->HealthCenterName,
            'union' => $union,
            'upazila' => $upazila,
        
        ]);
      
      
    }

    


    public function FollowUpDateReport(Request $request)
    {
        $branches=BarcodeFormat::with('healthCenter')->get(); 
        $daterange = $request->daterange;
        $dates = explode(' - ', $daterange);
// Assign the start and end dates to separate variables
        $first_date = $dates[0]??'';
        $last_date = $dates[1]??'';
        $hc=$request->hc_id;
        $fupdates = DB::table('MDataFollowUpDate')
        ->select(
            'MDataFollowUpDate.MDFollowUpDateId',
            'MDataFollowUpDate.PatientId',
            'Patient.RegistrationId',
            'Patient.GivenName',
            'Patient.FamilyName',
            'Patient.BirthDate',
            'Patient.Age',
            'Patient.CellNumber',
            'RefGender.GenderCode',
            'MDataFollowUpDate.FollowUpDate',
            'MDataFollowUpDate.CreateDate',
           
        )
        ->whereDate('MDataFollowUpDate.CreateDate', '>=', $first_date)
        ->whereDate('MDataFollowUpDate.CreateDate', '<=', $last_date)
        ->where(function($query) use ($hc) {
            if ($hc) {
                $query->where('Patient.RegistrationId', 'LIKE', $hc . '%');
            }
        })
        ->join('Patient', 'MDataFollowUpDate.PatientId', '=', 'Patient.PatientId')
        ->join('RefGender', 'RefGender.GenderId', '=', 'Patient.GenderId')
        ->get();
       
        $resultCount = $fupdates->count();
         $hcname=HealthCenter::where('HealthCenterCode',$hc )->first('HealthCenterName');
     

        $this->setPageData('Follow Up Date','Follow Up Date','fa fa-medical');
        return view('report::followupdate_index',compact('branches','fupdates','resultCount','daterange','hcname'));	

    }
    

    public function AjaxFupDate(Request $request){
    //branches
        $loginPrefix = session('login_prefix');
        if($loginPrefix == 'admin'){
            $branches=BarcodeFormat::with('healthCenter')->get(); 
        }else{
            $branches=BarcodeFormat::with('healthCenter')->where('barcode_prefix', $loginPrefix)->get(); 
        }
        $this->setPageData(
        'Followup Date',
        'Followup Date',
        'fas fa-th-list'
    );
    return view('report::ajaxfollowupdateIndex',compact('branches'));
}
     public function Ajaxfupdatereport(Request $request)
    {
      
        $barcode_prefixes = $request->hc_ids;
        $first_date = $request->fdate;
        $last_date = $request->ldate;
        $fupdates=FollowUpDate::whereBetween('CreateDate', [ $first_date,  $last_date])
         ->where(function($query) use ($barcode_prefixes) {
             if (!empty($barcode_prefixes)) {
                $query->where(function($q) use ($barcode_prefixes) {
                    foreach ($barcode_prefixes as $prefix) {
                        $q->orWhere('RegistrationId', 'LIKE', $prefix . '%');
                    }
                });
            } 
        })
        ->get();
    
     
        $resultCount = $fupdates->count();
        $branches = BarcodeFormat::with('healthCenter')->whereIn('barcode_prefix', $barcode_prefixes)->get();
        $hcnames = $branches->pluck('healthCenter.HealthCenterName')->toArray();
        $healthcenter = implode(',', $hcnames) ?: 'All';
         $response = [
            'healthcenter' => $healthcenter,	
            'fupdates' => $fupdates,
            'resultCount' => $resultCount,
            'first_date' => $first_date,
            'last_date' => $last_date,
        ];

        return response()->json($response);

    }

     public function GetDistrictwisePatient(Request $request){
        $districts=District::get(['id','name']);
        $daterange = $request->daterange;
        $dates = explode(' - ', $daterange);
        $starting_date = $dates[0]??'';
        $ending_date = $dates[1]??'';
        $dc_id=$request->dc_id;
        $up_id=$request->up_id;
        $un_id=$request->un_id;
       
$results = Address::with('districtAddress','upazillaAddress','unionAddress')->selectRaw('CAST(Address.CreateDate AS DATE) as CreateDate, Patient.GivenName, Patient.FamilyName, Patient.Age,Address.PatientId, Address.District,Address.Thana, Address.UnionId')
    ->whereDate('Address.CreateDate', '>=', $starting_date)
    ->whereDate('Address.CreateDate', '<=', $ending_date)
    ->where(function ($query) use ($dc_id, $up_id, $un_id) {
        if ($dc_id) {
            $query->where('Address.District', '=', $dc_id);
        }
        if ($up_id) {
            $query->where('Address.Thana', '=', $up_id);
        }
        if ($un_id) {
            $query->where('Address.UnionId', '=', $un_id);
        }
    })
    ->join('Patient', 'Address.PatientId', '=', 'Patient.PatientId')
    ->get();

   


             
        
        $this->setPageData('Districtwise Patients','Districtwise Patients','fas fa-th-list');
        return view('report::districtwisepatients',compact('districts','results'));

    }

    
        public function GetUpazillas($district_id){
       
        $dc_id=(int)$district_id;
        $upazillas=Upazilla::where('district_id', $dc_id)->get();
        return response()->json($upazillas);

    }
       public function GetUnions($upazilla_id){
       
        $up_id=(int)$upazilla_id;
        $unions=Union::where('upazilla_id', $up_id)->get();
        return response()->json($unions);

    }
    




    public function SearchByDate(Request $request){
       
       
    
        $first_date = $request->fdate;
        $last_date = $request->ldate;
        $barcode_prefixes = $request->hc_ids;

    


        $results = DB::table("MDataProvisionalDiagnosis")
            ->select(
                'ProvisionalDiagnosis',
                DB::raw("CONVERT(varchar, MDataProvisionalDiagnosis.CreateDate, 106) as CreateDate"),
                DB::raw('COUNT(MDataProvisionalDiagnosis.PatientId) as Total')
            )
            ->whereDate('MDataProvisionalDiagnosis.CreateDate', '>=', $first_date)
            ->whereDate('MDataProvisionalDiagnosis.CreateDate', '<=', $last_date)
            ->where(function($query) use ($barcode_prefixes) {
                if (!empty($barcode_prefixes)) {
                $query->where(function($q) use ($barcode_prefixes) {
                    foreach ($barcode_prefixes as $prefix) {
                        $q->orWhere('Patient.RegistrationId', 'LIKE', $prefix . '%');
                    }
                });
            }
            })
            ->join('Patient', 'MDataProvisionalDiagnosis.PatientId', '=', 'Patient.PatientId')
            ->groupBy('ProvisionalDiagnosis', DB::raw("CONVERT(varchar, MDataProvisionalDiagnosis.CreateDate, 106)"))
            ->get();

        $pivotedResults = $results->groupBy('ProvisionalDiagnosis')
        ->map(function ($group) {
            return $group->keyBy('CreateDate')
                ->mapWithKeys(function ($item) {
                    return [date('d-m-Y', strtotime($item->CreateDate)) => $item->Total];
                });
        });

       


            
            $resultCount = $results->sum('Total');
         
            // $hcname=DB::table('HealthCenter')->where('HealthCenterCode',$barcode_prefix )->first('HealthCenterName');
            $branches = BarcodeFormat::with('healthCenter')->whereIn('barcode_prefix', $barcode_prefixes)->get();
            $hcnames = $branches->pluck('healthCenter.HealthCenterName')->toArray();
            $healthcenter = implode(',', $hcnames) ?: 'All';
            $response = [
                'healthcenter' => $healthcenter,	
                'results' => $pivotedResults,
                'resultCount' => $resultCount,
                'first_date' => $first_date,
                'last_date' => $last_date,
            ];
    
        return response()->json($response);

    }
      public function patientwisedxreport(Request $request){
       
       
    
        $first_date = $request->fdate;
        $last_date = $request->ldate;
        $barcode_prefixes = $request->hc_ids;


         $results = DB::table("MDataProvisionalDiagnosis")
        ->select(
        DB::raw("FORMAT(MDataProvisionalDiagnosis.CreateDate, 'dd/MM/yyyy') as CreateDate"),
        'ProvisionalDiagnosis',
        'MDataProvisionalDiagnosis.MDProvisionalDiagnosisId',
        'MDataProvisionalDiagnosis.PatientId',
        'Patient.RegistrationId',
        'Patient.GivenName',
        'Patient.FamilyName',
        DB::raw("CONVERT(varchar, Patient.BirthDate, 106) as BirthDate"),
        'Patient.Age',
        'Patient.CellNumber',
        'RefGender.GenderCode'
    )
    
    ->whereDate('MDataProvisionalDiagnosis.CreateDate', '>=', $first_date)
    ->whereDate('MDataProvisionalDiagnosis.CreateDate', '<=', $last_date)
    ->where(function($query) use ($barcode_prefixes) {
         if (!empty($barcode_prefixes)) {
                $query->where(function($q) use ($barcode_prefixes) {
                    foreach ($barcode_prefixes as $prefix) {
                        $q->orWhere('Patient.RegistrationId', 'LIKE', $prefix . '%');
                    }
                });
            } 
    })
    ->join('Patient', 'MDataProvisionalDiagnosis.PatientId', '=', 'Patient.PatientId')
    ->join('RefGender', 'RefGender.GenderId', '=', 'Patient.GenderId')
    ->groupBy(
        DB::raw("FORMAT(MDataProvisionalDiagnosis.CreateDate, 'dd/MM/yyyy')"),
        'ProvisionalDiagnosis',
        'MDataProvisionalDiagnosis.MDProvisionalDiagnosisId',
        'MDataProvisionalDiagnosis.PatientId',
        'Patient.RegistrationId',
        'Patient.GivenName',
        'Patient.FamilyName',
        DB::raw("CONVERT(varchar, Patient.BirthDate, 106)"),
        'Patient.Age',
        'Patient.CellNumber',
        'RefGender.GenderCode'
    )
    ->get();
            $resultCount = $results->count();
            $branches = BarcodeFormat::with('healthCenter')->whereIn('barcode_prefix', $barcode_prefixes)->get();
            $hcnames = $branches->pluck('healthCenter.HealthCenterName')->toArray();
            $healthcenter = implode(',', $hcnames) ?: 'All';
            $response = [
                'healthcenter' => $healthcenter,	
                'results' => $results,
                'resultCount' => $resultCount,
                'first_date' => $first_date,
                'last_date' => $last_date,
            ];
    
        return response()->json($response);

    }

        public function treatmentreport(Request $request){
        
        $first_date = $request->fdate;
        $last_date = $request->ldate;
        $barcode_prefix = $request->hc_id;

         $results = DB::table("MDataTreatmentSuggestion")
        ->select(
        DB::raw("FORMAT(MDataTreatmentSuggestion.CreateDate, 'dd/MM/yyyy') as CreateDate"),
        'MDataTreatmentSuggestion.MDTreatmentSuggestionId',
        'MDataTreatmentSuggestion.Frequency',
        'MDataTreatmentSuggestion.Hourly',
        'MDataTreatmentSuggestion.DrugDurationValue',
        'MDataTreatmentSuggestion.OtherDrug',
        'MDataTreatmentSuggestion.PatientId',
        'Patient.RegistrationId',
        'Patient.GivenName',
        'Patient.FamilyName',
        'Patient.BirthDate',
        'Patient.Age',
        'Patient.CellNumber',
        'RefGender.GenderCode',
        'RefInstruction.InstructionInBangla',
        'RefDrug.DrugCode'
    )
    
    ->whereDate('MDataTreatmentSuggestion.CreateDate', '>=', $first_date)
    ->whereDate('MDataTreatmentSuggestion.CreateDate', '<=', $last_date)
    ->where(function($query) use ($barcode_prefix) {
        if ($barcode_prefix) {
            $query->where('Patient.RegistrationId', 'LIKE', $barcode_prefix . '%');
        }
    })
    ->join('Patient', 'MDataTreatmentSuggestion.PatientId', '=', 'Patient.PatientId')
    ->join('RefGender', 'RefGender.GenderId', '=', 'Patient.GenderId')
    ->join('RefInstruction', 'RefInstruction.RefInstructionId', '=', 'MDataTreatmentSuggestion.RefInstructionId')
    ->join('RefDrug', 'RefDrug.DrugId', '=', 'MDataTreatmentSuggestion.DrugId')
    ->groupBy(
        DB::raw("FORMAT(MDataTreatmentSuggestion.CreateDate, 'dd/MM/yyyy')"),
        'MDataTreatmentSuggestion.MDTreatmentSuggestionId',
        'MDataTreatmentSuggestion.Frequency',
        'MDataTreatmentSuggestion.Hourly',
        'MDataTreatmentSuggestion.DrugDurationValue',
        'MDataTreatmentSuggestion.OtherDrug',
        'MDataTreatmentSuggestion.PatientId',
        'Patient.RegistrationId',
        'Patient.GivenName',
        'Patient.FamilyName',
        'Patient.BirthDate',
        'Patient.Age',
        'Patient.CellNumber',
        'RefGender.GenderCode',
        'RefInstruction.InstructionInBangla',
        'RefDrug.DrugCode'
    )
    ->get();
            $resultCount = $results->count();
            $hcname=HealthCenter::where('HealthCenterCode',$barcode_prefix )->first('HealthCenterName');
            $response = [
                'healthcenter' => $hcname->HealthCenterName ?? 'All',	
                'results' => $results,
                'resultCount' => $resultCount,
                'first_date' => $first_date,
                'last_date' => $last_date,
            ];
    
        return response()->json($response);

    }
       public function PatientagewisedxReport(Request $request){
       
        $first_date = $request->fdate;
        $last_date = $request->ldate;
        $barcode_prefixes = $request->hc_ids;
        $starting_age = $request->starting_age;
        $ending_age = $request->ending_age;


       $results = DB::table("MDataProvisionalDiagnosis")
        ->select(
            'ProvisionalDiagnosis',
           
            DB::raw('COUNT(MDataProvisionalDiagnosis.PatientId) as Total'),
            DB::raw("
                SUM(CASE 
                    WHEN RefGender.GenderCode = 'Male' AND Patient.Age >= 0 AND Patient.Age <= 5 THEN 1
                    ELSE 0
                END) as Male_0_5,
                SUM(CASE 
                    WHEN RefGender.GenderCode = 'Male' AND Patient.Age > 5 THEN 1
                    ELSE 0
                END) as Male_Above_5,
                SUM(CASE 
                    WHEN RefGender.GenderCode = 'Male' THEN 1
                    ELSE 0
                END) as Male_Total,
                SUM(CASE 
                    WHEN RefGender.GenderCode = 'Female' AND Patient.Age >= 0 AND Patient.Age <= 5 THEN 1
                    ELSE 0
                END) as Female_0_5,
                SUM(CASE 
                    WHEN RefGender.GenderCode = 'Female' AND Patient.Age > 5 THEN 1
                    ELSE 0
                END) as Female_Above_5,
                SUM(CASE 
                    WHEN RefGender.GenderCode = 'Female' THEN 1
                    ELSE 0
                END) as Female_Total
            ")
        )
        ->whereDate('MDataProvisionalDiagnosis.CreateDate', '>=', $first_date)
        ->whereDate('MDataProvisionalDiagnosis.CreateDate', '<=', $last_date)
        ->where(function($query) use ($barcode_prefixes,$starting_age,$ending_age) {
            if (!empty($barcode_prefixes)) {
                $query->where(function($q) use ($barcode_prefixes) {
                    foreach ($barcode_prefixes as $prefix) {
                        $q->orWhere('Patient.RegistrationId', 'LIKE', $prefix . '%');
                    }
                });
            } 
            if ($starting_age !== null) {
                $query->where(DB::raw('CAST(Patient.Age AS INT)'), '>=', $starting_age);
            }
            if ($ending_age !== null) {
                $query->where(DB::raw('CAST(Patient.Age AS INT)'), '<=', $ending_age);
            }
        })
        ->join('Patient', 'MDataProvisionalDiagnosis.PatientId', '=', 'Patient.PatientId')
        ->join('RefGender', 'RefGender.GenderId', '=', 'Patient.GenderId')
        ->groupBy(
            'ProvisionalDiagnosis',    
        )
        ->get();

            $branches = BarcodeFormat::with('healthCenter')->whereIn('barcode_prefix', $barcode_prefixes)->get();
            $hcnames = $branches->pluck('healthCenter.HealthCenterName')->toArray();
            $healthcenter = implode(',', $hcnames) ?: 'All';
            $response = [
                'healthcenter' => $healthcenter,
                'results' => $results,
                'first_date' => $first_date,
                'last_date' => $last_date,
            ];

        
            return response()->json($response);

    }

      public function SearchByHC(Request $request){
        $healthcenters=HealthCenter::get(['HealthCenterId','HealthCenterName']);
        $refcases=RefReferral::get(['RId','Description']);

        $daterange = $request->daterange;
        $dates = explode(' - ', $daterange);
// Assign the start and end dates to separate variables
        $starting_date = $dates[0]??'';
        $ending_date = $dates[1]??'';
        $hc = $request->hc_id;
        $rc = $request->rc_id;

        //  $results=[];
        //  $results['hcname']=HealthCenter::where('HealthCenterId',$hc)->get('HealthCenterName');


$results = DB::table("MDataPatientReferral")
    ->select(
        'MDataPatientReferral.RId',
        DB::raw('COUNT(MDataPatientReferral.PatientId) as TotalPatient'),
        DB::raw("MIN(CAST(MDataPatientReferral.CreateDate AS DATE)) as PRCreateDate"),
        DB::raw("MIN(RefReferral.Description) as Description"),
        DB::raw("MIN(HealthCenter.HealthCenterName) as HealthCenterName")
    )
    ->whereDate('MDataPatientReferral.CreateDate', '>=', $starting_date)
    ->whereDate('MDataPatientReferral.CreateDate', '<=', $ending_date)
    ->where(function($query) use ($hc, $rc) {
        if ($hc) {
            $query->where(DB::raw("CONVERT(VARCHAR(36), MDataPatientReferral.HealthCenterId)"), '=', $hc);
        }
        if ($rc) {
            $query->where(DB::raw("CONVERT(VARCHAR(36), MDataPatientReferral.RId)"), '=', $rc);
        }
    })
    ->join('RefReferral', 'MDataPatientReferral.RId', '=', 'RefReferral.RId')
    ->join('HealthCenter', 'MDataPatientReferral.HealthCenterId', '=', 'HealthCenter.HealthCenterId')
    ->groupBy('MDataPatientReferral.RId','MDataPatientReferral.HealthCenterId')
    ->get();


        $this->setPageData('Referrad Cases','Referrad Cases','fas fa-th-list');
        return view('report::hcwisereferral',compact('results','healthcenters','refcases'));

    }
    public function SearchByDisease(Request $request){
        $healthcenters=HealthCenter::get(['HealthCenterId','HealthCenterName']);
         $refcases=RefReferral::get(['RId','Description']);

        $daterange = $request->daterange;
        $dates = explode(' - ', $daterange);
// Assign the start and end dates to separate variables
        $starting_date = $dates[0]??'';
        $ending_date = $dates[1]??'';
        $hc = $request->hc_id;
        $rc = $request->rc_id;
        //  $results=[];
        //  $results['hcname']=HealthCenter::where('HealthCenterId',$hc)->get('HealthCenterName');


$results = DB::table("MDataPatientReferral")
    ->select(
        'MDataPatientReferral.RId',
        DB::raw('COUNT(MDataPatientReferral.PatientId) as TotalPatient'),
        DB::raw("MIN(CAST(MDataPatientReferral.CreateDate AS DATE)) as PRCreateDate"),
        DB::raw("MIN(RefReferral.Description) as Description"),
        DB::raw("MIN(HealthCenter.HealthCenterName) as HealthCenterName")
    )
    ->whereDate('MDataPatientReferral.CreateDate', '>=', $starting_date)
    ->whereDate('MDataPatientReferral.CreateDate', '<=', $ending_date)
    ->where(function($query) use ($hc, $rc) {
        if ($hc) {
            $query->where(DB::raw("CONVERT(VARCHAR(36), MDataPatientReferral.HealthCenterId)"), '=', $hc);
        }
        if ($rc) {
            $query->where(DB::raw("CONVERT(VARCHAR(36), MDataPatientReferral.RId)"), '=', $rc);
        }
    })
    ->join('RefReferral', 'MDataPatientReferral.RId', '=', 'RefReferral.RId')
    ->join('HealthCenter', 'MDataPatientReferral.HealthCenterId', '=', 'HealthCenter.HealthCenterId')
    ->groupBy('MDataPatientReferral.RId','MDataPatientReferral.HealthCenterId')
    ->get();




       $this->setPageData('Number of Patient By Disease','Number of Patient By Disease','fas fa-th-list');
        return view('report::diseasewisepatient',compact('results','healthcenters','refcases'));

    }

    public function AjaxTopTenDiseases(Request $request){
        $startDate = $request->starting_date;
        $endDate = $request->ending_date;
        $hcId = $request->hc_id;
        $illnesses=[];


        $illnesses['branch']=HealthCenter::where('HealthCenterCode',$hcId)->get('HealthCenterName');

        $illnesses['diseases'] = DB::table('MDataPatientIllnessHistory')
            ->join('RefIllness', 'MDataPatientIllnessHistory.IllnessId', '=', 'RefIllness.IllnessId')
            ->join('Patient', 'MDataPatientIllnessHistory.PatientId', '=', 'Patient.PatientId')
            ->where('Patient.RegistrationId', 'LIKE', $hcId . '%')
            ->whereBetween(DB::raw('CAST(MDataPatientIllnessHistory.CreateDate AS DATE)'),[$startDate, $endDate])
            ->groupBy('RefIllness.IllnessId', 'RefIllness.IllnessCode')
            ->orderByRaw('COUNT(DISTINCT MDataPatientIllnessHistory.PatientId) DESC')
            ->select('RefIllness.IllnessId', 'RefIllness.IllnessCode', DB::raw('COUNT(DISTINCT MDataPatientIllnessHistory.PatientId) as Patients'))
            ->take(10)
            ->get();

            // dd( $illnesses['diseases']);

         return view('report::toptendiseases_ajax',compact('illnesses'));
    }



    public function SearchByAge(Request $request){
        $starting_age = $request->starting_age;
        $ending_age = $request->ending_age;
        $male=$female=$maleBelowFive=$maleAboveFive=$femaleBelowFive=$femaleAboveFive=$Total=0;
        $results = DB::table("Patient")
            ->whereRaw('CONVERT(float, Age) >= ?', [$starting_age])
            ->whereRaw('CONVERT(float, Age) <= ?', [$ending_age])
            ->join('MDataProvisionalDiagnosis', 'Patient.PatientId', '=', 'MDataProvisionalDiagnosis.PatientId')
            ->join('RefGender', 'Patient.GenderId', '=', 'RefGender.GenderId')
            ->get(['ProvisionalDiagnosis', 'OtherProvisionalDiagnosis', 'GivenName', 'FamilyName', 'Age', 'GenderCode']);
        foreach ($results as $result){
            if ($result->Age < 6 && $result->GenderCode == 'Male'){
                $maleBelowFive++;
            }
            if ($result->Age > 5 && $result->GenderCode == 'Male'){
                $maleAboveFive++;
            }
            if ($result->Age < 6 && $result->GenderCode == 'Female'){
                $femaleBelowFive++;
            }
            if ($result->Age > 5 && $result->GenderCode == 'Female'){
                $femaleAboveFive++;
            }
            if ($result->GenderCode == 'Male'){
                $male++;
            }
            if ($result->GenderCode == 'Female'){
                $female++;
            }

        }
        $Total=$male+$female;


        $this->setPageData(
            'Report-'. str_repeat(' ', 2).
            'Total: '. $Total .','.  str_repeat(' ', 2).
            'Male: '. $male .','.  str_repeat(' ', 2)  .
            'Female: '.$female.','. str_repeat(' ', 2) .
            'Male 0-5: '. $maleBelowFive .','. str_repeat(' ', 2) .
            'Male above 5: '. $maleAboveFive .','. str_repeat(' ', 2) .
            'Female 0-5: '. $femaleBelowFive .','. str_repeat(' ', 2) .
            'Female above 5: '. $femaleAboveFive,
            'Patient Age Count Report',
            'fas fa-th-list'
        );

        return view('report::index',compact('results','male','Total','female','maleAboveFive','maleBelowFive','femaleAboveFive','femaleBelowFive'));

    }

    public function branchWisePatients(Request $request){
        $daterange = $request->daterange;
        $dates = explode(' - ', $daterange);
// Assign the start and end dates to separate variables
        $first_date = $dates[0]??'';
        $last_date = $dates[1]??'';

        $barcode_prefix = $request->patient_id??"";
        $male=$female=$Total=$femaleAboveFive=0;

        //branches
        $branches=BarcodeFormat::with('healthCenter')->get(); 

        $results = DB::table('barcode_formats')
            ->join('HealthCenter', 'barcode_formats.barcode_community_clinic', '=', 'HealthCenter.HealthCenterId')
            ->join('Patient', function ($join) use ($first_date, $last_date, $barcode_prefix) {
                // Cast uniqueidentifier to string and then extract the first nine characters
                $join->on(DB::raw('SUBSTRING(CONVERT(VARCHAR(36), Patient.RegistrationId), 1, 9)'), '=', 'barcode_formats.barcode_prefix')
                    ->whereBetween('Patient.CreateDate', [$first_date, $last_date]);
                if ($barcode_prefix) {
                    $join->Where('barcode_formats.barcode_prefix', $barcode_prefix);
                }
            })
            ->join('RefGender','RefGender.GenderId','=','Patient.GenderId')
            ->get();

        if($barcode_prefix){
            $barcode_tbl = DB::table('barcode_formats')
                ->join('HealthCenter', 'barcode_formats.barcode_community_clinic', '=', 'HealthCenter.HealthCenterId')
                ->where('barcode_formats.barcode_prefix',$barcode_prefix)
                ->first();
            $branchName = $barcode_tbl->HealthCenterName??'';
        }else{
            $branchName = '';
        }

        foreach ($results as $result){
            if ($result->GenderCode == 'Male'){
                $male++;
            }
            if ($result->GenderCode == 'Female'){
                $female++;
            }
        }

        $Total=$male+$female;

        $this->setPageData(
            'Report-'. str_repeat(' ', 2).
            'Total: '. $Total .','.  str_repeat(' ', 2).
            'Male: '. $male .','.  str_repeat(' ', 2)  .
            'Female: '.$female.','. str_repeat(' ', 2) .
            'Branch Name: '. $branchName,
            'Branch Wise Patient Report',
            'fas fa-th-list'
        );

        return view('report::branchwisepatients',compact('branches','branchName','results','male','Total','female'));
    }

    public function diseaseRateDateRange(Request $request){
        //branches
        $branches=BarcodeFormat::with('healthCenter')->get(); 
        $this->setPageData(
            'Report-',
            'Branch Wise All Disease Report',
            'fas fa-th-list'
        );
        return view('report::diseaserate_by_daterange',compact('branches'));
    }

    public function AjaxDiseaseRateDateRange(Request $request){
//          dd($request->all());
        $barcode_prefix = $request->hc_id;
        
        $first_date = $request->fdate;
        $last_date = $request->ldate;

        if($barcode_prefix){
            $barcode_tbl = HealthCenter::where('HealthCenterCode',$barcode_prefix)->first('HealthCenterName');
        
       
           
            $branchName = $barcode_tbl['HealthCenterName']??'';
        
        }else{
            $branchName = 'All Branch';
        }
      

        $illnesses = DB::table('MDataPatientIllnessHistory')
            ->join('RefIllness', 'MDataPatientIllnessHistory.IllnessId', '=', 'RefIllness.IllnessId')
            ->join('Patient', 'MDataPatientIllnessHistory.PatientId', '=', 'Patient.PatientId')
            ->whereBetween(DB::raw('CAST(MDataPatientIllnessHistory.CreateDate AS DATE)'),[$first_date, $last_date])
            ->where('Patient.RegistrationId', 'LIKE', $barcode_prefix . '%')
            ->groupBy('RefIllness.IllnessId', 'RefIllness.IllnessCode')
            ->orderByRaw('COUNT(DISTINCT MDataPatientIllnessHistory.PatientId) DESC')
            ->select('RefIllness.IllnessId', 'RefIllness.IllnessCode', DB::raw('COUNT(DISTINCT MDataPatientIllnessHistory.PatientId) as Patients'))
            ->get();

        $data = [
            'data' => [],
        ];

        foreach ($illnesses as $datat) {
            $data['data'][] = [$datat->IllnessCode ?? '', (int)$datat->Patients ?? 0];
        }
        $response = [
            'healthcenter' => $branchName,
            'data' => $data,
        ];
        return response()->json($response);
    }

    public function GlucoseGraph(Request $request){
        $starting_date = $request->starting_date;
        $ending_date = $request->ending_date;
        $RegistrationId = $request->registration_id;

           $datas = DB::select("
            WITH RankedData AS (
                SELECT
                    CONVERT(date, MDataGlucoseHb.CreateDate) AS DistinctDate,
                    RBG,
                    FBG,
                    Hemoglobin,
                    ROW_NUMBER() OVER (PARTITION BY CONVERT(date, MDataGlucoseHb.CreateDate) ORDER BY MDataGlucoseHb.CreateDate ASC) AS RowNum
                FROM MDataGlucoseHb
                INNER JOIN Patient ON Patient.PatientId = MDataGlucoseHb.PatientId AND Patient.RegistrationId = '{$RegistrationId}'
                WHERE CONVERT(date, MDataGlucoseHb.CreateDate) BETWEEN ? AND ?
            )
            SELECT
                DistinctDate,
                RBG,
                FBG,
                Hemoglobin
            FROM RankedData
            WHERE RowNum = 1
            ORDER BY DistinctDate ASC", [$starting_date, $ending_date]);

            foreach ($datas as $row) {
            $distinctDate = $row->DistinctDate;
            $medications = DB::table('MDataTreatmentSuggestion')
                ->select(['MDataTreatmentSuggestion.DrugId', 'DrugCode', DB::raw("CONVERT(date, MDataTreatmentSuggestion.CreateDate) AS Cdate")])
                ->join('Patient', 'MDataTreatmentSuggestion.PatientId', '=', 'Patient.PatientId')
                ->join('RefDrug', 'RefDrug.DrugId', '=', 'MDataTreatmentSuggestion.DrugId')
                ->where('Patient.RegistrationId', $RegistrationId)
                ->whereRaw("CONVERT(date, MDataTreatmentSuggestion.CreateDate) = ?", [$distinctDate])
                ->get();

            $medicationsByDate[$distinctDate] = $medications;
        }

        $rbg = array();
        $fbg = array();
        $hemoglobin = array();
        $DistinctDate = array();
        $medicationData=array();
        foreach ($datas as $gcRow) {
        $distinctDate = $gcRow->DistinctDate;
        $medications = $medicationsByDate[$distinctDate] ?? null;

            if ($medications !== null) {

                array_push($rbg, $gcRow->RBG);
                array_push($fbg, $gcRow->FBG);
                array_push($hemoglobin, $gcRow->Hemoglobin);
                array_push($DistinctDate, $gcRow->DistinctDate);
                $medicationData[] = $medications->toArray();
                
            }
        }

        $rbgNumeric = json_encode($rbg,JSON_NUMERIC_CHECK);
        $fbgNumeric = json_encode($fbg, JSON_NUMERIC_CHECK);
        $hemoglobinNumeric = json_encode($hemoglobin, JSON_NUMERIC_CHECK);
        $this->setPageData('Diabetes Mellitus','Diabetes Mellitus','fas fa-th-list');
        return view('report::glucose_ajax',compact('DistinctDate','rbg','rbgNumeric','fbg','fbgNumeric','hemoglobin','hemoglobinNumeric','medicationData'));
    }

     /**
     * Patient blood pressure Search form load.
     * @return Renderable
     */

    public function PatientBloodPressureGraph(){
      
        $branches=BarcodeFormat::with('healthCenter')->get();        
        $this->setPageData('Patient Blood Pressure Graph','Patient wise Blood Pressure Graph','fas fa-th-list');

        return view('report::patientbloodpressuregraph',compact('branches'));
    }

    public function GetPatients($hc_id){
       
        $registrationId=Patient::where('RegistrationId', 'LIKE', $hc_id . '%')->get();
        return response()->json($registrationId);

    }

     /**
     * Patient blood pressure Ajax graph with data load.
     * @return Renderable
     */

    public function AjaxPatientBloodPressure(Request $request){
        $startDate = $request->starting_date;
        $endDate = $request->ending_date;
        $RegistrationId = $request->registration_id;

      

       $datas = DB::select("
        WITH RankedData AS (
            SELECT
                CONVERT(date, MDataBP.CreateDate) AS DistinctDate,
                BPSystolic1,
                BPDiastolic1,
                BPSystolic2,
                BPDiastolic2,
                ROW_NUMBER() OVER (PARTITION BY CONVERT(date, MDataBP.CreateDate) ORDER BY MDataBP.CreateDate ASC) AS RowNum
            FROM MDataBP
            INNER JOIN Patient ON Patient.PatientId = MDataBP.PatientId AND Patient.RegistrationId = '{$RegistrationId}'
            WHERE CONVERT(date, MDataBP.CreateDate) BETWEEN ? AND ?
        )
        SELECT
            DistinctDate,
            BPSystolic1,
            BPDiastolic1,
            BPSystolic2,
            BPDiastolic2
        FROM RankedData
        WHERE RowNum = 1
        ORDER BY DistinctDate ASC", [$startDate, $endDate]);

       

       
            

      $medicationsByDate = [];

        foreach ($datas as $row) {
            $distinctDate = $row->DistinctDate;
            $medications = DB::table('MDataTreatmentSuggestion')
                ->select(['MDataTreatmentSuggestion.DrugId', 'DrugCode', DB::raw("CONVERT(date, MDataTreatmentSuggestion.CreateDate) AS Cdate")])
                ->join('Patient', 'MDataTreatmentSuggestion.PatientId', '=', 'Patient.PatientId')
                ->join('RefDrug', 'RefDrug.DrugId', '=', 'MDataTreatmentSuggestion.DrugId')
                ->where('Patient.RegistrationId', $RegistrationId)
                ->whereRaw("CONVERT(date, MDataTreatmentSuggestion.CreateDate) = ?", [$distinctDate])
                ->get();

            $medicationsByDate[$distinctDate] = $medications;
        }
        

       

       
        $BPSystolic1 = array();
        $BPDiastolic1 = array();
        $BPSystolic2 = array();
        $BPDiastolic2 = array();
        $DistinctDate = array();
        $medicationData=array();

         foreach ($datas as $bpRow) {
            $distinctDate = $bpRow->DistinctDate;
            $medications = $medicationsByDate[$distinctDate] ?? null;

            if ($medications !== null) {

                array_push($BPSystolic1, $bpRow->BPSystolic1);
                array_push($BPDiastolic1, $bpRow->BPDiastolic1);
                array_push($BPSystolic2, $bpRow->BPSystolic2);
                array_push($BPDiastolic2, $bpRow->BPDiastolic2);
                array_push($DistinctDate, $bpRow->DistinctDate);
                $medicationData[] = $medications->toArray();
                
            }
        }


        $BPSystolic1Numeric = json_encode($BPSystolic1,JSON_NUMERIC_CHECK);
        $BPDiastolic1Numeric = json_encode($BPDiastolic1, JSON_NUMERIC_CHECK);
        $BPSystolic2Numeric = json_encode($BPSystolic2, JSON_NUMERIC_CHECK);
        $BPDiastolic2Numeric = json_encode($BPDiastolic2, JSON_NUMERIC_CHECK);

        return view('report::bloodpressure_ajax',compact('BPSystolic1Numeric','BPDiastolic1Numeric',
        'BPSystolic2Numeric','BPDiastolic2Numeric','DistinctDate','BPSystolic1','BPDiastolic1',
        'BPSystolic2','BPDiastolic2','medicationData'));

       
    }

    /**
     * Hear rate Search form load.
     * @return Renderable
     */
    public function HeartRateGraph(){
        $registrationId=Patient::select('RegistrationId')->get();
        $this->setPageData('Hear Rate Graph','Hear Rate Graph','fas fa-th-list');

        return view('report::heartrategraph',compact('registrationId'));
    }

    /**
     * Hear Rate Ajax load graph with data.
     * @return Renderable
     */

     public function AjaxHeartRateGraph(Request $request){
        $startDate = $request->starting_date;
        $endDate = $request->ending_date;
        $RegistrationId = $request->registration_id;

        $datas = DB::select("SELECT TOP 7 CONVERT(DATE, MDataBP.CreateDate) AS DistinctDate, MDataBP.HeartRate
        FROM MDataBP
        INNER JOIN Patient ON Patient.PatientId=MDataBP.PatientId AND Patient.RegistrationId='{$RegistrationId}'
        WHERE CONVERT(DATE, MDataBP.CreateDate) BETWEEN ? AND ? AND MDataBP.HeartRate !=''
        GROUP BY CONVERT(DATE, MDataBP.CreateDate), MDataBP.HeartRate
        ORDER BY DistinctDate DESC", [$startDate, $endDate]);

        $HeartRate1 = array();
        $DistinctDate = array();

        foreach ($datas as $row) {
            array_push($HeartRate1, $row->HeartRate);
            array_push($DistinctDate, $row->DistinctDate);
        }

        $HeartRate1Numeric = json_encode($HeartRate1,JSON_NUMERIC_CHECK);
        return view('report::heartrategraph_ajax',compact('HeartRate1Numeric','DistinctDate','HeartRate1'));
    }

    /**
     * Temperature Graph Search form load.
     * @return Renderable
     */
    public function TemperatureGraph(){
        $registrationId=Patient::select('RegistrationId')->get();
        $this->setPageData('Temperature Graph','Temperature Graph','fas fa-th-list');
        return view('report::temperaturegraph',compact('registrationId'));
    }

    /**
     * Temperature Ajax load graph with data.
     * @return Renderable
     */

     public function AjaxTemperatureGraph(Request $request){
        $startDate = $request->starting_date;
        $endDate = $request->ending_date;
        $RegistrationId = $request->registration_id;

        $datas = DB::select("SELECT TOP 7 CONVERT(DATE, MDataBP.CreateDate) AS DistinctDate, MDataBP.CurrentTemparature
        FROM MDataBP
        INNER JOIN Patient ON Patient.PatientId=MDataBP.PatientId AND Patient.RegistrationId='{$RegistrationId}'
        WHERE CONVERT(DATE, MDataBP.CreateDate) BETWEEN ? AND ? AND MDataBP.CurrentTemparature !=''
        GROUP BY CONVERT(DATE, MDataBP.CreateDate), MDataBP.CurrentTemparature
        ORDER BY DistinctDate DESC", [$startDate, $endDate]);

        $CurrentTemparature1 = array();
        $DistinctDate = array();

        foreach ($datas as $row) {
            array_push($CurrentTemparature1, $row->CurrentTemparature);
            array_push($DistinctDate, $row->DistinctDate);
        }

        $CurrentTemparature1Numeric = json_encode($CurrentTemparature1,JSON_NUMERIC_CHECK);
        return view('report::temperaturegraph_ajax',compact('CurrentTemparature1Numeric','DistinctDate','CurrentTemparature1'));
    }

        public function SyncDatabase(Request $request)
    {
       
        $rawMacAddress = exec("getmac");
        $macAddress = $this->extractMacAddress($rawMacAddress);
        $last_sync = SyncRecord::where('IPAddress',$macAddress)->latest('CreateDate')->first();
        if(!empty($last_sync))
        {
            $newDate = date('d-m-Y h:i:s', strtotime($last_sync->OperationDate));
        }else{
            $newDate='';
        }
         $this->setPageData('Synchronize Data','Synchronize Data','Synchronize Data');
         return view('report::syncpage',compact('newDate'));
    }

     public function SyncDatabasePerform(Request $request)
    {
        set_time_limit(3600);
        
        $serverIp = '192.168.10.10'; // Replace with your SQL Server IP address
        $serverPort = 1433; // Replace with the SQL Server port

        $timeout = 5; // Set an appropriate timeout value
        $hostname = gethostname();
        $rawMacAddress = exec("getmac");
       
        $user=Auth::user()->name;
        $id=Auth::user()->cc_id;
        $id=intval($id);
        $type = gettype($id);
       
         

    // Extract the MAC address without "Media disconnected"
        $macAddress = $this->extractMacAddress($rawMacAddress);
      

        $workplace=DB::table('barcode_formats')->where('id', $id)->pluck('barcode_prefix')->first();
   
         
      
        // dd($hostname,$macAddress,$syncdate,$user);
       

    // Attempt to establish a socket connection
        $socket = @fsockopen($serverIp, $serverPort, $errno, $errstr, $timeout);
        if ($socket) {
        fclose($socket);
        
        $batchFilePath = 'E:\HaefaDB\Local-Server-Include.bat'; // Replace with the actual path to your batch file
        $output = shell_exec("E:\HaefaDB\Local-Server-Include.bat");
        //  Artisan::call('serve');
         exec("start /B $batchFilePath", $output, $returnCode);
        // dd($output);
        // $batchFilePath1 = env('BATCH_FILE_BASE_PATH') . DIRECTORY_SEPARATOR . 'Master.bat';
        
        // $here=Artisan::call('execute:master-batch');
        // dd($here);
        // $output = Artisan::output();
        //  Artisan::call('serve');

    // You can access the output and exit code if needed
        //  $output = Artisan::output();
        //  dd($output);

        // $batchFilePath = 'E:\HaefaDB\Local-Server-Include.bat'; // Update with the actual path
        
        // Create a new Process instance and run the batch file
        // $process = new Process(['cmd', '/c', $batchFilePath]);
      
        // $output=$process->run();
        // SyncJob::dispatch();
        //   dd($output);
    //      Artisan::call('queue:work', [
    //     '--queue' => 'default', // Specify the queue name if needed
    //     '--tries' => 3,         // Specify the number of job attempts
    // ]);
      
        
      
        $syncdate=Carbon::now()->toDateTimeString();
        
         SyncRecord::create([
        'DownloadUploadIndicator' => $hostname,
        'IPAddress' => $macAddress,
        'OperationDate' => $syncdate,
        'CreateDate' => $syncdate,
        'UpdateDate' => $syncdate,
        'CreateUser' => $user,
        'UpdateUser' => $user,
        'WorkPlaceId' => $workplace,

    ]);
        return response()->json('success');
   
        // $this->setPageData('Synchronize Data', 'Synchronize Data', 'Synchronize Data');
        // return view('report::syncsuccess');
        
        } else {
        return response()->json('Failure');
        // $this->setPageData('Synchronize Data', 'Synchronize Data', 'Synchronize Data');
        // return view('report::syncfail');
        
        }

    }
    private function extractMacAddress($rawMacAddress)
{
    // Use regular expressions to extract the MAC address
    // Use regular expressions to extract the MAC address without leading/trailing spaces
    $pattern = '/([0-9A-Fa-f:-]+)/';
    preg_match($pattern, $rawMacAddress, $matches);
    if (isset($matches[1])) {
        return trim($matches[1]); // Trim any leading/trailing spaces
    } else {
        return 'Unknown';
    }
}


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('report::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('report::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('report::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
public function generateReport(Request $request)
    {
        $areaId = $request->input('area_id');

        $topDiseases = Disease::select('name', DB::raw('COUNT(*) as occurrence'))
            ->where('area_id', $areaId)
            ->groupBy('name')
            ->orderByDesc('occurrence')
            ->limit(10)
            ->get();

        return view('reports.disease_pie_chart', compact('topDiseases'));
    }
}
