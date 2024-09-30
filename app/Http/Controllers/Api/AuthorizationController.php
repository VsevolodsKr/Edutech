<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthorizationController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'password' => 'required|max:50',
            'conf_password' => 'required|max:50',
        ], [
            'username.required' => 'Please, enter your name',
            'email.required' => 'Please, enter your email',
            'password.required' => 'Please, enter your password',
            'conf_password.required' => 'Please, confirm your password'
        ]);
       if($validator->fails()){
            $errors = $validator->messages()->all();
            $error = implode("\n", $errors);
            throw ValidationException::withMessages([$error]);
        }

        if($request->password != $request->conf_password){
            throw ValidationException::withMessages(implode("\n", 'Passwords are not the same!'));
        }

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->image = 'Some image';
            // $user->save();
        //     if(!$save){
        //         throw ValidationException::withMessages('Something went wrong. Please try again later');
        //     }else{
        //         return response()->json([
        //             'message' => 'You have successfully registered!',
        //             'data' => $user,
        //     ]);
        // }
    }
}
