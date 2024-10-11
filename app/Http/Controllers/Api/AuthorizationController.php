<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
                $image_name = time() . '_' . $request->image->getClientOriginalName();
                $image_path = $request->image->storeAs('uploads', $image_name, 'public');
                $user->image = '/storage/app/public/' . $image_path;
                $save = $user->save();
            }else{
                $user->image = '/storage/app/public/default.png';
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
        }   
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $logged_user = Auth::user();
            $token = $logged_user->createToken('Logged')->plainTextToken;
            return response()->json(['message' => array('You succesfully logged in!'), 'status' => 200, 'data' => $user, 'token' => $token], 200);
        }else{
            if(!Hash::check($request->password, $user->password)){
                return response()->json(['message' => array('Password is incorrect'), 'status' => 500], 500);
            }
        }
    }

    public function update_store(Request $request){
        $rules = array(
            'name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'p_password' => 'required|max:50',
            'n_password' => 'required|max:50',
            'c_password' => 'required|max:50',
        );
        $messages = array(
            'name.required' => 'Please, enter your name',
            'email.required' => 'Please, enter your email',
            'p_password.required' => 'Please, enter your previous password',
            'n_password.required' => 'Please, enter your new password',
            'c_password.required' => 'Please, confirm your new password',
        );
        $validator = Validator::make($request->input(), $rules, $messages);
        if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
        $user = Users::where('email', $request->old_email)->first();
        if(!$user){
            return response()->json(['message' => array('Provided email does not exist in database. Try to register!'), 'status' => 500], 500);
        }
        if(!Hash::check($request->p_password, $user->password)){
            return response()->json(['message' => array('Previous password does not match that is written in database!'), 'status' => 500], 500);
        }
        if($request->n_password != $request->c_password){
            return response()->json(['message' => array('Password does not confirmed!'), 'status' => 500], 500);
        }
        if(Users::where('email', $request->email)->first() && $request->old_email != $request->email){
            return response()->json(['message' => array('Email already exists in database!'), 'status' => 500], 500);
        }
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->n_password),
        ]);
        $save = $user->save();
        if($request->image){
            $image_name = time() . '_' . $request->image->getClientOriginalName();
            $image_path = $request->image->storeAs('uploads', $image_name, 'public');
            $image = '/storage/app/public/' . $image_path;
            $user->update([
                'image' => $image
            ]);
            $save = $user->save();
        }else{
            $image = '/storage/app/public/default.png';
            $user->update([
                'image' => $image
            ]);
            $save = $user->save();
        }
        if(!$save){
            return response()->json(['message' => array('Something went wrong, please try again later!'), 'status' => 500], 500);
        }else{
            return response()->json(['message' => array('You have succesfully updated your profile'), 'data' => $user, 'status' => 200], 200);
    }
    }
}