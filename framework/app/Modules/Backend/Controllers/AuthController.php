<?php   namespace App\Modules\Backend\Controllers;

use Auth;
//use Config;
//use Request;
use Illuminate\Http\Request;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AuthController extends \Illuminate\Routing\Controller{
    
    use DispatchesJobs, ValidatesRequests;

    private static $langName = 'auth';

    private $guard;

    //private $baseUrl = 'admin/';
    private static $BASE_URL = '/';
    
    public function __construct(){
        
        $this->guard = Auth::guard('admin');

        app()->setLocale(config('backend.lang'));
                    
        view()->share('css', config('backend.css'));
        view()->share('js', config('backend.js'));
                
        $start = config('backend.start_year');
        $copy_year = (date('Y') == $start) ? $start : $start . '-' . date('Y');
        view()->share('copy_right_year', $copy_year);

        view()->share('lang_mod', strtolower(config('module.backend.folder_name')) . '/' . self::$langName);
    }
    
    public function postLogin(Request $request){
        
        $credentials = [
            'email' => $request->post('email'),
            'password' => $request->post('password')
        ];
        
        $remember_me = false;
        if($request->has('remember_me') && $request->input('remember_me') == 1)
            $remember_me = true;
       
        if($this->guard->attempt($credentials, $remember_me)){
            $admin = $this->guard->user();
            if($admin->status == 0){
                $this->guard->logout();
                return redirect()
                    ->route('backend-login')
                    ->with('message-error', 'User is not actived.');
            }
            
            return redirect()->route('dashboard');
        }
        
        
        return redirect()
            ->route('backend-login')
            ->with('message-error', 'The credentials is not found.');

    }
    
    public function getLogin(Request $request){
        
         return view(
            'Backend::user.login',[
                'login_or_lock' => 'login-page'
            ]
        );
    }
    
    public function getLogout(){
        $this->guard->logout();
        session()->flush();
        return redirect()->guest(route('backend-login'));
    }
}