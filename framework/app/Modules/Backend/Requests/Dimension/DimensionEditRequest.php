<?php
namespace App\Modules\Backend\Requests\Dimension;

use App\Modules\Backend\Requests\Request;

class DimensionEditRequest extends Request{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        //return Auth::admin()->check();
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        return [
            'dim_type' => 'required',
            'dim_name' => 'required',
            // 'account_code' => 'required',
            
            // 'role_id' => 'required'
            //'total_prize' => 'required|integer|min:1',
        ];
    }

    public function messages(){
        return [
            'dim_name.required' => '[Dimension Name] cannot be blank',
            'dim_type.required' => '[Dimension Type] Select one',
           
        ];
    }
}