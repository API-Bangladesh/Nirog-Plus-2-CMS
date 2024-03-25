<?php

namespace Modules\District\Http\Requests;

use App\Http\Requests\FormRequest;

class DistrictFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        $rules['name'] = ['required'];
        if(request()->update_id){
            $rules['name'] = 'unique:districts,name,'.request()->update_id;
        }
        $rules['division_id'] = ['required'];
        $rules['bn_name'] = ['nullable'];
        $rules['url'] = ['nullable'];
        $rules['lat'] = ['nullable'];
        $rules['lon'] = ['nullable'];
        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
