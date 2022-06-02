<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        
        $validator=Validator::make($request->all(),[
            'name'=>['required','unique:users,name'],
            'email'=>['required','email','unique:users,email'],
            'recoveryEmail'=>['required','email'],
            'password'=>['required','confirmed']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors() , 422);
        }
        $userlogins['name']=$request->name;
        $userlogins['email']=$request->email;
        $userlogins['recoveryEmail']=$request->recoveryEmail;
        $userlogins['password']=bcrypt($request->password);
        $user=User::create($userlogins);
        

        $token=$user->createToken('myapptoken')->plainTextToken;
        $user->remember_token=$token;
        $user->save();
        $response=[
            'user'=>$user,
            'token'=>$token
        ];
        return response($response,201);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>['required','email'],
            'password'=>['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors() , 422);
        }
        //checkemail
        $user=User::where('email',$request->email)->first();
        if(!$user || !Hash::check($request->password,$user->password)){
            return response([
                'message'=>'credentials are not matching'
            ],401);
        }
        $token=$user->createToken('myapptoken')->plainTextToken;
        $user->remember_token=$token;
        $user->save();

        return response([
            'user'=>$user,
            'token'=>$token
        ]);
    }
    
    public function logout(Request $request){
        $user=Auth::user();
        $user->remember_token=0;
        $user->currentAccessToken()->delete();
        
        return[
            'message'=>'user has been logged out'
        ];
    }

}
