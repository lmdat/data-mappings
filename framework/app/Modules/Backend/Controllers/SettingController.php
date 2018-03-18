<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Http\Request;
use App\Libs\Utils\Vii;

class SettingController extends Controller{

    const LANG_NAME = 'setting';

    public function __construct(){
        parent::__construct();
    }

    public function getDimension(Request $request){
        
        $data = [
            '110' => 'Front Office',
            '260' => 'Banquet',
            '270' => 'Room Service',
            '280' => 'Pastry',
            '250' => 'Pool',
            '290' => 'Mini Bar',
            '310' => 'Telephone',
            '320' => 'Guest Laundry',
            '330' => 'Business Centre',
            '340' => 'Souvenir Shop',
            '350' => 'Fitness',
            '360' => 'Transportation',
            '380' => 'Other Income',
            '810' => 'House Laundry',
            '270' => 'Room Service'
        ];

        $type_list = [
            '' => '---',
            1 => 'Department | 100',
            2 => 'Salary | 101',
            3 => 'Meal Period | 102'
        ];

        $qs = Vii::queryStringBuilder($request->getQueryString());

        
        return view(
            'Backend::setting.dimension',
            [
                'page_title' => 'Dimention',
                'data' => $data,
                'qs' => $qs,
                'type_list' => $type_list
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }

    public function getMappingsItem(Request $request){


        return view(
            'Backend::setting.mappings-item',
            [
                'page_title' => 'Mapping Item',
                // 'data' => $data,
                // 'qs' => $qs,
                // 'type_list' => $type_list
                //'user' => session()->get('test-name', $full_name)
            ]
        );
    }


}