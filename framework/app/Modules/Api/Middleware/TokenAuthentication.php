<?php   namespace App\Modules\Api\Middleware;


use Illuminate\Support\Facades\Auth;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth; 


class TokenAuthentication {



    public function __construct(){

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $base_url = $request->getBaseUrl();

        if(preg_match('/(\/api|\/api\/)$/', $base_url, $match)){

//            if(!$request->expectsJson()){
//                return response()->json(['error' => 'expects_respond_with_json_only'], 401);
//            }

            $token = $request->input('token', null);
            if(!$token){
                $token = JWTAuth::getToken();
                //    return response()->json(['error_code' => 'INVALID_TOKEN', 'message' => 'The token is incorrect.'], 401);
            }

            try{
                $apl = JWTAuth::getPayload($token)->toArray();
            }
            catch(\Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
                throw $e;
            }
            catch(\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e){
                throw $e;
            }
            catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                throw $e;
            }            
            catch(\Tymon\JWTAuth\Exceptions\JWTException $e){
                throw $e;
            }

        }

        return $next($request);
    }
}