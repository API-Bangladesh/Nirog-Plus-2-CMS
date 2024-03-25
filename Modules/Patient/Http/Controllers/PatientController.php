<?php

namespace Modules\Patient\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Patient\Entities\Patient;
use Modules\Patient\Entities\Gender;
use Modules\Patient\Entities\MaritalStatus;
use Modules\Patient\Entities\Address;
use Modules\Patient\Entities\RefHeadOfFamily;
use Modules\Patient\Entities\SelfType;
use Modules\Patient\Entities\District;
use Modules\Base\Http\Controllers\BaseController;
use Modules\Patient\Http\Requests\PatientFormRequest;
use Illuminate\Http\RedirectResponse;
use DB;
use Carbon\Carbon;
use Auth;
use Modules\RefEducation\Entities\RefEducation;
use Modules\RefReligion\Entities\RefReligion;
use Modules\Union\Entities\Union;
use Modules\Upazila\Entities\Upazila;

class PatientController extends BaseController
{
    public function __construct(Patient $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        if(permission('patient-access')){
            $patients=Patient::paginate(15);
            $this->setPageData('Patient','Patient','fas fa-th-list');
            return view('patient::index',compact('patients'));
        }else{
            return $this->unauthorized_access_blocked();
        }
    }

    // public function get_datatable_data(Request $request)
    // {
    //     if(permission('patient-access')){
    //         if($request->ajax()){

    //             set_time_limit(3600);

    //             if (!empty($request->name)) {
    //                 $this->model->setName($request->name);
    //             }

    //             $this->set_datatable_default_property($request);
    //             $list = $this->model->getDatatableList();

    //             $data = [];
    //             $no = $request->input('start');
    //             foreach ($list as $value) {
    //                 $no++;
    //                 $action = '';

    //                 // if(permission('patient-edit')){
    //                 //     $action .= ' <a class="dropdown-item edit_data" data-id="' . $value->PatientId . '"><i class="fas fa-edit text-primary"></i> Edit</a>';
    //                 // }
    //                 if(permission('patient-viewid')){
    //                     $action .= ' <a class="dropdown-item viewid_data" data-id="' . $value->PatientId . '"><i class="fas fa-eye text-success"></i> View</a>';
    //                 }
    //                 /* if(permission('patient-view')){
    //                     $action .= ' <a class="dropdown-item view_data" data-id="' . $value->PatientId . '"><i class="fas fa-eye text-success"></i> Last Prescription</a>';
    //                 } */
    //                 if(permission('patient-edit')){
    //                     $action .= ' <a class="dropdown-item" href="'.route("patient.edit",$value->PatientId).'"><i class="fas fa-eye text-success"></i> Edit</a>';
    //                 }


    //                 $row = [];

    //                 if(permission('patient-bulk-delete')){
    //                     $row[] = table_checkbox($value->PatientId);
    //                 }
    //                 $row[] = $no;
    //                 $row[] = $value->RegistrationId;
    //                 $row[] = $value->GivenName.' '.$value->FamilyName;
    //                 $row[] = $value->IdNumber;
    //                 $row[] = $value->CellNumber;
    //                 //$row[] = permission('patient-access') ? change_status($value->PatientId,$value->Status) : STATUS_LABEL[$value->Status];
    //                 //$row[] = permission('patient-edit') ? change_status($value->PatientId,$value->Status) : STATUS_LABEL[$value->Status];
    //                 $row[] = action_button($action);
    //                 $data[] = $row;
    //             }
    //             return $this->datatable_draw($request->input('draw'),$this->model->count_all(),
    //              $this->model->count_filtered(), $data);
    //         }else{
    //             $output = $this->access_blocked();
    //         }

    //         return response()->json($output);
    //     }
    // }

    public function store_or_update_data(PatientFormRequest $request)
    {
        if($request->ajax()){
            if(permission('patient-add') || permission('patient-edit')){
                $collection = collect($request->validated());
                $collection = $this->track_data($request->update_id,$collection);
                $result = $this->model->updateOrCreate(['id'=>$request->update_id],$collection->all());
                $output = $this->store_message($result,$request->update_id);
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
           return response()->json($this->access_blocked());
        }
    }

    public function store_or_update_data1(Request $request)
    {
        if($request->ajax()){
            if(permission('patient-add') || permission('patient-edit')){
                $currentTime = Carbon::now();
                $date=$currentTime->toDateTimeString();
                Patient::where('PatientId','=' ,$request->update_id)
                    ->update([
                        'PatientCode' => $request->PatientCode,
                        'RegistrationId' => $request->RegistrationId,
                        'WorkPlaceId' => $request->WorkPlaceId,
                        'WorkPlaceBranchId' => $request->WorkPlaceBranchId,
                        'GivenName' => $request->GivenName,
                        'FamilyName' => $request->FamilyName,
                        'BirthDate' => $request->BirthDate,
                        'Age' => $request->Age,
                        'CellNumber' => $request->CellNumber,
                        'GenderId' => $request->GenderId,
                        'IdType' => $request->IdType,
                        'PatientImage' => $request->profile_photo,
                        'IdNumber' => $request->IdNumber,
                        'IdOwner' => $request->IdOwner,
                        'MaritalStatusId' => $request->MaritalStatusId,
                        'SpouseName' => $request->SpouseName,
                        'ReligionId' => $request->ReligionId,
                        'FamilyMembers' => $request->FamilyMembers,
                        'FatherName' => $request->FatherName,
                        'MotherName' => $request->MotherName,
                        'EducationId' => $request->EducationId,
                        'HeadOfFamilyId' => $request->HeadOfFamilyId,
                        'ChildAge0To1' => $request->ChildAge0To1,
                        'ChildAge1To5' => $request->ChildAge1To5,
                        'ChildAgeOver5' => $request->ChildAgeOver5,
                        'UpdateUser' => auth()->user()->name,
                        'UpdateDate' => $date,
                ]);
                Address::where('AddressId','=' ,$request->address_update_id)
                    ->update([
                        'PatientId' => $request->update_id,
                        'AddressLine1' => $request->AddressLine1,
                        'Village' => $request->Village,
                        'Thana' => $request->Thana,
                        'PostCode' => $request->PostCode,
                        'District' => $request->District,
                        'Country' => $request->Country,
                        'AddressLine1Parmanent' => $request->AddressLine1Parmanent,
                        'VillageParmanent' => $request->VillageParmanent,
                        'ThanaParmanent' => $request->ThanaParmanent,
                        'CountryParmanent' => $request->CountryParmanent,
                        'Camp' => $request->Camp,
                        'BlockNumber' => $request->BlockNumber,
                        'Majhi' => $request->Majhi,
                        'TentNumber' => $request->TentNumber,
                        'FCN' => $request->FCN,
                        'UpdateUser' => auth()->user()->name,
                        'UpdateDate' => $date,
                ]);

                $output = $this->store_message('ok',$request->update_id);
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
           return response()->json($this->access_blocked());
        }
    }

    public function edit(Request $request)
    {

            $this->setPageData('Edit Patient','Edit Patient','fab fa-pencil',[['name'=>'Patient','link'=> route('patient')],['name' => 'Edit Patient']]);

            if(permission('patient-edit')){

                $data =  Patient::where('PatientId','=',$request->id);

                $output = $this->data_message($data);
                $data = [
                    'patient' => Patient::where('PatientId','=',$request->id)->first(),
                    'genders' => Gender::all(),
                    'maritals' => MaritalStatus::all(),
                    'selfTypes' => SelfType::all(),
                    'address' => Address::with('upazillaAddress','unionAddress','districtAddress','districtAddressPermanent','upazillaAddressPermanent','unionAddressPermanent')->where('PatientId','=',$request->id)->first(),
                    'districts' => District::all(),
                    'upazilas' => Union::all(),
                    'educations' => RefEducation::all(),
                    'religions' => RefReligion::all(),
                    'RefHeadOfFamilies' => RefHeadOfFamily::all(),
                ];
                
                return view('patient::edit',$data);
            }else{
                return $this->access_blocked();
            }

    }

    public function delete(Request $request)
    {
        if($request->ajax()){
            if(permission('patient-delete')){
                $result = $this->model->find($request->id)->delete();
                $output = $this->delete_message($result);
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
            return response()->json($this->access_blocked());
        }
    }

    public function bulk_delete(Request $request)
    {
        if($request->ajax()){
            if(permission('patient-bulk-delete')){
                $result = $this->model->destroy($request->ids);
                $output = $this->bulk_delete_message($result);
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
            return response()->json($this->access_blocked());
        }
    }

    public function change_status(Request $request)
    {
        if($request->ajax()){
            if (permission('patient-edit')) {
                $result = $this->model->find($request->id)->update(['Status'=>$request->status]);
                $output = $result ? ['status'=>'success','message'=>'Status has been changed successfully']
                : ['status'=>'error','message'=>'Failed to change status'];
            }else{
                $output = $this->access_blocked();
            }
            return response()->json($output);
        }else{
            return response()->json($this->access_blocked());
        }
    }

    public function show(Request $request)
    {
        if($request->ajax()){
            if (permission('patient-view')) {

            $patientDetails=Patient::with('Gender')->where('PatientId','=',$request->id)->get();

            $prescriptionCreation= DB::select("SELECT TOP 1 MAX(PPC.PrescriptionId) AS PrescriptionId,MAX(E.FirstName) AS FirstName,MAX(E.LastName) AS LastName,MAX(E.Designation) AS Designation,MAX(E.EmployeeSignature) AS EmployeeSignature, CAST(PPC.CreateDate AS date) as CreateDate
            FROM PrescriptionCreation as PPC
            INNER JOIN Employee as E on E.EmployeeId = PPC.EmployeeId
            WHERE PatientId = '$request->id' AND CAST(PPC.CreateDate AS date)
            = CAST(
                (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
                FROM PrescriptionCreation WHERE PatientId = '$request->id'
                GROUP BY CAST(CreateDate AS date)
                ORDER BY MaxCreateDate DESC)
                AS date) GROUP BY PPC.CreateDate ORDER BY PPC.CreateDate DESC");

        $Complaints= DB::select("SELECT PC.ChiefComplain AS ChiefComplain, PC.CCDurationValue AS CCDurationValue, PC.OtherCC AS OtherCC, RD.DurationInEnglish AS DurationInEnglish, PC.CreateDate AS CreateDate
            FROM MDataPatientCCDetails as PC
            INNER JOIN RefDuration as RD on RD.DurationId = PC.DurationId
            WHERE PatientId = '$request->id' AND CAST(PC.CreateDate AS date)
            = CAST(
                (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
                FROM MDataPatientCCDetails WHERE PatientId = '$request->id'
                GROUP BY CAST(CreateDate AS date)
                ORDER BY MaxCreateDate DESC)
                AS date) ORDER BY PC.CreateDate");

        $HeightWeight= DB::select("SELECT TOP 1 MAX(Height) AS Height, MAX(Weight) AS Weight, MAX(BMI) AS BMI, MAX(BMIStatus) AS BMIStatus, CAST(CreateDate AS date) as CreateDate
            FROM MDataHeightWeight WHERE PatientId = '$request->id' AND CAST(CreateDate AS date)
            = CAST(
                (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
                FROM MDataHeightWeight WHERE PatientId = '$request->id'
                GROUP BY CAST(CreateDate AS date)
                ORDER BY MaxCreateDate DESC)
                AS date) GROUP BY CreateDate ORDER BY CreateDate");

        $BP= DB::select("SELECT TOP 1 MAX(BPSystolic1) AS BPSystolic1, MAX(BPDiastolic1) AS BPDiastolic1, MAX(BPSystolic2) AS BPSystolic2, MAX(BPDiastolic2) AS BPDiastolic2, MAX(HeartRate) AS HeartRate, MAX(CurrentTemparature) AS CurrentTemparature, CAST(CreateDate AS date) as CreateDate
            FROM MDataBP WHERE PatientId = '$request->id' AND CAST(CreateDate AS date)
            = CAST(
                (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
                FROM MDataBP WHERE PatientId = '$request->id'
                GROUP BY CAST(CreateDate AS date)
                ORDER BY MaxCreateDate DESC)
                AS date) GROUP BY CreateDate ORDER BY CreateDate");

        $GlucoseHb = DB::select("SELECT TOP 1 MAX(RBG) AS RBG, MAX(FBG) AS FBG, MAX(Hemoglobin) AS Hemoglobin, MAX(HrsFromLastEat) AS HrsFromLastEat, CAST(CreateDate AS date) as CreateDate
            FROM MDataGlucoseHb WHERE PatientId = '$request->id' AND CAST(CreateDate AS date)
            = CAST(
                (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
                FROM MDataGlucoseHb WHERE PatientId = '$request->id'
                GROUP BY CAST(CreateDate AS date)
                ORDER BY MaxCreateDate DESC)
                AS date) GROUP BY CreateDate ORDER BY CreateDate");

        $ProvisionalDx = DB::select("SELECT ProvisionalDiagnosis, DiagnosisStatus, OtherProvisionalDiagnosis, CreateDate
        FROM MDataProvisionalDiagnosis WHERE PatientId = '$request->id' AND CAST(CreateDate AS date)
        = CAST(
            (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
            FROM MDataProvisionalDiagnosis WHERE PatientId = '$request->id'
            GROUP BY CAST(CreateDate AS date)
            ORDER BY MaxCreateDate DESC)
            AS date) ORDER BY CreateDate");


       $Investigation= DB::select("SELECT  RI.Investigation AS Investigation, I.OtherInvestigation AS OtherInvestigation, I.CreateDate
            FROM MDataInvestigation as I
            INNER JOIN RefLabInvestigation as RI on RI.RefLabInvestigationId = I.InvestigationId
            WHERE PatientId = '$request->id' AND CAST(I.CreateDate AS date)
            = CAST(
                (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
                FROM MDataInvestigation WHERE PatientId = '$request->id'
                GROUP BY CAST(CreateDate AS date)
                ORDER BY MaxCreateDate DESC)
                AS date) ORDER BY I.CreateDate");


        $Treatment= DB::select("SELECT T.Frequency AS Frequency, T.DrugDurationValue AS DrugDurationValue,T.OtherDrug AS OtherDrug,T.SpecialInstruction AS SpecialInstruction, Dr.DrugCode AS DrugCode, Dr.Description AS Description, Dr.DrugDose AS DrugDose, Ins.InstructionInBangla AS InstructionInBangla, T.CreateDate
        FROM MDataTreatmentSuggestion as T
        INNER JOIN RefDrug as Dr on Dr.DrugId = T.DrugId
        INNER JOIN RefInstruction as Ins on Ins.RefInstructionId = T.RefInstructionId
        WHERE PatientId = '$request->id' AND CAST(T.CreateDate AS date)
        = CAST(
            (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
            FROM MDataTreatmentSuggestion WHERE PatientId = '$request->id'
            GROUP BY CAST(CreateDate AS date)
            ORDER BY MaxCreateDate DESC)
            AS date) ORDER BY T.CreateDate");


        $Advice= DB::select("SELECT RA.AdviceInBangla AS AdviceInBangla, RA.AdviceInEnglish AS AdviceInEnglish, A.CreateDate
            FROM MDataAdvice as A
            INNER JOIN RefAdvice as RA on RA.AdviceId = A.AdviceId
            WHERE PatientId = '$request->id' AND CAST(A.CreateDate AS date)
            = CAST(
                (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
                FROM MDataAdvice WHERE PatientId = '$request->id'
                GROUP BY CAST(CreateDate AS date)
                ORDER BY MaxCreateDate DESC)
                AS date) ORDER BY A.CreateDate");


        $PatientReferral= DB::select("SELECT RR.Description AS Description, HC.HealthCenterName AS HealthCenterName, PR.CreateDate
            FROM MDataPatientReferral as PR
            INNER JOIN RefReferral as RR on RR.RId = PR.RId
            INNER JOIN HealthCenter as HC on HC.HealthCenterId = PR.HealthCenterId
            WHERE PatientId = '$request->id' AND CAST(PR.CreateDate AS date)
            = CAST(
                (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
                FROM MDataPatientReferral WHERE PatientId = '$request->id'
                GROUP BY CAST(CreateDate AS date)
                ORDER BY MaxCreateDate DESC)
                AS date) ORDER BY PR.CreateDate");



        $FollowUpDate= DB::select("SELECT TOP 1 MAX(FD. FollowUpDate) AS FollowUpDate, MAX(FD.Comment) AS Comment, CAST(FD.CreateDate AS date) as CreateDate
            FROM MDataFollowUpDate as FD
            WHERE PatientId = '$request->id' AND CAST(FD.CreateDate AS date)
            = CAST(
                (SELECT TOP 1 MAX(CreateDate) AS MaxCreateDate
                FROM MDataPatientReferral WHERE PatientId = '$request->id'
                GROUP BY CAST(CreateDate AS date)
                ORDER BY MaxCreateDate DESC)
                AS date) GROUP BY FD.CreateDate ORDER BY FD.CreateDate");


            }
        }

        return view('patient::details',compact('patientDetails','prescriptionCreation','Complaints','HeightWeight','BP','GlucoseHb','ProvisionalDx','Investigation','Treatment','Advice','PatientReferral','FollowUpDate'))->render();
    }

    public function showid(Request $request){

        $patientDetails=Patient::with('Gender','MaritalStatus','self_type','address')->where('PatientId','=',$request->id)->first();
        return view('patient::detailsp',compact('patientDetails'))->render();

    }
   public function searchid(Request $request) {
    $patients = Patient::where('RegistrationId', $request->patient_id)->paginate(15);

    $this->setPageData('Patient', 'Patient', 'fas fa-th-list');
    return view('patient::index', compact('patients'));
}

}
