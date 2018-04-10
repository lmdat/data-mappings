<?php
namespace App\Modules\Backend\Requests\Dimension;

use App\Modules\Backend\Requests\Request;

class DimensionCreateRequest extends Request{

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
            'dim_name' => 'required_without:show_multiple',
            'dim_code' => 'required_without:show_multiple',
            // 'data_file' => 'required_with:show_multiple|max:10240|mimes:csv,xls,xlsx,txt'
            'data_file' => 'required_with:show_multiple|max:10240|mimetypes:text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            // 'role_id' => 'required'
            //'total_prize' => 'required|integer|min:1',
        ];
    }

    public function messages(){
        return [
            'dim_type.required' => '[Dimension Tpye] Select one',
            'dim_name.required_without' => '[Dimension Name] cannot be blank',
            'dim_code.required_without' => '[Dimension Code] cannot be blank',
            'data_file.required_with' => '[Data File] select one file to upload',
            'data_file.max' => '[Data File] file size must be <= 10Mb',
            'data_file.mimetypes' => '[Data File] file type must be csv, xls or xlsx',
           
           
        ];
    }
}