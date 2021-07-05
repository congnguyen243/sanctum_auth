<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validated = Validator::make($request->all(),[
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        if($validated->fails()){
            return response()->json(['msg'=>$validated->errors()]);
        }
        $param = $validated->validated();
        $user = User::create(array_merge(
            $validated->validated(),
            ['password'=> bcrypt($request->password)] 
        ));
        return response()->json(['user'=>$user,'msg'=>'register ok']);
       
    }

    public function login(Request $request){
        $validated = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        if ($validated->fails()) {
            return response()->json([$validated->errors(), 422]);
        }

        if (!$token = auth()->attempt($validated->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        else{
            $user = auth()->user();
            $token = $user->createToken($user->email)->plainTextToken;
            return response()->json(['user' => $user, 'msg' => 'dang nhap ok', 'token' => $token]);
        }
    }

    public function getMe(){
        $user = auth()->user();
        return response()->json(['user'=>$user]);
    }
}
