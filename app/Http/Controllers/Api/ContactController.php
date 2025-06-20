<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contacts;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index()
    {
        try {
            $messages = Contacts::orderBy('created_at', 'desc')->get();
            return response()->json([
                'status' => 200,
                'message' => 'Ziņojumi veiksmīgi ielādēti',
                'data' => $messages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās ielādēt ziņojumus',
                'data' => null
            ], 500);
        }
    }

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
        $form->status = 'jauns';
        $save = $form->save();
        if(!$save) {
            return response()->json(['message' => array('Kaut kas nogāja greizi, mēģiniet vēlreiz!'), 'status' => 500], 500);
        } else {
            return response()->json(['message' => array('Jūsu ziņa ir veiksmīgi nosūtīta!'), 'status' => 200], 200);
        }
    }

    public function mark_as_read($id)
    {
        try {
            $message = Contacts::find($id);
            if (!$message) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Ziņojums nav atrasts',
                    'data' => null
                ], 404);
            }

            $message->update(['status' => 'atvērts']);

            return response()->json([
                'status' => 200,
                'message' => 'Ziņojums atzīmēts kā atvērts',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās atzīmēt ziņojumu kā atvērtu',
                'data' => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $message = Contacts::find($id);
            if (!$message) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Ziņojums nav atrasts',
                    'data' => null
                ], 404);
            }

            $message->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Ziņojums veiksmīgi dzēsts',
                'data' => null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās dzēst ziņojumu',
                'data' => null
            ], 500);
        }
    }

    public function get_message_status_distribution()
    {
        $statuses = Contacts::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $data = [
            'labels' => ['jauns', 'atvērts', 'apstrāde', 'pabeigts'],
            'data' => [
                $statuses->where('status', 'jauns')->first()->count ?? 0,
                $statuses->where('status', 'atvērts')->first()->count ?? 0,
                $statuses->where('status', 'apstrāde')->first()->count ?? 0,
                $statuses->where('status', 'pabeigts')->first()->count ?? 0
            ]
        ];

        return response()->json($data);
    }

    public function get_unread_messages()
    {
        $messages = Contacts::where('status', 'jauns')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'name' => $message->name,
                    'email' => $message->email,
                    'created_at' => $message->created_at->format('d.m.Y H:i')
                ];
            });

        return response()->json($messages);
    }

    public function update_status(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:jauns,atvērts,apstrāde,pabeigts'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validācijas kļūda!',
                    'errors' => $validator->errors()
                ], 422);
            }

            $message = Contacts::find($id);
            if (!$message) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Ziņojums nav atrasts!',
                    'data' => null
                ], 404);
            }

            $message->update(['status' => $request->status]);

            return response()->json([
                'status' => 200,
                'message' => 'Statuss veiksmīgi rediģēts!',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās rediģēt statusu!',
                'data' => null
            ], 500);
        }
    }
}
