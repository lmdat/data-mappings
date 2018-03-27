<?php namespace App\Modules\Backend\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

use Illuminate\Contracts\Auth\Guard;


class CompanySelection{
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
        if (!$this->guard->check()) {
            if ($request->ajax()) {
                return response()->json(['error' => "Unauthorized."], 403);
            } else {
                return redirect()->guest(route('backend-login'));
            }
        }

        $action = $request->route()->getAction();
        // dd($request->route()->getName());
        if(isset($action['skip_com_selection']) && $action['skip_com_selection'] == true)
            return $next($request);
        
        if(!session()->has('selected_company')){
            return redirect()->route('company-get-select');
        }

        
        return $next($request);
    }
}