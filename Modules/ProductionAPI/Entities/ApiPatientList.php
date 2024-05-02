<?php

namespace Modules\ProductionAPI\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Base\Entities\BaseModel;

class ApiPatientList extends BaseModel
{
    protected $table = 'ApiPateintLists';

    protected $guarded = [];

    public $timestamps = false;
    

}
