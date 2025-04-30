<?php

namespace App\Http\Controllers\Api;

use App\Traits\Encryptable;
use App\Http\Controllers\Controller;
use App\Models\Teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    use Encryptable;

    public function get_teacher(string $encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Invalid teacher ID',
                    'data' => null
                ], 404);
            }

            $teacher = Teachers::find($id);
            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Teacher not found',
                    'data' => null
                ], 404);
            }

            // Get the formatted image
            $formattedImage = $teacher->formatted_image;
            
            // Prepare the response data
            $teacherData = [
                'id' => $teacher->id,
                'encrypted_id' => $teacher->encrypted_id,
                'name' => $teacher->name,
                'profession' => $teacher->profession,
                'email' => $teacher->email,
                'image' => $formattedImage,
                'formatted_image' => $formattedImage
            ];
            
            return response()->json([
                'status' => 200,
                'message' => 'Teacher found successfully',
                'data' => $teacherData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to get teacher',
                'data' => null
            ], 500);
        }
    }

    public function get_teacher_playlists(string $encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Invalid teacher ID',
                    'data' => null
                ], 404);
            }

            $teacher = Teachers::find($id);
            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Teacher not found',
                    'data' => null
                ], 404);
            }

            $playlists = $teacher->playlists()
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($playlist) {
                    return [
                        'id' => $playlist->id,
                        'encrypted_id' => $this->encryptId($playlist->id),
                        'title' => $playlist->title,
                        'description' => $playlist->description,
                        'thumb' => $playlist->thumb,
                        'date' => $playlist->date,
                        'teacher_id' => $playlist->teacher_id,
                        'content_count' => $playlist->contents()->count()
                    ];
                });

            return response()->json([
                'status' => 200,
                'message' => 'Playlists retrieved successfully',
                'data' => $playlists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to get teacher playlists',
                'data' => null
            ], 500);
        }
    }

    public function get_all()
    {
        $teachers = Teachers::all()->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'encrypted_id' => $teacher->encrypted_id,
                'name' => $teacher->name,
                'profession' => $teacher->profession,
                'email' => $teacher->email,
                'image' => $teacher->formatted_image,
                'formatted_image' => $teacher->formatted_image
            ];
        });
        $teachers = Teachers::all();
        return response()->json($teachers);
    }

    public function search_teachers(Request $request)
    {
        $query = $request->input('query');
        $teachers = Teachers::where('name', 'like', "%{$query}%")
            ->orWhere('profession', 'like', "%{$query}%")
            ->get();
        return response()->json($teachers);
    }

    public function find_teacher($encryptedId) {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Invalid teacher ID',
                    'data' => null
                ], 404);
            }

            $teacher = Teachers::find($id);
            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Teacher not found',
                    'data' => null
                ], 404);
            }
            
            // Get the formatted image
            $formattedImage = $teacher->formatted_image;
            
            // Prepare the response data
            $teacherData = [
                'id' => $teacher->id,
                'encrypted_id' => $teacher->encrypted_id,
                'name' => $teacher->name,
                'profession' => $teacher->profession,
                'email' => $teacher->email,
                'image' => $formattedImage,
                'formatted_image' => $formattedImage
            ];
            
            return response()->json([
                'status' => 200,
                'message' => 'Teacher found successfully',
                'data' => $teacherData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error finding teacher: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Failed to find teacher',
                'data' => null
            ], 500);
        }
    }
}
