<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\Users;

class AuthorizationController extends Controller
{
    public function store(Request $request){
        $rules = array(
            'name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'password' => 'required|max:50',
            'conf_password' => 'required|max:50',
        );
        $messages = array(
            'name.required' => 'Please, enter your name',
            'email.required' => 'Please, enter your email',
            'password.required' => 'Please, enter your password',
            'conf_password.required' => 'Please, confirm your password',
        );
        $validator = Validator::make($request->input(), $rules, $messages);
       if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors], 422);
        }
        if($request->password != $request->conf_password){
            return response()->json(['message' => 'Passwords are not the same!'], 422);
        }
        if(Users::where('email', $request->email)->first()){
            return response()->json(['message' => 'Email already exists in database!'], 500);
        }
            $user = new Users;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->image = 'Some image';
            $save = $user->save();
            if(!$save){
                return response()->json(['message' => 'Someething went wrong, please try again later!'], 500);
            }else{
                return response()->json(['message' => 'You have succesfully registered to Edutech', 'data' => $user]);
        }
    }
}