<?php
namespace App\Modules\Backend\Requests\Topic;

use App\Modules\Backend\Requests\Request;

class TopicCreateRequest extends Request{

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
            'topic_name' => 'required_without:show_multiple',
            // 'short_name' => 'required_without:show_multiple',
            'show_multiple' => 'sometimes',
            'multiple_item' => 'required_with:show_multiple'
            // 'role_id' => 'required'
            //'total_prize' => 'required|integer|min:1',
        ];
    }

    public function messages(){
        return [
            'type_id.required' => '[Mapping Type] Select one',
            'topic_name.required_without' => '[Topic Name] cannot be blank',
            // 'short_name.required_without' => '[Short Name] cannot be blank',
            'multiple_item.required_with' => '[Multple Items] cannot be blank',
           
        ];
    }
}