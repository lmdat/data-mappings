<?php   namespace App\Modules\Backend\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class BackendAuthorization{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $guard;

    protected $guardName;
    
    private $roleAction;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct()
    {
        $this->guardName = 'admin';
        $this->guard = Auth::guard($this->guardName);
        $this->roleAction = 'roles';

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role='')
    {

        if (!$this->guard->check()) {
            if ($request->ajax()) {
                return response()->json(['error' => "Unauthorized."], 403);
            } else {
                return redirect()->guest(route('backend-login'));
            }
        }

        $roles = $this->getRolesOfRoute($request);

        // dd($roles);

        if ($request->user($this->guardName)->hasRole($roles) || $roles == '*' || !$roles) {
            return $next($request);
        }

        //return response('Insufficient Permissions', 403);
        abort(403, 'Insufficient Permissions.');

    }

    private function getRolesOfRoute($request){
        $actions = $request->route()->getAction();

        if(!isset($actions[$this->roleAction]))
            return null;

        $roles = $actions[$this->roleAction];
        if(is_string($roles) && $roles == '*')
            return $roles;

        if(is_array($roles)){
            if(count($roles) > 0){
                if($roles[0] == '*'){
                    return '*';
                }
                
                
                arsort($roles);
            }
        }

        return $roles;
    }
}