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

    /**
     * Register a new user
     */
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
                'You have successfully registered to Edutech',
                ['data' => $user]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(['Registration failed. Please try again later.']);
        }
    }

    /**
     * Handle user login
     */
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
                return $this->errorResponse(['Email not found. Please register first.']);
            }

            if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
                $token = $user->createToken('MyApp')->plainTextToken;
                return $this->successResponse('Login successful', [
                    'data' => $user,
                    'token' => $token,
                    'is_teacher' => false
                ]);
            }

            if (Auth::guard('teacher')->attempt(['email' => $request->email, 'password' => $request->password])) {
                $token = $teacher->createToken('MyApp')->plainTextToken;
                return $this->successResponse('Login successful', [
                    'data' => $teacher,
                    'token' => $token,
                    'is_teacher' => true
                ]);
            }

            return $this->errorResponse(['Invalid credentials']);
        } catch (\Exception $e) {
            return $this->errorResponse(['Login failed. Please try again later.']);
        }
    }

    /**
     * Update user profile
     */
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
                    return $this->errorResponse(['Previous password is incorrect']);
                }

                if ($request->n_password !== $request->c_password) {
                    return $this->errorResponse(['New passwords do not match']);
                }

                $user->password = Hash::make($request->n_password);
            }

            if ($request->email !== $request->old_email && Users::where('email', $request->email)->exists()) {
                return $this->errorResponse(['Email already exists']);
            }

            $user->fill([
                'name' => $request->name,
                'email' => $request->email,
                'image' => $request->hasFile('image') ? $this->handleImageUpload($request->image) : $user->image
            ]);

            $user->save();

            return $this->successResponse(
                'Profile updated successfully',
                ['data' => $user]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(['Update failed. Please try again later.']);
        }
    }

    /**
     * Update teacher profile
     */
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
                    return $this->errorResponse(['Previous password is incorrect']);
                }

                if ($request->n_password !== $request->c_password) {
                    return $this->errorResponse(['New passwords do not match']);
                }

                $teacher->password = Hash::make($request->n_password);
            }

            if ($request->email !== $request->old_email && Teachers::where('email', $request->email)->exists()) {
                return $this->errorResponse(['Email already exists']);
            }

            $teacher->fill([
                'name' => $request->name,
                'email' => $request->email,
                'profession' => $request->profession . ' teacher',
                'image' => $request->hasFile('image') ? $this->handleImageUpload($request->image) : $teacher->image
            ]);

            $teacher->save();

            return $this->successResponse(
                'Profile updated successfully',
                ['data' => $teacher]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(['Update failed. Please try again later.']);
        }
    }

    /**
     * Register a new teacher
     */
    public function admin_registration_store(Request $request)
    {
        try {
            $validator = $this->validateAdminRegistration($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            if ($request->password !== $request->conf_password) {
                return $this->errorResponse(['Passwords do not match']);
            }

            if (Teachers::where('email', $request->email)->exists()) {
                return $this->errorResponse(['Email already exists. Try to login!']);
            }

            if (Users::where('email', $request->email)->exists()) {
                return $this->errorResponse(['Email already exists in User database']);
            }

            $teacher = new Teachers();
            $teacher->fill([
                'name' => $request->name,
                'email' => $request->email,
                'profession' => $request->profession . ' teacher',
                'password' => Hash::make($request->password),
                'image' => $this->handleImageUpload($request->image)
            ]);

            $teacher->save();

            return $this->successResponse(
                'Teacher registered successfully',
                ['data' => $teacher]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(['Registration failed. Please try again later.']);
        }
    }

    /**
     * Get user profile
     */
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->errorResponse(['User not found'], 404);
            }

            return $this->successResponse('Profile retrieved successfully', [
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'image' => $user->image
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Profile retrieval error: ' . $e->getMessage());
            return $this->errorResponse(['Failed to retrieve profile: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return $this->errorResponse(['User not found'], 404);
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

            // Check old password if trying to change password
            if ($request->filled('old_password')) {
                if (!Hash::check($request->old_password, $user->password)) {
                    return $this->errorResponse(['Current password is incorrect'], 422);
                }
                $user->password = Hash::make($request->new_password);
            }

            $user->name = $request->name;
            $user->email = $request->email;

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                // Delete old image if it exists and is not the default image
                if ($user->image && $user->image !== self::DEFAULT_IMAGE) {
                    Storage::delete(str_replace('/storage/', '', $user->image));
                }
                $user->image = $this->handleImageUpload($request->file('image'));
            }

            $user->save();

            return $this->successResponse('Profile updated successfully', ['data' => $user]);
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return $this->errorResponse(['Failed to update profile: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($image)
    {
        if (!$image) {
            return self::DEFAULT_IMAGE;
        }

        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = $image->storeAs(self::UPLOAD_PATH, $imageName, 'public');
        return '/storage/app/public/' . $imagePath;
    }

    /**
     * Check if password should be changed
     */
    private function shouldChangePassword(Request $request)
    {
        return $request->filled(['p_password', 'n_password', 'c_password']);
    }

    /**
     * Validate registration request
     */
    private function validateRegistration(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|max:50',
            'password' => 'required|max:50',
            'conf_password' => 'required|max:50'
        ], [
            'name.required' => 'Please enter your name',
            'email.required' => 'Please enter your email',
            'password.required' => 'Please enter your password',
            'conf_password.required' => 'Please confirm your password'
        ]);
    }

    /**
     * Validate login request
     */
    private function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email|max:50',
            'password' => 'required|max:50'
        ], [
            'email.required' => 'Please enter your email',
            'password.required' => 'Please enter your password'
        ]);
    }

    /**
     * Validate update request
     */
    private function validateUpdate(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|max:50'
        ], [
            'name.required' => 'Please enter your name',
            'email.required' => 'Please enter your email'
        ]);
    }

    /**
     * Validate admin update request
     */
    private function validateAdminUpdate(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:50',
            'profession' => 'required',
            'email' => 'required|email|max:50'
        ], [
            'name.required' => 'Please enter your name',
            'profession.required' => 'Please select your profession',
            'email.required' => 'Please enter your email'
        ]);
    }

    /**
     * Validate admin registration request
     */
    private function validateAdminRegistration(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:50',
            'profession' => 'required',
            'email' => 'required|email|max:50',
            'password' => 'required|max:50',
            'conf_password' => 'required|max:50'
        ], [
            'name.required' => 'Please enter your name',
            'profession.required' => 'Please select your profession',
            'email.required' => 'Please enter your email',
            'password.required' => 'Please enter your password',
            'conf_password.required' => 'Please confirm your password'
        ]);
    }

    /**
     * Validate password change request
     */
    private function validatePasswordChange(Request $request)
    {
        return Validator::make($request->all(), [
            'p_password' => 'required|max:50',
            'n_password' => 'required|max:50',
            'c_password' => 'required|max:50'
        ], [
            'p_password.required' => 'Please enter your previous password',
            'n_password.required' => 'Please enter your new password',
            'c_password.required' => 'Please confirm your password'
        ]);
    }

    /**
     * Format error response
     */
    private function errorResponse(array $messages, int $status = 500)
    {
        return response()->json([
            'message' => $messages,
            'status' => $status
        ], $status);
    }

    /**
     * Format success response
     */
    private function successResponse(string $message, array $data = [], int $status = 200)
    {
        return response()->json(array_merge([
            'message' => [$message],
            'status' => $status
        ], $data), $status);
    }
}