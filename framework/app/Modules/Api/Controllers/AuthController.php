<?php
namespace App\Modules\Api\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Models\User;

class AuthController extends BaseController{

   
    public function __construct(){
       
    }

    public function postLogin(Request $request){

        
        $credentials = $request->only(['email', 'password']);

        $rules = [
            'email'     => 'required|email',
            'password'  => 'required|min:6'
        ];

        $validator = Validator::make($credentials, $rules);

        if($validator->fails()){
            return response()->json(['success'=> false, 'error'=> $validator->messages()]);
        }

        // return response()->json(['success'=> true, 'error'=> "ERROR"]);

        try{
            if(!$token = auth('api')->attempt($credentials)){
                $user = User::where('email', $credentials['email'])->first();
                if($user == null){
                    return response()->json(['success' => false, 'error' => 'EMAIL_DOES_NOT_EXIST'], 401);    
                }
                
                return response()->json(['success' => false, 'error' => 'INCORRECT_PASSWORD'], 401);
            }
        }
        catch(JWTException $e){
            return response()->json(['success' => false, 'error' => 'FAILED_TO_LOGIN'], 500);
        }

        $user = auth('api')->user();

        return $this->respondWithToken($token, $user);
    }

    private function respondWithToken($token, $user){

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'full_name' => $user->first_name . ' ' . $user->last_name,
            'role_name' => $user->getMaxRoleName(),
            'role_power' => $user->maxRole()
        ]);
    }

    public function postPayload(){
        return auth('api')->payload();
    }

    public function postRefresh()
    {
        $user = auth('api')->user();
        return $this->respondWithToken(auth('api')->refresh(), $user);
    }

    public function postLogout(){
        auth('api')->logout();
        return response()->json(['success' => true, 'message' => 'LOGGED_OUT_SUCCESSFULLY']);
    }

}