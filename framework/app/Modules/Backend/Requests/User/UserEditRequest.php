<?php
namespace App\Modules\Backend\Requests\User;

use App\Modules\Backend\Requests\Request;

class UserEditRequest extends Request{

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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:user,email,' . $this->get('id'),
            'password' => 'sometimes|nullable|min:6',
            'role_id' => 'required'
            //'total_prize' => 'required|integer|min:1',
        ];
    }

    public function messages(){
        return [
            'first_name.required' => '[First Name] cannot be blank.',
            'last_name.required' => '[Last Name] cannot be blank',
            'email.required' => '[Email] cannot be blank',
            'email.email' => '[Email] must be an email',
            'email.unique' => '[Email] has been used already.',
            'password.min' => '[Password] length must be >= 6.',
            'role_id.required' => '[Role] select one Role.',
        ];
    }
}