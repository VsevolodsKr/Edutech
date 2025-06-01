<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Developers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DevelopersController extends Controller
{
    public function update(Request $request)
    {
        try {
            $developer = auth()->user();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('developers', 'email')->ignore($developer->id),
                    Rule::unique('teachers', 'email'),
                    Rule::unique('users', 'email'),
                ],
                'old_password' => 'required_with:new_password',
                'new_password' => 'nullable|min:6',
                'confirm_password' => 'same:new_password',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validācijas kļūda',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->filled('old_password')) {
                if (!Hash::check($request->old_password, $developer->password)) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Pašreizējā parole nav pareiza'
                    ], 422);
                }
            }

            $developer->name = $request->name;
            $developer->email = $request->email;

            if ($request->filled('new_password')) {
                $developer->password = Hash::make($request->new_password);
            }

            if ($request->hasFile('image')) {
                if ($developer->image && !str_starts_with($developer->image, 'http')) {
                    $oldImagePath = str_replace('/storage/', '', $developer->image);
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }
                }

                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('uploads', $imageName, 'public');
                $developer->image = '/storage/uploads/' . $imageName;
            }

            $developer->save();

            return response()->json([
                'status' => 200,
                'message' => 'Profils veiksmīgi atjaunināts',
                'data' => [
                    'id' => $developer->id,
                    'name' => $developer->name,
                    'email' => $developer->email,
                    'image' => $developer->getImageAttribute($developer->image),
                    'is_developer' => true
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Kļūda atjaunojot profilu: ' . $e->getMessage()
            ], 500);
        }
    }
} 