<?php
namespace App\Modules\Backend\Requests\Company;

use App\Modules\Backend\Requests\Request;

class CompanySelectRequest extends Request{

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
            'company_id' => 'required',
            
        ];
    }

    public function messages(){
        return [
            'company_id.required' => '[Company Name] Select one.',
        ];
    }
}