<?php

namespace App\Http\Controllers\Api;

use App\Traits\Encryptable;
use App\Http\Controllers\Controller;
use App\Models\Teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Teacher;
use App\Models\Playlists;
use App\Models\Contents;
use App\Models\Comments;
use App\Models\Likes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Users;
use Illuminate\Support\Facades\Storage;

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

    public function index()
    {
        try {
            $teachers = Teachers::all()->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'profession' => $teacher->profession,
                    'status' => $teacher->status,
                    'created_at' => $teacher->created_at
                ];
            });

            return response()->json([
                'status' => 200,
                'message' => 'Skolotāji veiksmīgi ielādēti',
                'data' => $teachers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās ielādēt skolotājus',
                'data' => null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:teachers,email',
                'profession' => 'required|string|max:255',
                'status' => 'required',
                'password' => 'required|min:6',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Validācijas kļūda',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'profession' => $request->profession,
                'status' => $request->status,
                'password' => Hash::make($request->password)
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/uploads', $imageName);
                $data['image'] = '/storage/app/public/uploads/' . $imageName;
            }

            $teacher = Teachers::create($data);

            return response()->json([
                'status' => 201,
                'message' => 'Skolotājs veiksmīgi pievienots!',
                'data' => $teacher
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Failed to create teacher: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās pievienot skolotāju',
                'data' => null
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            \Log::info('Update request data:', $request->all());

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:teachers,email,' . $id,
                'profession' => 'required|string|max:255',
                'status' => 'required|in:aktīvs,neaktīvs',
                'password' => 'nullable|min:6',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'message' => 'Validācijas kļūda',
                    'errors' => $validator->errors()
                ], 422);
            }

            $teacher = Teachers::findOrFail($id);

            $teacherData = [
                'name' => $request->name,
                'email' => $request->email,
                'profession' => $request->profession,
                'status' => $request->status
            ];

            // Update password only if provided
            if ($request->filled('password')) {
                $teacherData['password'] = Hash::make($request->password);
            }

            // Handle image upload only if provided
            if ($request->hasFile('image')) {
                if ($teacher->image && Storage::disk('public')->exists($teacher->image)) {
                    Storage::disk('public')->delete($teacher->image);
                }
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/uploads', $imageName);
                $teacherData['image'] = '/storage/app/public/uploads/' . $imageName;
            }

            $teacher->update($teacherData);

            return response()->json([
                'message' => 'Skolotājs veiksmīgi rediģēts!',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating teacher: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'message' => 'Neizdevās rediģēt skolotāju',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $teacher = Teachers::find($id);
            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Skolotājs nav atrasts',
                    'data' => null
                ], 404);
            }

            // Start a database transaction
            DB::beginTransaction();

            try {
                // Delete teacher's image if it exists
                if ($teacher->image) {
                    $imagePath = str_replace('/storage/', '', $teacher->image);
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }

                // Get all contents first to delete their files
                $contents = Contents::where('teacher_id', $id)->get();
                foreach ($contents as $content) {
                    if ($content->thumb) {
                        $thumbPath = str_replace('/storage/', '', $content->thumb);
                        if (Storage::disk('public')->exists($thumbPath)) {
                            Storage::disk('public')->delete($thumbPath);
                        }
                    }

                    if ($content->video && $content->video_source_type === 'local') {
                        $videoPath = str_replace('/storage/', '', $content->video);
                        if (Storage::disk('public')->exists($videoPath)) {
                            Storage::disk('public')->delete($videoPath);
                        }
                    }
                }

                // Delete all contents
                Contents::where('teacher_id', $id)->delete();

                // Get all playlists to delete their thumbnails
                $playlists = Playlists::where('teacher_id', $id)->get();
                foreach ($playlists as $playlist) {
                    if ($playlist->thumb) {
                        $thumbPath = str_replace('/storage/', '', $playlist->thumb);
                        if (Storage::disk('public')->exists($thumbPath)) {
                            Storage::disk('public')->delete($thumbPath);
                        }
                    }
                }

                // Delete all playlists
                Playlists::where('teacher_id', $id)->delete();

                // Delete all comments
                Comments::where('teacher_id', $id)->delete();

                // Delete all likes
                Likes::where('teacher_id', $id)->delete();

                // Finally delete the teacher
                $teacher->delete();

                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Skolotājs un visi saistītie dati veiksmīgi dzēsti',
                    'data' => null
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās dzēst skolotāju: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getTopTeachers()
    {
        $teachers = Teachers::withCount([
            'playlists',
            'contents',
            'comments',
            'likes'
        ])
        ->orderBy('likes_count', 'desc')
        ->take(5)
        ->get();

        $data = [
            'labels' => $teachers->pluck('name'),
            'datasets' => [
                'playlists' => $teachers->pluck('playlists_count'),
                'contents' => $teachers->pluck('contents_count'),
                'comments' => $teachers->pluck('comments_count'),
                'likes' => $teachers->pluck('likes_count')
            ]
        ];

        return response()->json($data);
    }

    public function getTeacherStats()
    {
        $stats = [
            'total' => Teachers::count(),
            'active' => Teachers::where('status', 'active')->count(),
            'inactive' => Teachers::where('status', 'inactive')->count()
        ];

        return response()->json($stats);
    }

    public function destroyTeacherContent($id)
    {
        try {
            $teacher = Teachers::find($id);
            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Skolotājs nav atrasts',
                    'data' => null
                ], 404);
            }

            // Start a database transaction
            DB::beginTransaction();

            try {
                // Get all playlists to delete their thumbnails
                $playlists = Playlists::where('teacher_id', $id)->get();
                foreach ($playlists as $playlist) {
                    if ($playlist->thumb) {
                        $thumbPath = str_replace('/storage/', '', $playlist->thumb);
                        if (Storage::disk('public')->exists($thumbPath)) {
                            Storage::disk('public')->delete($thumbPath);
                        }
                    }
                }

                // Get all contents to delete their thumbnails and videos
                $contents = Contents::where('teacher_id', $id)->get();
                foreach ($contents as $content) {
                    if ($content->thumb) {
                        $thumbPath = str_replace('/storage/', '', $content->thumb);
                        if (Storage::disk('public')->exists($thumbPath)) {
                            Storage::disk('public')->delete($thumbPath);
                        }
                    }

                    if ($content->video && $content->video_source_type === 'local') {
                        $videoPath = str_replace('/storage/', '', $content->video);
                        if (Storage::disk('public')->exists($videoPath)) {
                            Storage::disk('public')->delete($videoPath);
                        }
                    }
                }

                // Delete all playlists (this will cascade delete contents)
                Playlists::where('teacher_id', $id)->delete();

                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Visi pasniedzēja kursi un video ir dzēsti',
                    'data' => null
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās dzēst pasniedzēja kursus un video: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
