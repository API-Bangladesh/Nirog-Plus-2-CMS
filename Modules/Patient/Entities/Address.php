<?php

namespace Modules\Patient\Entities;
use Modules\Patient\Entities\Patient;
use Modules\Base\Entities\BaseModel;
use Modules\Report\Entities\District;
use Modules\Report\Entities\Union;
use Modules\Report\Entities\Upazilla;

class Address extends BaseModel
{
    protected $table = 'Address';
    public $timestamps = false;

    public function patient()
    {
      return $this->belongsTo(Patient::class,'PatientId', 'PatientId');
    }
    public function districtAddress()
    {
        return $this->belongsTo(District::class, 'District', 'id');
    }

  public function upazillaAddress()
  {
      return $this->belongsTo(Upazilla::class, 'Thana', 'id');
  }

  public function unionAddress()
  {
      return $this->belongsTo(Union::class, 'UnionId', 'id');
  }

   public function districtAddressPermanent()
    {
        return $this->belongsTo(District::class, 'DistrictParmanent', 'id');
    }

  public function upazillaAddressPermanent()
  {
      return $this->belongsTo(Upazilla::class, 'ThanaParmanent', 'id');
  }

  public function unionAddressPermanent()
  {
      return $this->belongsTo(Union::class, 'UnionIdParmanent', 'id');
  }
}