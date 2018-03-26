<?php namespace App\Modules\Backend\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Config;

use Illuminate\Contracts\Auth\Guard;


class BackendAuth{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $guard;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct()
    {
        $this->guard = Auth::guard('admin');
        
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $base_url = $request->getBaseUrl();

        //base url end with cms or cms/
        if(preg_match('/(\/cpanel|\/cpanel\/)$/', $base_url, $match)){
        //if($request->is('admin') || $request->is('admin/*')){
            
            //POST and GET admin/login
//            if($request->is(Config::get('auth.authentication_url.admin.login'))){
//                return $next($request);
//            }

            if (!$this->guard->check()) { //$this->guard->guest() == true
                if ($request->ajax()) {
                    return response()->json(['error'=>"Unauthorized."], 401);
                } else {
                    return redirect()->guest(config('auth.authentication_url.admin.login'));
                }
            }

            return $next($request);
        }
        
        return $next($request);
    }
}