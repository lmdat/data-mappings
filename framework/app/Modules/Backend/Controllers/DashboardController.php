<?php
namespace App\Modules\Backend\Controllers;

class DashboardController extends Controller{
    const LANG_NAME = 'dashboard';

    public function __construct(){
        parent::__construct();

        view()->share('lang_mod', $this->mod . '/' . self::LANG_NAME);

        $actions = request()->route()->getAction();
        $this->prefixUrl = $actions['prefix'];


    }

    public function welcome(){

        $full_name = 'Vincent Valentine';//$this->guard->user()->first_name . ' ' . $this->guard->user()->surname;
        session()->flush();
        return view(
            'Backend::dashboard.welcome',
            [
                'page_title' => 'Dashboard',
                'user' => session()->get('test-name', $full_name)
            ]
        );
    }
}