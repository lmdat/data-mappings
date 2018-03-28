<?php
namespace App\Modules\Backend\Requests\Account;

use App\Modules\Backend\Requests\Request;

class AccountEditRequest extends Request{

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
            'account_name' => 'required',
            'account_code' => 'required',
            
            // 'role_id' => 'required'
            //'total_prize' => 'required|integer|min:1',
        ];
    }

    public function messages(){
        return [
            'account_name.required' => '[Account Name] cannot be blank',
            'account_code.required' => '[Account Code] cannot be blank',
           
        ];
    }
}