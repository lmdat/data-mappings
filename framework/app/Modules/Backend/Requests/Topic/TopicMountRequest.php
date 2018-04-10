<?php
namespace App\Modules\Backend\Requests\Topic;

use App\Modules\Backend\Requests\Request;

class TopicMountRequest extends Request{

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
            'data_file' => 'required|max:10240|mimetypes:text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            // 'role_id' => 'required'
            //'total_prize' => 'required|integer|min:1',
        ];
    }

    public function messages(){
        return [
            'data_file.required' => '[Data File] select one file to upload',
            'data_file.max' => '[Data File] file size must be <= 10Mb',
            'data_file.mimetypes' => '[Data File] file type must be csv, xls or xlsx',
           
        ];
    }
}