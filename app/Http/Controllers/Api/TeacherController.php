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
                    'message' => 'Nepareizs pasniedzēja ID',
                    'data' => null
                ], 404);
            }

            $teacher = Teachers::find($id);
            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Pasniedzējs nav atrasts',
                    'data' => null
                ], 404);
            }

            $formattedImage = $teacher->formatted_image;
            
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
                'message' => 'Pasniedzējs ir atrasts',
                'data' => $teacherData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās iegūt pasniedzēja datus',
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
                    'message' => 'Nepareizs pasniedzēja ID',
                    'data' => null
                ], 404);
            }

            $teacher = Teachers::find($id);
            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Pasniedzējs nav atrasts',
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
                'message' => 'Kursi ir iegūti',
                'data' => $playlists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās iegūt pasniedzēja kursus',
                'data' => null
            ], 500);
        }
    }

    public function all()
    {
        $teachers = Teachers::all()->map(function ($teacher) {
            $playlistCount = $teacher->playlists()->count();
            $contentCount = $teacher->contents()->count();

            $formattedImage = $teacher->formatted_image;

            return [
                'id' => $teacher->id,
                'encrypted_id' => $this->encryptId($teacher->id),
                'name' => $teacher->name,
                'profession' => $teacher->profession,
                'email' => $teacher->email,
                'image' => $formattedImage,
                'formatted_image' => $formattedImage,
                'playlist_count' => (int)$playlistCount,
                'content_count' => (int)$contentCount
            ];
        });
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
                    'message' => 'Nepareizs pasniedzēja ID',
                    'data' => null
                ], 404);
            }

            $teacher = Teachers::find($id);
            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Pasniedzējs nav atrasts',
                    'data' => null
                ], 404);
            }
            
            $formattedImage = $teacher->formatted_image;
            
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
                'message' => 'Pasniedzējs ir atrasts',
                'data' => $teacherData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās iegūt pasniedzēja datus',
                'data' => null
            ], 500);
        }
    }
}
