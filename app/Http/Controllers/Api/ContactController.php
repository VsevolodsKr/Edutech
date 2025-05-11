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

    public function reply(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reply' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validācijas kļūda',
                    'errors' => $validator->errors()
                ], 422);
            }

            $message = Contacts::find($id);
            if (!$message) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Ziņojums nav atrasts',
                    'data' => null
                ], 404);
            }

            $message->update([
                'reply' => $request->reply,
                'status' => 'replied'
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Atbilde veiksmīgi nosūtīta',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās nosūtīt atbildi',
                'data' => null
            ], 500);
        }
    }

    public function markAsRead($id)
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

    public function getMessageStats()
    {
        $stats = [
            'total' => Contacts::count(),
            'unread' => Contacts::where('status', 'unread')->count(),
            'in_progress' => Contacts::whereIn('status', ['replied', 'read'])->count(),
            'completed' => Contacts::where('status', 'replied')->count()
        ];

        return response()->json($stats);
    }

    public function getMessageStatusDistribution()
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

    public function getUnreadMessages()
    {
        $messages = Contacts::where('status', 'unread')
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

    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:jauns,atvērts,apstrāde,pabeigts'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validācijas kļūda',
                    'errors' => $validator->errors()
                ], 422);
            }

            $message = Contacts::find($id);
            if (!$message) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Ziņojums nav atrasts',
                    'data' => null
                ], 404);
            }

            $message->update(['status' => $request->status]);

            return response()->json([
                'status' => 200,
                'message' => 'Statuss veiksmīgi atjaunināts',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās atjaunināt statusu',
                'data' => null
            ], 500);
        }
    }
}
