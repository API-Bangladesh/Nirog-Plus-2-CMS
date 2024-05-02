<?php

namespace Modules\Patient\Entities;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\HealthCenter\Entities\HealthCenter;
use Modules\Patient\Entities\Gender;
use Modules\Patient\Entities\MaritalStatus;
use Modules\Patient\Entities\Address;
use Modules\Patient\Entities\SelfType;
use Modules\Base\Entities\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Prescription\Entities\Prescription;

class Patient extends BaseModel
{
    protected $table = 'Patient';
    public $timestamps = false;
    protected $fillable = ['PatientId', 'WorkPlaceId', 'WorkPlaceBranchId','PatientCode',
        'RegistrationId','GivenName','FamilyName','GenderId','BirthDate','Age','AgeYear',
        'AgeMonth','AgeDay','JoiningDate','ReligionId','RefDepartmentId','RefDesignationId',
        'MaritalStatusId','EducationId','FatherName','MotherName','SpouseName','HeadOfFamilyId',
        'IdNumber','CellNumber','FamilyMembers','ChildrenNumber','ChildAge0To1','ChildAge1To5',
        'ChildAgeOver5','EmailAddress','PatientImage','Status','CreateDate', 'CreateUser',
        'UpdateDate', 'UpdateUser', 'OrgId'];
    protected $order = ['CreateDate'=>'desc'];

    protected $name;
    public $incrementing = false;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function Gender()
    {
        return $this->hasOne(Gender::class, 'GenderId', 'GenderId')->select('GenderId','GenderCode');
    }
    public function MaritalStatus()
    {
        return $this->hasOne(MaritalStatus::class, 'MaritalStatusId', 'MaritalStatusId')->select('MaritalStatusId','MaritalStatusCode');
    }
    public function self_type()
    {
        return $this->hasOne(SelfType::class,'HeadOfFamilyId', 'IdOwner')->select('HeadOfFamilyId','HeadOfFamilyCode');
    }
    public function address()
    {
        return $this->hasOne(Address::class,'PatientId', 'PatientId');
    }

    public function prescription()
    {
      return $this->belongsTo(Prescription::class, 'PatientId', 'PatientId');
    }


    private function get_datatable_query()
    {
        if(permission('patient-bulk-delete')){
            $this->column_order = [null,'PatientId','name','status',null];
        }else{
            $this->column_order = ['PatientId','name','status',null];
        }

        $query = self::toBase();

        /*****************
         * *Search Data **
         ******************/
        if (!empty($this->name)) {
            $query->where('RegistrationId',$this->name);
        }

        if (isset($this->orderValue) && isset($this->dirValue)) {
            $query->orderBy($this->column_order[$this->orderValue], $this->dirValue);
        } else if (isset($this->order)) {
            $query->orderBy(key($this->order), $this->order[key($this->order)]);
        }
        return $query;
    }

    public function getDatatableList()
    {
        $query = $this->get_datatable_query();
        if ($this->lengthVlaue != -1) {
            $query->offset($this->startVlaue)->limit($this->lengthVlaue);
        }
        return $query->get();
    }

    public function count_filtered()
    {
        $query = $this->get_datatable_query();
        return $query->get()->count();
    }

    public function count_all()
    {
        return self::toBase()->get()->count();
    }

    public static function get_branch_name()
    {

         $cc_prefix=BarcodeFormat::where('id',Auth::user()->cc_id)->first('barcode_community_clinic') ?? '';

         if (!empty($cc_prefix)) {
         return HealthCenter::where('HealthCenterId',$cc_prefix->barcode_community_clinic ??'')->first()->HealthCenterName;
        } else {
            return '';
        }
  
    }

    public static function get_branch_id()
    {
        $login_user = Auth::user()->cc_id ?? 0;

        return Patient::select('HealthCenter.HealthCenterId')
            ->join('barcode_formats AS bf', DB::raw('LEFT(Patient.RegistrationId, 9)'), '=', 'bf.barcode_prefix')
            ->join('HealthCenter', 'bf.barcode_community_clinic', '=', 'HealthCenter.HealthCenterId')
            ->join('users AS u', 'u.cc_id', '=', 'bf.id')
            ->where('u.cc_id', $login_user)
            ->first()->HealthCenterId?? '69A15C52-B198-4DBC-9EAE-000000000000';
    }

    //get branch wise disease

    public static function get_branch_wise_disease_count()
    {
   
         $cc_prefix=BarcodeFormat::where('id',Auth::user()->cc_id)->first('barcode_prefix') ?? '';
         $cc=$cc_prefix->barcode_prefix ?? '';
     
        $disease_array = [
            '81F1CECB-8B22-41D3-AE18-54CC2D79065F',
            '81209B1C-8C0D-414C-A5ED-3D179F3B463A',
            '98E2AE4F-7639-49CA-A7AF-9FE396F5EDC2',
            'BB268EAB-EDD6-4D50-8886-C418C133C555',
            '0C436780-E230-4A61-8B9C-C111CF294539',
            'A69382EF-905C-4FC1-BA32-53E86FC50E35'
        ];

        $today = Carbon::today();
        $startDate = $today->format('Y-m-d');

 $all_diseases = MDataPatientIllnessHistory::selectRaw('COUNT(DISTINCT MDataPatientIllnessHistory.PatientId) as count, MDataPatientIllnessHistory.IllnessId ')
    ->join('Patient', 'Patient.PatientId', '=', 'MDataPatientIllnessHistory.PatientId')
     // Add this line
    ->where('Patient.RegistrationId', 'LIKE', $cc. '%')
    ->whereIn('MDataPatientIllnessHistory.IllnessId', $disease_array)
    ->whereDate('MDataPatientIllnessHistory.CreateDate', $startDate)
    ->where('MDataPatientIllnessHistory.Status','LIKE', 'yes')
    ->whereDate(DB::raw('CONVERT(date, MDataPatientIllnessHistory.CreateDate)'), $startDate)
    ->groupBy('MDataPatientIllnessHistory.IllnessId')
    ->get();


         

        return $all_diseases;
    }

    //get branch wise referred case with referrel center
    public static function branch_wise_referred_case_with_referrel_center_count(){
        $branch_id = Patient::get_branch_id();

        $today = Carbon::today();
        $startDate = $today->format('m/d/Y');

        return MDataPatientReferral::selectRaw('COUNT(DISTINCT MDataPatientReferral.PatientId) as number_of_referred_case')
            ->join('HealthCenter', 'HealthCenter.HealthCenterId', '=', 'MDataPatientReferral.HealthCenterId')
            ->join('Patient', 'Patient.PatientId', '=', 'MDataPatientReferral.PatientId')
            ->when($branch_id !== null, function ($query) use ($branch_id) {
                $query->where('HealthCenter.HealthCenterId', $branch_id);
            }, function ($query) {
                $query->whereNull('HealthCenter.HealthCenterId');
            })
            ->whereDate(DB::raw('CONVERT(date, MDataPatientReferral.CreateDate)'), $startDate)
            ->groupBy('HealthCenter.HealthCenterName')
            ->orderByRaw('COUNT(*) DESC')
            ->first()->number_of_referred_case?? 0;
    }

    //get referred

  

    //top ten disease based on patient

    public static function top_ten_disease()
    {
      
        $today = Carbon::today();
        $startDate = $today->format('Y-m-d');

        $cc_prefix=BarcodeFormat::where('id',Auth::user()->cc_id)->first('barcode_prefix');
        $cc=$cc_prefix->barcode_prefix ?? '';

      

        $illnesses= DB::table('MDataPatientIllnessHistory')
            ->join('RefIllness', 'MDataPatientIllnessHistory.IllnessId', '=', 'RefIllness.IllnessId')
            ->join('Patient', 'MDataPatientIllnessHistory.PatientId', '=', 'Patient.PatientId')
            ->where('Patient.RegistrationId', 'LIKE', $cc . '%')
            ->where('MDataPatientIllnessHistory.Status','LIKE', 'yes')
            ->whereDate(DB::raw('CONVERT(date, MDataPatientIllnessHistory.CreateDate)'), $startDate)
            ->groupBy('RefIllness.IllnessId', 'RefIllness.IllnessCode')
            ->orderByRaw('COUNT(DISTINCT MDataPatientIllnessHistory.PatientId) DESC')
            ->select('RefIllness.IllnessId', 'RefIllness.IllnessCode', DB::raw(' COUNT(DISTINCT MDataPatientIllnessHistory.PatientId) as Patients'))
            ->take(10)
            ->get();


        return $illnesses;
    }

    //all disease based on patient start

    public static function all_disease()
    {
        $today = Carbon::today();
        $startDate = $today->format('Y-m-d');

        $cc_prefix=BarcodeFormat::where('id',Auth::user()->cc_id)->first('barcode_prefix');
        $cc=$cc_prefix->barcode_prefix ?? '';

      

        $illnesses= DB::table('MDataPatientIllnessHistory')
            ->join('RefIllness', 'MDataPatientIllnessHistory.IllnessId', '=', 'RefIllness.IllnessId')
            ->join('Patient', 'MDataPatientIllnessHistory.PatientId', '=', 'Patient.PatientId')
            ->where('Patient.RegistrationId', 'LIKE', $cc . '%')
            ->where('MDataPatientIllnessHistory.Status','LIKE', 'yes')
            ->whereDate(DB::raw('CONVERT(date, MDataPatientIllnessHistory.CreateDate)'), $startDate)
            ->groupBy('RefIllness.IllnessId', 'RefIllness.IllnessCode')
            ->orderByRaw('COUNT(DISTINCT MDataPatientIllnessHistory.PatientId) DESC')
            ->select('RefIllness.IllnessId', 'RefIllness.IllnessCode', DB::raw(' COUNT(DISTINCT MDataPatientIllnessHistory.PatientId) as Patients'))
            ->get();


           return $illnesses;
    }
    //all disease based on patient end
}
