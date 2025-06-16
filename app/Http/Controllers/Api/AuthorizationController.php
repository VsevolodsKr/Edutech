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
use App\Traits\Encryptable;
use Illuminate\Support\Facades\DB;
use App\Models\Likes;
use App\Models\Comments;
use App\Models\Bookmarks;

class AuthorizationController extends Controller
{
    use Encryptable;

    private const DEFAULT_IMAGE = '/storage/app/public/default.png';
    private const UPLOAD_PATH = 'uploads';

    public function registration_store(Request $request)
    {
        try {
            $validator = $this->validateRegistration($request);
            if ($validator->fails()) {
                return $this->error_response($validator->messages()->all());
            }

            if ($request->password !== $request->conf_password) {
                return $this->error_response(['Paroles nesakrīt!']);
            }

            if (Users::where('email', $request->email)->exists()) {
                return $this->error_response(['E-pasts jau eksistē. Pamēģiniet ielogoties!']);
            }

            $user = new Users();
            $user->fill([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $this->handle_image_upload($request->image)
            ]);

            $user->save();

            return $this->successResponse(
                'Jūs veiksmīgi esat reģistrējusies Edutech platformā!',
                ['data' => $user]
            );
        } catch (\Exception $e) {
            return $this->error_response(['Reģistrācija neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
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
                'is_developer' => false,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'image' => $user->image ?: self::DEFAULT_IMAGE,
                    'status' => $user->status
                ]
            ]);
        }

        if ($teacher && Auth::guard('teacher')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $token = $teacher->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 200,
                'message' => 'Veiksmīga autorizācija',
                'token' => $token,
                'is_teacher' => true,
                'is_developer' => false,
                'data' => [
                    'id' => $teacher->id,
                    'encrypted_id' => $this->encryptId($teacher->id),
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'profession' => $teacher->profession,
                    'image' => $teacher->image ?: self::DEFAULT_IMAGE,
                    'status' => $teacher->status
                ]
            ]);
        }

        if ($developer && Auth::guard('developer')->attempt(['email' => $request->email, 'password' => $request->password])) {
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
            $validator = $this->validate_update($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            $user = Users::where('email', $request->old_email)->firstOrFail();

            if ($this->should_change_password($request)) {
                $passwordValidator = $this->validate_password_change($request);
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
                'image' => $request->hasFile('image') ? $this->handle_image_upload($request->image) : $user->image
            ]);

            $user->save();

            return $this->success_response(
                'Profils veiksmīgi rediģēts!',
                ['data' => $user]
            );
        } catch (\Exception $e) {
            return $this->error_response(['Rediģēšana neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function admin_update_store(Request $request)
    {
        try {
            $validator = $this->validate_admin_update($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            $teacher = Teachers::where('email', $request->old_email)->firstOrFail();

            if ($this->should_change_password($request)) {
                $passwordValidator = $this->validate_password_change($request);
                if ($passwordValidator->fails()) {
                    return $this->error_response($passwordValidator->messages()->all());
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
                'image' => $request->hasFile('image') ? $this->handle_image_upload($request->image) : $teacher->image
            ]);

            $teacher->save();

            return $this->success_response(
                'Profils veiksmīgi rediģēts!',
                ['data' => $teacher]
            );
        } catch (\Exception $e) {
            return $this->error_response(['Rediģēšana neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function admin_registration_store(Request $request)
    {
        try {
            $validator = $this->validate_admin_registration($request);
            if ($validator->fails()) {
                return $this->error_response($validator->messages()->all());
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
                'image' => $this->handle_image_upload($request->image)
            ]);

            $teacher->save();

            return $this->success_response(
                'Pasniedzējs veiksmīgi reģistrēts!',
                ['data' => $teacher]
            );
        } catch (\Exception $e) {
            return $this->error_response(['Reģistrācija neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function get_profile(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->error_response(['Lietotājs nav atrasts!'], 404);
            }

            return $this->success_response('Profils veiksmīgi atgriezts!', [
                'data' => [
                    'id' => $user->id,
                    'encrypted_id' => $user->encrypted_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'image' => $user->image
                ]
            ]);
        } catch (\Exception $e) {
            return $this->error_response(['Profils atgriešana neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    public function update_profile(Request $request)
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
                return $this->error_response($validator->errors()->all(), 422);
            }

            if ($request->filled('old_password')) {
                if (!Hash::check($request->old_password, $user->password)) {
                    return $this->error_response(['Pašreizējā parole nav pareiza!'], 422);
                }
                $user->password = Hash::make($request->new_password);
            }

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->hasFile('image')) {
                if ($user->image && $user->image !== self::DEFAULT_IMAGE) {
                    Storage::delete(str_replace('/storage/', '', $user->image));
                }
                $user->image = $this->handle_image_upload($request->file('image'));
            }

            $user->save();

            return $this->success_response('Profils veiksmīgi rediģēts!', ['data' => $user]);
        } catch (\Exception $e) {
            return $this->error_response(['Rediģēšana neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
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
                'email' => 'required|string|email|max:255',
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

            if (Users::where('email', $request->email)->exists() ||
                Teachers::where('email', $request->email)->exists() ||
                Developers::where('email', $request->email)->exists()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'E-pasts jau eksistē citā lietotāju, pasniedzēju vai izstrādātāju kontā'
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
                $userData['image'] = $this->handle_image_upload($request->file('image'));
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
                'email' => 'required|string|email|max:255',
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

            if (Users::where('email', $request->email)->where('id', '!=', $id)->exists() ||
                Teachers::where('email', $request->email)->exists() ||
                Developers::where('email', $request->email)->exists()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'E-pasts jau eksistē citā lietotāju, pasniedzēju vai izstrādātāju kontā'
                ], 422);
            }

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
                if ($user->image && $user->image !== self::DEFAULT_IMAGE) {
                    Storage::delete(str_replace('/storage/', '', $user->image));
                }
                $userData['image'] = $this->handle_image_upload($request->file('image'));
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
            $user = Users::find($id);
            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lietotājs nav atrasts'
                ], 404);
            }

            DB::beginTransaction();

            try {
                if ($user->image && $user->image !== self::DEFAULT_IMAGE) {
                    $imagePath = str_replace('/storage/', '', $user->image);
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }

                Likes::where('user_id', $id)->delete();

                Comments::where('user_id', $id)->delete();

                Bookmarks::where('user_id', $id)->delete();

                $user->delete();

                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Lietotājs un visi saistītie dati veiksmīgi dzēsti'
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('User deletion error: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Kļūda dzēšot lietotāju: ' . $e->getMessage()
            ], 500);
        }
    }

    private function handle_image_upload($image)
    {
        if (!$image) {
            return self::DEFAULT_IMAGE;
        }

        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs(self::UPLOAD_PATH, $imageName, 'public');
        return '/storage/app/public/' . $imagePath;
    }

    private function should_change_password(Request $request)
    {
        return $request->filled(['p_password', 'n_password', 'c_password']);
    }

    private function validate_registration(Request $request)
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

    private function validate_update(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|max:50'
        ], [
            'name.required' => 'Ievadiet savu vārdu',
            'email.required' => 'Ievadiet savu e-pastu'
        ]);
    }

    private function validate_admin_update(Request $request)
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

    private function validate_admin_registration(Request $request)
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

    private function validate_password_change(Request $request)
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

    private function error_response(array $messages, int $status = 500)
    {
        return response()->json([
            'message' => $messages,
            'status' => $status
        ], $status);
    }

    private function success_response(string $message, array $data = [], int $status = 200)
    {
        return response()->json(array_merge([
            'message' => [$message],
            'status' => $status
        ], $data), $status);
    }
}