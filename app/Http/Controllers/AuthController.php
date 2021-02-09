<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuth;

class AuthController extends Controller
{
    //
    public $loginAfterSignUp=true;

     public function login(Request $request){
        $credentials=$request->only("email","password");
        $token=null;

        if(!$token=JWTAuth::attempt($credentials)){
            return response()->json([
                "status"=>false,
                "message"=>"Unauthorized"
            ]);
        }
        return response()->json([
            'status'=>true,
            'token'=>$token
        ]);
    } 

    public function register(Request $request){
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users',
            'phone'=>'required|string',
            'code'=>'required|string',
            'password'=>'required|string|min:6|max:12'
        ]);
        $user=new User();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->phone=$request->phone;
        $user->code=$request->code;
        $user->password=bcrypt($request->password);
        $user->save();

/*         if($this->loginAfterSignUp){
            return $this->login($request);
        } */
        return response()->json([
            "status"=>true,
            "user"=>$user
        ]);
    }
     public function logout(Request $request){
        $this->validate($request,[
            "token"=>"required"
        ]);
        try{
            JWTAuth::invalidate($request->token);
            return response()->json([
                "status"=>true,
                "message"=>"User logged out successfully"
            ]);
        }
        catch(JWTException $exception){
            return response()->json([
                "status"=>false,
                "message"=>"Oops, the user can not be logged out"
            ]);
        }
    } 
}
