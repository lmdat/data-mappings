<?php
namespace App\Modules\Backend\Controllers;


use Illuminate\Support\Facades\DB;
use App\Models\Ledger;

class DashboardController extends Controller{
    const LANG_NAME = 'dashboard';

    public function __construct(){
        parent::__construct();

        view()->share('lang_mod', $this->mod . '/' . self::LANG_NAME);

        $actions = request()->route()->getAction();
        $this->prefixUrl = $actions['prefix'];


    }

    public function welcome(){

        $full_name = $this->guard->user()->first_name . ' ' . $this->guard->user()->last_name;
        // dd(bcrypt('123456'));
        // dd($this->guard->user()->maxRole());
        
        return view(
            'Backend::dashboard.welcome',
            [
                'page_title' => 'Dashboard',
                'user' => $full_name//session()->get('test-name', $full_name)
            ]
        );
    }
}