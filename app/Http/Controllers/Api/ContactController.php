<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contacts;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function send(Request $request) {
        $rules = array(
            'name' => 'required',
            'email' => 'required|max:50',
            'number' => 'required',
            'message' => 'required',
        );
        $messages = array(
            'name.required' => 'Please, enter name!',
            'email.required' => 'Please, enter email!',
            'number.required' => 'Please, enter number!',
            'message.required' => 'Please, enter your message!',
        );
        $validator = Validator::make($request->input(), $rules, $messages);
        if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
        $form = new Contacts;
        $form->name = $request->name;
        $form->email = $request->email;
        $form->number = $request->number;
        $form->message = $request->message;
        $save = $form->save();
        if(!$save) {
            return response()->json(['message' => array('Something went wrong, please try again later!'), 'status' => 500], 500);
        } else {
            return response()->json(['message' => array('You have succesfully created new playlist!'), 'status' => 200], 200);
        }

    }
}
