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
    private const DEFAULT_IMAGE = '/storage/app/public/default.png';
    private const UPLOAD_PATH = 'uploads';

    public function registration_store(Request $request)
    {
        try {
            $validator = $this->validateRegistration($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            if ($request->password !== $request->conf_password) {
                return $this->errorResponse(['Passwords do not match']);
            }

            if (Users::where('email', $request->email)->exists()) {
                return $this->errorResponse(['Email already exists in database. Try to login!']);
            }

            $user = new Users();
            $user->fill([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $this->handleImageUpload($request->image)
            ]);

            $user->save();

            return $this->successResponse(
                'Jūs veiksmīgi esat reģistrējusies Edutech platformā!',
                ['data' => $user]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(['Reģistrācija neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function login_store(Request $request)
    {
        try {
            $validator = $this->validateLogin($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            $user = Users::where('email', $request->email)->first();
            $teacher = Teachers::where('email', $request->email)->first();

            if (!$user && !$teacher) {
                return $this->errorResponse(['Nepareizs e-pasts vai parole!']);
            }

            if ($user && Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
                if ($user->status === 'neaktīvs') {
                    return $this->errorResponse(['Jūsu konts ir deaktivizēts. Lūdzu, sazinieties ar administratoru.'], 403);
                }
                $token = $user->createToken('MyApp')->plainTextToken;
                return $this->successResponse('Autentifikācija veiksmīga!', [
                    'data' => $user,
                    'token' => $token,
                    'is_teacher' => false
                ]);
            }

            if ($teacher && Auth::guard('teacher')->attempt(['email' => $request->email, 'password' => $request->password])) {
                if ($teacher->status === 'neaktīvs') {
                    return $this->errorResponse(['Jūsu konts ir deaktivizēts. Lūdzu, sazinieties ar administratoru.'], 403);
                }
                $token = $teacher->createToken('MyApp')->plainTextToken;
                return $this->successResponse('Autentifikācija veiksmīga!', [
                    'data' => $teacher,
                    'token' => $token,
                    'is_teacher' => true
                ]);
            }

            return $this->errorResponse(['Nepareizs e-pasts vai parole!']);
        } catch (\Exception $e) {
            return $this->errorResponse(['Autentifikācija neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function update_store(Request $request)
    {
        try {
            $validator = $this->validateUpdate($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            $user = Users::where('email', $request->old_email)->firstOrFail();

            if ($this->shouldChangePassword($request)) {
                $passwordValidator = $this->validatePasswordChange($request);
                if ($passwordValidator->fails()) {
                    return $this->errorResponse($passwordValidator->messages()->all());
                }

                if (!Hash::check($request->p_password, $user->password)) {
                    return $this->errorResponse(['Iepriekšējā parole nav pareiza!']);
                }

                if ($request->n_password !== $request->c_password) {
                    return $this->errorResponse(['Jaunas paroles nesakrīt!']);
                }

                $user->password = Hash::make($request->n_password);
            }

            if ($request->email !== $request->old_email && Users::where('email', $request->email)->exists()) {
                return $this->errorResponse(['E-pasts jau eksistē!']);
            }

            $user->fill([
                'name' => $request->name,
                'email' => $request->email,
                'image' => $request->hasFile('image') ? $this->handleImageUpload($request->image) : $user->image
            ]);

            $user->save();

            return $this->successResponse(
                'Profils veiksmīgi rediģēts!',
                ['data' => $user]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(['Rediģēšana neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function admin_update_store(Request $request)
    {
        try {
            $validator = $this->validateAdminUpdate($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            $teacher = Teachers::where('email', $request->old_email)->firstOrFail();

            if ($this->shouldChangePassword($request)) {
                $passwordValidator = $this->validatePasswordChange($request);
                if ($passwordValidator->fails()) {
                    return $this->errorResponse($passwordValidator->messages()->all());
                }

                if (!Hash::check($request->p_password, $teacher->password)) {
                    return $this->errorResponse(['Iepriekšējā parole nav pareiza!']);
                }

                if ($request->n_password !== $request->c_password) {
                    return $this->errorResponse(['Jaunas paroles nesakrīt!']);
                }

                $teacher->password = Hash::make($request->n_password);
            }

            if ($request->email !== $request->old_email && Teachers::where('email', $request->email)->exists()) {
                return $this->errorResponse(['E-pasts jau eksistē!']);
            }

            $teacher->fill([
                'name' => $request->name,
                'email' => $request->email,
                'profession' => $request->profession,
                'image' => $request->hasFile('image') ? $this->handleImageUpload($request->image) : $teacher->image
            ]);

            $teacher->save();

            return $this->successResponse(
                'Profils veiksmīgi rediģēts!',
                ['data' => $teacher]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(['Rediģēšana neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function admin_registration_store(Request $request)
    {
        try {
            $validator = $this->validateAdminRegistration($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            if ($request->password !== $request->conf_password) {
                return $this->errorResponse(['Paroles nesakrīt!']);
            }

            if (Teachers::where('email', $request->email)->exists()) {
                return $this->errorResponse(['E-pasts jau eksistē!']);
            }

            if (Users::where('email', $request->email)->exists()) {
                return $this->errorResponse(['E-pasts jau eksistē lietotāja datubāzē!']);
            }

            $teacher = new Teachers();
            $teacher->fill([
                'name' => $request->name,
                'email' => $request->email,
                'profession' => $request->profession,
                'password' => Hash::make($request->password),
                'image' => $this->handleImageUpload($request->image)
            ]);

            $teacher->save();

            return $this->successResponse(
                'Pasniedzējs veiksmīgi reģistrēts!',
                ['data' => $teacher]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(['Reģistrācija neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->errorResponse(['Lietotājs nav atrasts!'], 404);
            }

            return $this->successResponse('Profils veiksmīgi atgriezts!', [
                'data' => [
                    'id' => $user->id,
                    'encrypted_id' => $user->encrypted_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'image' => $user->image
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse(['Profils atgriešana neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->errorResponse(['Lietotājs nav atrasts!'], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id . ',id',
                'old_password' => 'required_with:new_password',
                'new_password' => 'nullable|min:6',
                'confirm_password' => 'required_with:new_password|same:new_password',
                'image' => 'nullable|image|max:2048'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->all(), 422);
            }

            if ($request->filled('old_password')) {
                if (!Hash::check($request->old_password, $user->password)) {
                    return $this->errorResponse(['Pašreizējā parole nav pareiza!'], 422);
                }
                $user->password = Hash::make($request->new_password);
            }

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->hasFile('image')) {
                if ($user->image && $user->image !== self::DEFAULT_IMAGE) {
                    Storage::delete(str_replace('/storage/', '', $user->image));
                }
                $user->image = $this->handleImageUpload($request->file('image'));
            }

            $user->save();

            return $this->successResponse('Profils veiksmīgi rediģēts!', ['data' => $user]);
        } catch (\Exception $e) {
            return $this->errorResponse(['Rediģēšana neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function index()
    {
        try {
            $users = Users::all()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at
                ];
            });

            return response()->json([
                'status' => 200,
                'message' => 'Lietotāji veiksmīgi ielādēti',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās ielādēt lietotājus',
                'data' => null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|in:admin,teacher,student'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validācijas kļūda',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Users::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role
            ]);

            return response()->json([
                'status' => 201,
                'message' => 'Lietotājs veiksmīgi pievienots',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās pievienot lietotāju',
                'data' => null
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Users::find($id);
            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lietotājs nav atrasts',
                    'data' => null
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required|in:admin,teacher,student'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validācijas kļūda',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Lietotājs veiksmīgi atjaunināts',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās atjaunināt lietotāju',
                'data' => null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Users::find($id);
            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lietotājs nav atrasts',
                    'data' => null
                ], 404);
            }

            $user->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Lietotājs veiksmīgi dzēsts',
                'data' => null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās dzēst lietotāju',
                'data' => null
            ], 500);
        }
    }

    private function handleImageUpload($image)
    {
        if (!$image) {
            return self::DEFAULT_IMAGE;
        }

        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs(self::UPLOAD_PATH, $imageName, 'public');
        return '/storage/app/public/' . $imagePath;
    }

    private function shouldChangePassword(Request $request)
    {
        return $request->filled(['p_password', 'n_password', 'c_password']);
    }

    private function validateRegistration(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'password' => 'required|max:50',
            'conf_password' => 'required|max:50'
        ], [
            'name.required' => 'Ievadiet savu vārdu',
            'email.required' => 'Ievadiet savu e-pastu',
            'password.required' => 'Ievadiet savu paroli',
            'conf_password.required' => 'Apstipriniet savu paroli'
        ]);
    }

    private function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email|max:50',
            'password' => 'required|max:50'
        ], [
            'email.required' => 'Ievadiet savu e-pastu',
            'password.required' => 'Ievadiet savu paroli'
        ]);
    }

    private function validateUpdate(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|max:50'
        ], [
            'name.required' => 'Ievadiet savu vārdu',
            'email.required' => 'Ievadiet savu e-pastu'
        ]);
    }

    private function validateAdminUpdate(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:50',
            'profession' => 'required',
            'email' => 'required|email|max:50'
        ], [
            'name.required' => 'Ievadiet savu vārdu',
            'profession.required' => 'Izvēlieties savu profesiju',
            'email.required' => 'Ievadiet savu e-pastu'
        ]);
    }

    private function validateAdminRegistration(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:50',
            'profession' => 'required',
            'email' => 'required|email|max:50',
            'password' => 'required|max:50',
            'conf_password' => 'required|max:50'
        ], [
            'name.required' => 'Ievadiet savu vārdu',
            'profession.required' => 'Izvēlieties savu profesiju',
            'email.required' => 'Ievadiet savu e-pastu',
            'password.required' => 'Ievadiet savu paroli',
            'conf_password.required' => 'Apstipriniet savu paroli'
        ]);
    }

    private function validatePasswordChange(Request $request)
    {
        return Validator::make($request->all(), [
            'p_password' => 'required|max:50',
            'n_password' => 'required|max:50',
            'c_password' => 'required|max:50'
        ], [
            'p_password.required' => 'Ievadiet savu iepriekšējo paroli',
            'n_password.required' => 'Ievadiet savu jauno paroli',
            'c_password.required' => 'Apstipriniet savu jauno paroli'
        ]);
    }

    private function errorResponse(array $messages, int $status = 500)
    {
        return response()->json([
            'message' => $messages,
            'status' => $status
        ], $status);
    }

    private function successResponse(string $message, array $data = [], int $status = 200)
    {
        return response()->json(array_merge([
            'message' => [$message],
            'status' => $status
        ], $data), $status);
    }
}