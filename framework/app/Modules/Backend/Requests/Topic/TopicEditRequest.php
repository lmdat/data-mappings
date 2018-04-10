<?php
namespace App\Modules\Backend\Requests\Topic;

use App\Modules\Backend\Requests\Request;

class TopicEditRequest extends Request{

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
            'type_id' => 'required',
            'topic_name' => 'required',
            // 'short_name' => 'required',
            //'total_prize' => 'required|integer|min:1',
        ];
    }

    public function messages(){
        return [
            'type_id.required' => '[Mapping Type] Select one',
            'topic_name.required' => '[Item Name] cannot be blank',
            // 'short_name.required' => '[Short Name] cannot be blank',
        ];
    }
}