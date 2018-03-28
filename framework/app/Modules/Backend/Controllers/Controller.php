<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Auth;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SidebarMenuTrait;

    protected $guard;

    protected $guardName;

    protected $sidebarMenu;

    protected $mod;

    protected $langCommon;

    protected $prefixUrl;

    protected $companyId;

    protected $mime;

    public function __construct(){

        //inject admin authentication
        $this->middleware(['backend_auth', 'company_selection']);

        //Set language
        app()->setLocale(config('backend.default_lang'));

        $this->init();

        view()->share('css', config('backend.css'));
        view()->share('js', config('backend.js'));
        view()->share('sidebar_menu', $this->sidebarMenu);

        $start = config('backend.start_year');
        $copy_year = (date('Y') == $start) ? $start : $start . '-' . date('Y');
        view()->share('copy_right_year', $copy_year);
        view()->share('lang_common', $this->langCommon);

        

        //Use Session in constructor
        $this->middleware(function ($request, $next) {
            view()->share('full_name', $this->guard->user()->first_name . ' ' . $this->guard->user()->last_name);
            view()->share('user_role', $this->guard->user()->getMaxRoleName());

            $this->companyId = session()->get('selected_company');

            return $next($request);
        });

    }

    private function init(){
        $this->guardName = 'admin';
        $this->guard = Auth::guard($this->guardName);
        $this->prefixUrl = '';
        $this->mod = strtolower(config('module.backend.folder_name'));
        $this->langCommon = $this->mod . '/common';

        $this->sidebarMenu = $this->createMenu();

        $this->mime = [
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            // 'csv' => 'application/vnd.ms-excel'
            'csv' => 'text/plain'
        ];

    }

    protected function guard(){
        return Auth::guard($this->guardName);
    }

    protected function getTrueFileExtension($ufile){

        $ext = null;
        foreach($this->mime as $k =>$v){
            if($ufile->getMimeType() == trim($v)){
                $ext = $k;
                break;
            }
        }
        // if($ufile->getClientMimeType() == $this->mime['xlsx']){   // .xlsx
        //     $ext = 'xlsx';
        // }
        // else{
        //     if($ufile->getClientOriginalExtension() == $this->mime['xls']){    // .xls
        //         $ext = 'xls';
        //     }
        //     else{   // .csv
        //         $ext = 'csv';
        //     }
        // }
        return $ext;
    }

    protected function createReader($ext){
       
        $reader = null;
       
        if($ext == 'xlsx'){   // .xlsx
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }
        else if($ext == 'xls'){
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        }
        else{
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        }

        return $reader;
    }
}
