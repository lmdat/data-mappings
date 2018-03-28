<?php
namespace App\Modules\Backend\Requests\Account;

use App\Modules\Backend\Requests\Request;

class AccountCreateRequest extends Request{

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
            'account_name' => 'required_without:show_multiple',
            'account_code' => 'required_without:show_multiple',
            // 'data_file' => 'required_with:show_multiple|max:10240|mimes:csv,xls,xlsx,txt'
            'data_file' => 'required|max:10240|mimetypes:text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            // 'role_id' => 'required'
            //'total_prize' => 'required|integer|min:1',
        ];
    }

    public function messages(){
        return [
            'account_name.required_without' => '[Account Name] cannot be blank',
            'account_code.required_without' => '[Account Code] cannot be blank',
            'data_file.required_with' => '[Data File] select one file to upload',
            'data_file.max' => '[Data File] file size must be <= 10Mb',
            'data_file.mimetypes' => '[Data File] file type must be csv, xls or xlsx',
           
           
        ];
    }
}