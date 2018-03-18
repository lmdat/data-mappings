<?php
namespace App\Modules\Backend\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SidebarMenuTrait;

    protected $guard;

    protected $guardName;

    protected $sidebarMenu;

    protected $mod;

    protected $langCommon;

    protected $prefixUrl;

    public function __construct(){

        //inject admin authentication
        //$this->middleware('admin_auth');

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
        // $this->middleware(function ($request, $next) {
        //     view()->share('full_name', $this->guard->user()->first_name . ' ' . $this->guard->user()->surname);
        //     view()->share('user_role', $this->guard->user()->getMaxRoleName());

        //     return $next($request);
        // });

    }

    private function init(){
        //$this->guardName = 'admin';
        //$this->guard = Auth::guard($this->guardName);
        $this->prefixUrl = '';
        $this->mod = strtolower(config('module.backend.folder_name'));
        $this->langCommon = $this->mod . '/common';

        $this->sidebarMenu = $this->createMenu();

    }

    protected function guard(){
        return Auth::guard($this->guardName);

    }
}