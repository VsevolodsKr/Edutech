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

    // Konstantes
    private const DEFAULT_IMAGE = '/storage/app/public/default.png';
    private const UPLOAD_PATH = 'uploads';

    // Reģistrācijas metode
    public function registration_store(Request $request)
    {
        try {
            // Validācija
            $validator = $this->validateRegistration($request);
            // Ja validācija neizdodas, atgriež kļūdas
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            // Vai paroles sakrīt?
            if ($request->password !== $request->conf_password) {
                return $this->errorResponse(['Paroles nesakrīt!']);
            }

            // Vai e-pasts jau eksistē?
            if (Users::where('email', $request->email)->exists()) {
                return $this->errorResponse(['E-pasts jau eksistē. Pamēģiniet ielogoties!']);
            }

            // Pēc visām validācijām izveido jaunu lietotāju un aizpilda tā laukus
            $user = new Users();
            $user->fill([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $this->handleImageUpload($request->image)
            ]);

            // Saglabājam jaunu lietotāju

            $user->save();

            // Atgriežam lietotāja datus un veiksmīgu reģistrācijas paziņojumu
            return $this->successResponse(
                'Jūs veiksmīgi esat reģistrējusies Edutech platformā!',
                ['data' => $user]
            );
        } catch (\Exception $e) {
            // Ja kāda kļūda notikusi, atgriežam kļūdas paziņojumu
            return $this->errorResponse(['Reģistrācija neizdevās. Lūdzu, mēģiniet vēlreiz vēlāk!']);
        }
    }

    // Autentifikācijas metode
    public function login_store(Request $request)
    {
        // Validācija
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Ja validācija neizdodas, atgriežam kļūdas
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validācijas kļūda',
                'errors' => $validator->errors()
            ], 422);
        }

        // Iegūstam lietotāju, pasniedzēju vai administratoru
        $user = Users::where('email', $request->email)->first();
        $teacher = Teachers::where('email', $request->email)->first();
        $developer = Developers::where('email', $request->email)->first();

        // Ja lietotājs ir deaktivizēts, atgriežam kļūdas
        if ($user && $user->status === 'neaktīvs') {
            return response()->json([
                'status' => 403,
                'message' => 'Jūsu konts ir deaktivizēts'
            ], 403);
        }

        // Ja pasniedzējs ir deaktivizēts, atgriežam kļūdas
        if ($teacher && $teacher->status === 'neaktīvs') {
            return response()->json([
                'status' => 403,
                'message' => 'Jūsu konts ir deaktivizēts'
            ], 403);
        }

        // Ja izstrādātājs ir deaktivizēts, atgriežam kļūdas
        if ($developer && $developer->status === 'neaktīvs') {
            return response()->json([
                'status' => 403,
                'message' => 'Jūsu konts ir deaktivizēts'
            ], 403);
        }

        // Ja lietotājs ir aktivizēts un e-pasts ar paroli sakrīt, izveidojam jaunu token un atgriežam lietotāja datus
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

        // Ja pasniedzējs ir aktivizēts un e-pasts ar paroli sakrīt, izveidojam jaunu token un atgriežam pasniedzēja datus
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

        // Ja administrators ir aktivizēts un e-pasts ar paroli sakrīt, izveidojam jaunu token un atgriežam administratora datus
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

        // Ja neizdodas, atgriežam kļūdas
        return response()->json([
            'status' => 401,
            'message' => 'Nederīgs e-pasts vai parole'
        ], 401);
    }

    // Lietotāja profila rediģēšanas metode
    public function update_store(Request $request)
    {
        try {
            // Validācija
            $validator = $this->validateUpdate($request);
            // Ja validācija neizdodas, atgriežam kļūdas
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            // Iegūstam lietotāju
            $user = Users::where('email', $request->old_email)->firstOrFail();

            // Ja jāmaina parole
            if ($this->shouldChangePassword($request)) {
                // Validācija
                $passwordValidator = $this->validatePasswordChange($request);
                // Ja validācija neizdodas, atgriežam kļūdas
                if ($passwordValidator->fails()) {
                    return $this->errorResponse($passwordValidator->messages()->all());
                }

                // Ja iepriekšējā parole nav pareiza, atgriežam kļūdas
                if (!Hash::check($request->p_password, $user->password)) {
                    return $this->errorResponse(['Iepriekšējā parole nav pareiza!']);
                }

                // Ja jaunā parole nesakrīt, atgriežam kļūdas
                if ($request->n_password !== $request->c_password) {
                    return $this->errorResponse(['Jaunas paroles nesakrīt!']);
                }

                // Ja visas validācijas izdevās, maina paroli
                $user->password = Hash::make($request->n_password);
            }

            // Ja e-pasts ir mainīts uz jau eksistējošu e-pastu, atgriežam kļūdas
            if ($request->email !== $request->old_email && Users::where('email', $request->email)->exists()) {
                return $this->errorResponse(['E-pasts jau eksistē!']);
            }

            // Ja visas validācijas izdevās, aizpilda lietotāja datus
            $user->fill([
                'name' => $request->name,
                'email' => $request->email,
                'image' => $request->hasFile('image') ? $this->handleImageUpload($request->image) : $user->image
            ]);

            // Saglabājam lietotāja datus
            $user->save();

            // Atgriežam veiksmīgu paziņojumu
            return $this->successResponse(
                'Profils veiksmīgi rediģēts!',
                ['data' => $user]
            );
        } catch (\Exception $e) {
            // Ja kāda kļūda notikusi, atgriežam kļūdas paziņojumu
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
            $user = Users::find($id);
            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lietotājs nav atrasts'
                ], 404);
            }

            // Start a database transaction
            DB::beginTransaction();

            try {
                // Delete user's image if it exists and is not the default image
                if ($user->image && $user->image !== self::DEFAULT_IMAGE) {
                    $imagePath = str_replace('/storage/', '', $user->image);
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }

                // Delete all user's likes
                Likes::where('user_id', $id)->delete();

                // Delete all user's comments
                Comments::where('user_id', $id)->delete();

                // Delete all user's bookmarks
                Bookmarks::where('user_id', $id)->delete();

                // Finally delete the user
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