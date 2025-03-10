<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AdminController extends Controller
{
    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();

            // Validate the request
            $rules = [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'old_password' => 'required_with:new_password',
                'new_password' => 'required_with:old_password|min:8',
                'confirm_password' => 'required_with:new_password|same:new_password',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Update basic info
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }

            // Handle password update
            if ($request->has('old_password')) {
                if (!Hash::check($request->old_password, $user->password)) {
                    return response()->json([
                        'message' => 'Current password is incorrect'
                    ], 422);
                }
                $user->password = Hash::make($request->new_password);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($user->image && Storage::exists('public/' . $user->image)) {
                    Storage::delete('public/' . $user->image);
                }

                // Store new image
                $imagePath = $request->file('image')->store('uploads', 'public');
                $user->image = $imagePath;
            }

            $user->save();

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatistics()
    {
        $user = auth()->user();
        
        return response()->json([
            'likes' => 0, // Implement your logic to get likes count
            'comments' => 0, // Implement your logic to get comments count
        ]);
    }
} 