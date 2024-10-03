<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Users;

class AuthorizationController extends Controller
{
    public function registration_store(Request $request){
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
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
        if($request->password != $request->conf_password){
            return response()->json(['message' => array('Passwords are not the same!'), 'status' => 500], 500);
        }
        if(Users::where('email', $request->email)->first()){
            return response()->json(['message' => array('Email already exists in database. Try to login!'), 'status' => 500], 500);
        }
            $user = new Users;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            if($request->image){
                $image = $request->file('image');
                $extension = $image->getClientOriginalExtension();
                $name = time() .'_'. $image->getClientOriginalName();
                Storage::disk('public')->put($name, File::get($image));
                $user->image = $name;
                $save = $user->save();
            }else{
                $user->image = 'default.png';
                $save = $user->save();
            }
            if(!$save){
                return response()->json(['message' => array('Something went wrong, please try again later!'), 'status' => 500], 500);
            }else{
                return response()->json(['message' => array('You have succesfully registered to Edutech'), 'data' => $user, 'status' => 200], 200);
        }
    }

    public function login_store(Request $request){
        $rules = array(
            'email' => 'required|email|max:50',
            'password' => 'required|max:50',
        );
        $messages = array(
            'email.required' => 'Please, enter your email',
            'password.required' => 'Please, enter your password',
        );
        $validator = Validator::make($request->input(), $rules, $messages);
        if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
        $user = Users::where('email', $request->email)->first();
        if(!$user){
            return response()->json(['message' => array('Provided email does not exist in database. Try to register!'), 'status' => 500], 500);
        }else{
            if(Hash::check($request->password, $user->password)){
                return response()->json(['message' => array('You succesfully logged in!'), 'status' => 200, 'data' => $user], 200);
            }else{
                return response()->json(['message' => array('Password is incorrect'), 'status' => 500], 500);
            }
        }
    }
}