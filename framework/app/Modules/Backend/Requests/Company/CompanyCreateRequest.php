<?php
namespace App\Modules\Backend\Requests\Company;

use App\Modules\Backend\Requests\Request;

class CompanyCreateRequest extends Request{

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
            'company_name' => 'required',
            'short_name' => 'required',
            // 'email' => 'required|email|unique:user,email',
            // 'password' => 'required|min:6',
            // 'role_id' => 'required'
            //'total_prize' => 'required|integer|min:1',
        ];
    }

    public function messages(){
        return [
            'company_name.required' => '[Company Name] cannot be blank.',
            'short_name.required' => '[Short Name] cannot be blank',
        ];
    }
}