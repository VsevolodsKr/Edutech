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
use App\Models\Teachers;

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
        $teacher = Teachers::where('email', $request->email)->first();
        if(!$user && !$teacher){
            return response()->json(['message' => array('Provided email does not exist in database. Try to register!'), 'status' => 500], 500);
        }
        if(Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])){
            $token = $user->createToken('MyApp')->plainTextToken;
            return response()->json(['message' => array('You succesfully logged in!'), 'status' => 200, 'data' => $user, 'token' => $token, 'is_teacher' => false], 200);
        }else if(Auth::guard('teacher')->attempt(['email' => $request->email, 'password' => $request->password])){
            $token = $teacher->createToken('MyApp')->plainTextToken;
            return response()->json(['message' => array('You succesfully logged in!'), 'status' => 200, 'data' => $teacher, 'token' => $token, 'is_teacher' => true], 200);
        }else{
            if(!Hash::check($request->password, $user->password) || !Hash::check($request->password, $teacher->password)){
                return response()->json(['message' => array('Password is incorrect'), 'status' => 500], 500);
            }
        }
    }

    public function update_store(Request $request){
        $rules = array(
            'name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'p_password' => 'max:50',
            'n_password' => 'max:50',
            'c_password' => 'max:50',
        );
        $messages = array(
            'name.required' => 'Please, enter your name',
            'email.required' => 'Please, enter your email',
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
        $changePassword = false;
        if($request->p_password != '' || $request->n_password != '' || $request->c_password != ''){
            $password_rules = array(
                'p_password' => 'required|max:50',
                'n_password' => 'required|max:50',
                'c_password' => 'required|max:50',
            );
            $password_messages = array(
                'p_password.required' => 'Please, enter your previous password',
                'n_password.required' => 'Please, enter your new password',
                'c_password.required' => 'Please, confirm your password',
            );
            $validator = Validator::make($request->input(), $password_rules, $password_messages);
            if($validator->fails()){
                $errors = $validator->messages()->all();
                return response()->json(['message' => $errors, 'status' => 500], 500);
            }
            if(!Hash::check($request->p_password, $user->password)){
                return response()->json(['message' => array('Previous password does not match that is written in database!'), 'status' => 500], 500);
            }
            if($request->n_password != $request->c_password){
                return response()->json(['message' => array('Password does not confirmed!'), 'status' => 500], 500);
            }
            $changePassword = true;
        }
        if(Users::where('email', $request->email)->first() && $request->old_email != $request->email){
            return response()->json(['message' => array('Email already exists in database!'), 'status' => 500], 500);
        }
        if($changePassword){
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->n_password),
            ]);
        }else{
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }
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

    public function admin_update_store(Request $request){
        $rules = array(
            'name' => 'required|max:50',
            'profession' => 'required',
            'email' => 'required|email|max:50',
            'p_password' => 'max:50',
            'n_password' => 'max:50',
            'c_password' => 'max:50',
        );
        $messages = array(
            'name.required' => 'Please, enter your name',
            'profession.required' => 'Please, select your profession',
            'email.required' => 'Please, enter your email',
        );
        $validator = Validator::make($request->input(), $rules, $messages);
        if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
        $teacher = Teachers::where('email', $request->old_email)->first();
        if(!$teacher){
            return response()->json(['message' => array('Provided email does not exist in database. Try to register!'), 'status' => 500], 500);
        }
        $changePassword = false;
        if($request->p_password != '' || $request->n_password != '' || $request->c_password != ''){
            $password_rules = array(
                'p_password' => 'required|max:50',
                'n_password' => 'required|max:50',
                'c_password' => 'required|max:50',
            );
            $password_messages = array(
                'p_password.required' => 'Please, enter your previous password',
                'n_password.required' => 'Please, enter your new password',
                'c_password.required' => 'Please, confirm your password',
            );
            $password_validator = Validator::make($request->input(), $password_rules, $password_messages);
            if($password_validator->fails()){
                $password_errors = $password_validator->messages()->all();
                return response()->json(['message' => $password_errors, 'status' => 500], 500);
            }
            if(!Hash::check($request->p_password, $teacher->password)){
                return response()->json(['message' => array('Previous password does not match that is written in database!'), 'status' => 500], 500);
            }
            if($request->n_password != $request->c_password){
                return response()->json(['message' => array('Password does not confirmed!'), 'status' => 500], 500);
            }
            $changePassword = true;
        }
        if(Teachers::where('email', $request->email)->first() && $request->old_email != $request->email){
            return response()->json(['message' => array('Email already exists in database!'), 'status' => 500], 500);
        }
        if($request->profession == ''){
            return response()->json(['message' => array('Please, select your profession'), 'status' => 500], 500);
        }
        if($changePassword){
            $teacher->update([
                'name' => $request->name,
                'profession' => $request->profession . ' teacher',
                'email' => $request->email,
                'password' => Hash::make($request->n_password),
            ]);
        }else{
            $teacher->update([
                'name' => $request->name,
                'profession' => $request->profession . ' teacher',
                'email' => $request->email,
            ]);
        }
        $save = $teacher->save();
        if($request->image){
            $image_name = time() . '_' . $request->image->getClientOriginalName();
            $image_path = $request->image->storeAs('uploads', $image_name, 'public');
            $image = '/storage/app/public/' . $image_path;
            $teacher->update([
                'image' => $image
            ]);
            $save = $teacher->save();
        }else{
            $image = '/storage/app/public/default.png';
            $teacher->update([
                'image' => $image
            ]);
            $save = $teacher->save();
        }
        if(!$save){
            return response()->json(['message' => array('Something went wrong, please try again later!'), 'status' => 500], 500);
        }else{
            return response()->json(['message' => array('You have succesfully updated your profile'), 'data' => $teacher, 'status' => 200], 200);
    }
    }

    public function admin_registration_store(Request $request){
        $rules = array(
            'name' => 'required|max:50',
            'profession' => 'required',
            'email' => 'required|email|max:50',
            'password' => 'required|max:50',
            'conf_password' => 'required|max:50',
        );
        $messages = array(
            'name.required' => 'Please, enter your name!',
            'profession.required' => 'Please, select your profession!',
            'email.required' => 'Please, enter your email!',
            'password.required' => 'Please, enter your password!',
            'conf_password.required' => 'Please, confirm your password!',
        );
        $validator = Validator::make($request->input(), $rules, $messages);
       if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
        if($request->password != $request->conf_password){
            return response()->json(['message' => array('Passwords are not the same!'), 'status' => 500], 500);
        }
        if(Teachers::where('email', $request->email)->first()){
            return response()->json(['message' => array('Email already exists in database. Try to login!'), 'status' => 500], 500);
        }
        if(Users::where('email', $request->email)->first()){
            return response()->json(['message' => array('Provided email already exists in User database. Change email!'), 'status' => 500], 500);
        }
        if($request->profession == ''){
            return response()->json(['message' => array('Please, select your profession!'), 'status' => 500], 500);
        }
            $teacher = new Teachers;
            $teacher->name = $request->name;
            $teacher->profession = $request->profession . ' teacher';
            $teacher->email = $request->email;
            $teacher->password = Hash::make($request->password);
            if($request->image){
                $image_name = time() . '_' . $request->image->getClientOriginalName();
                $image_path = $request->image->storeAs('uploads', $image_name, 'public');
                $teacher->image = '/storage/app/public/' . $image_path;
                $save = $teacher->save();
            }else{
                $teacher->image = '/storage/app/public/default.png';
                $save = $teacher->save();
            }
            if(!$save){
                return response()->json(['message' => array('Something went wrong, please try again later!'), 'status' => 500], 500);
            }else{
                return response()->json(['message' => array('You have succesfully registered to Edutech'), 'data' => $teacher, 'status' => 200], 200);
        }
    }
}