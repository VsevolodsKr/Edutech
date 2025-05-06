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
            'name.required' => 'Ievadiet savu vārdu!',
            'email.required' => 'Ievadiet savu e-pasta adresi!',
            'number.required' => 'Ievadiet savu tālruņa numuru!',
            'message.required' => 'Ievadiet savu ziņu!',
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
            return response()->json(['message' => array('Kaut kas nogāja greizi, mēģiniet vēlreiz!'), 'status' => 500], 500);
        } else {
            return response()->json(['message' => array('Jūsu ziņa ir veiksmīgi nosūtīta!'), 'status' => 200], 200);
        }

    }
}
