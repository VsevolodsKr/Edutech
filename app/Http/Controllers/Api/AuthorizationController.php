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
use App\Models\Developers;

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
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validācijas kļūda',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Users::where('email', $request->email)->first();
        $teacher = Teachers::where('email', $request->email)->first();
        $developer = Developers::where('email', $request->email)->first();

        if ($user && $user->status === 'neaktīvs') {
            return response()->json([
                'status' => 403,
                'message' => 'Jūsu konts ir deaktivizēts'
            ], 403);
        }

        if ($teacher && $teacher->status === 'neaktīvs') {
            return response()->json([
                'status' => 403,
                'message' => 'Jūsu konts ir deaktivizēts'
            ], 403);
        }

        if ($developer && $developer->status === 'neaktīvs') {
            return response()->json([
                'status' => 403,
                'message' => 'Jūsu konts ir deaktivizēts'
            ], 403);
        }

        if ($user && Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 200,
                'message' => 'Veiksmīga autorizācija',
                'token' => $token,
                'is_teacher' => false,
                'is_developer' => false
            ]);
        }

        if ($teacher && Auth::guard('teacher')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $token = $teacher->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 200,
                'message' => 'Veiksmīga autorizācija',
                'token' => $token,
                'is_teacher' => true,
                'is_developer' => false
            ]);
        }

        if ($developer && Hash::check($request->password, $developer->password)) {
            $token = $developer->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 200,
                'message' => 'Veiksmīga autorizācija',
                'token' => $token,
                'is_teacher' => false,
                'is_developer' => true,
                'data' => [
                    'id' => $developer->id,
                    'name' => $developer->name,
                    'email' => $developer->email,
                    'image' => $developer->image ?: self::DEFAULT_IMAGE,
                    'status' => $developer->status
                ]
            ]);
        }

        return response()->json([
            'status' => 401,
            'message' => 'Nederīgs e-pasts vai parole'
        ], 401);
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

    public function get_users()
    {
        try {
            $users = Users::all();
            return response()->json([
                'status' => 200,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Kļūda ielādējot lietotājus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store_user(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'status' => 'required|in:aktīvs,neaktīvs',
                'image' => 'nullable|image|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validācijas kļūda',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $request->status,
                'image' => self::DEFAULT_IMAGE
            ];

            if ($request->hasFile('image')) {
                $userData['image'] = $this->handleImageUpload($request->file('image'));
            }

            $user = Users::create($userData);

            return response()->json([
                'status' => 201,
                'message' => 'Lietotājs veiksmīgi izveidots',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            \Log::error('User creation error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Kļūda izveidojot lietotāju',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update_user(Request $request, $id)
    {
        try {
            \Log::info('Update user request data:', $request->all());

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|string|min:6',
                'status' => 'required|in:aktīvs,neaktīvs',
                'image' => 'nullable|image|max:2048'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'status' => 422,
                    'message' => 'Validācijas kļūda',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Users::findOrFail($id);
            \Log::info('Found user:', $user->toArray());
            
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'status' => $request->status
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('image')) {
                // Delete old image if it exists and is not the default image
                if ($user->image && $user->image !== self::DEFAULT_IMAGE) {
                    Storage::delete(str_replace('/storage/', '', $user->image));
                }
                $userData['image'] = $this->handleImageUpload($request->file('image'));
            }

            \Log::info('Updating user with data:', $userData);
            $user->update($userData);
            \Log::info('User updated successfully:', $user->fresh()->toArray());

            return response()->json([
                'status' => 200,
                'message' => 'Lietotājs veiksmīgi atjaunināts',
                'data' => $user->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('User update error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 500,
                'message' => 'Kļūda atjauninot lietotāju',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete_user($id)
    {
        try {
            $user = Users::findOrFail($id);
            $user->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Lietotājs veiksmīgi dzēsts'
            ]);
        } catch (\Exception $e) {
            \Log::error('User deletion error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Kļūda dzēšot lietotāju',
                'error' => $e->getMessage()
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