<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Playlists;
use App\Models\Teachers;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PlaylistController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour

    public function get_all()
    {
        return Cache::remember('playlists.all', self::CACHE_TTL, function () {
            return Playlists::with('teacher')
                ->where('status', 'active')
                ->get()
                ->map(function ($playlist) {
                    return $this->formatPlaylist($playlist);
                });
        });
    }

    public function get_teacher_playlists(string $id)
    {
        return Cache::remember('teacher.playlists.'.$id, self::CACHE_TTL, function () use ($id) {
            return Playlists::with('teacher')
                ->where('teacher_id', $id)
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($playlist) {
                    return $this->formatPlaylist($playlist);
                });
        });
    }

    public function get_single(string $id)
    {
        return Cache::remember('playlist.'.$id, self::CACHE_TTL, function () use ($id) {
            $playlist = Playlists::with('teacher')->find($id);
            return $playlist ? $this->formatPlaylist($playlist) : null;
        });
    }

    public function get_amount(string $id)
    {
        return Cache::remember('teacher.playlists.count.'.$id, self::CACHE_TTL, function () use ($id) {
            return Playlists::where('teacher_id', $id)->count();
        });
    }

    public function get_teacher(string $id)
    {
        return Cache::remember('teacher.'.$id, self::CACHE_TTL, function () use ($id) {
            $teacher = Teachers::find($id);
            return $teacher ? $this->formatTeacher($teacher) : null;
        });
    }

    public function search_playlists(Request $request)
    {
        $searchTerm = strtolower($request->name);
        
        // Search in playlists
        $playlists = Playlists::with('teacher')
            ->where('title', 'like', '%'.$searchTerm.'%')
            ->orWhere('description', 'like', '%'.$searchTerm.'%')
            ->get();

        if ($playlists->isNotEmpty()) {
            return $playlists->map(function ($playlist) {
                return $this->formatPlaylist($playlist);
            });
        }

        // If no playlists found, search by teacher name
        return Cache::remember('playlists.all', self::CACHE_TTL, function () {
            return Playlists::with('teacher')
                ->where('status', 'active')
                ->get();
        })->filter(function ($playlist) use ($searchTerm) {
            return str_contains(strtolower($playlist->teacher->name), $searchTerm);
        })->values()->map(function ($playlist) {
            return $this->formatPlaylist($playlist);
        });
    }

    public function add_playlist(Request $request)
    {
        $validator = $this->validatePlaylist($request);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->all(), 'status' => 500], 500);
        }

        try {
            $thumb = $this->handleThumbUpload($request->image);
            $playlist = $this->createPlaylist($request, $thumb);
            
            // Clear relevant caches
            $this->clearPlaylistCaches($playlist->teacher_id);
            
            return response()->json([
                'message' => ['You have successfully created new playlist!'], 
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => ['Something went wrong, please try again later!'], 
                'status' => 500
            ], 500);
        }
    }

    public function store(Request $request, string $id)
    {
        $validator = $this->validatePlaylist($request);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->messages()->all(), 'status' => 500], 500);
        }

        try {
            $playlist = Playlists::findOrFail($id);
            $updateData = [
                'status' => $request->status,
                'title' => $request->title,
                'description' => $request->description
            ];

            if ($request->image) {
                $updateData['thumb'] = $this->handleThumbUpload($request->image);
            }

            $playlist->update($updateData);
            
            // Clear relevant caches
            $this->clearPlaylistCaches($playlist->teacher_id);
            
            return response()->json([
                'message' => ['You have successfully updated your playlist!'], 
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => ['Something went wrong, please try again later!'], 
                'status' => 500
            ], 500);
        }
    }

    public function delete(string $id)
    {
        try {
            $playlist = Playlists::findOrFail($id);
            $teacherId = $playlist->teacher_id;
            $playlist->delete();
            
            // Clear relevant caches
            $this->clearPlaylistCaches($teacherId);
            
            return response()->json([
                'message' => ['Playlist deleted successfully'], 
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => ['Failed to delete playlist'], 
                'status' => 500
            ], 500);
        }
    }

    private function validatePlaylist(Request $request)
    {
        $rules = [
            'status' => 'required',
            'title' => 'required|max:50',
            'description' => 'required',
        ];
        
        $messages = [
            'status.required' => 'Please, select playlist status',
            'title.required' => 'Please, enter playlist title',
            'description.required' => 'Please, enter playlist description',
        ];
        
        return Validator::make($request->input(), $rules, $messages);
    }

    private function handleThumbUpload($image)
    {
        $thumbName = time() . '_' . $image->getClientOriginalName();
        $thumbPath = $image->storeAs('playlist_thumbs', $thumbName, 'public');
        return '/storage/' . str_replace('public/', '', $thumbPath);
    }

    private function createPlaylist(Request $request, string $thumb)
    {
        return Playlists::create([
            'teacher_id' => $request->teacher_id,
            'title' => $request->title,
            'description' => $request->description,
            'thumb' => $thumb,
            'date' => Carbon::now()->toDateString(),
            'status' => $request->status,
        ]);
    }

    private function clearPlaylistCaches(string $teacherId)
    {
        Cache::forget('playlists.all');
        Cache::forget('teacher.playlists.'.$teacherId);
        Cache::forget('teacher.playlists.count.'.$teacherId);
    }

    private function formatPlaylist($playlist)
    {
        if (!$playlist) return null;
        
        $formatted = [
            'id' => $playlist->id,
            'teacher_id' => $playlist->teacher_id,
            'title' => $playlist->title,
            'description' => $playlist->description,
            'thumb' => $playlist->thumb,
            'date' => $playlist->formatted_date,
            'status' => $playlist->status,
            'created_at' => $playlist->created_at,
            'updated_at' => $playlist->updated_at,
            'content_count' => \App\Models\Contents::where('playlist_id', $playlist->id)->count()
        ];
        
        if ($playlist->teacher) {
            $formatted['teacher'] = $this->formatTeacher($playlist->teacher);
        }
        
        return $formatted;
    }

    private function formatTeacher($teacher)
    {
        if (!$teacher) return null;
        
        return [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'image' => $teacher->image ? str_replace('/storage/app/public/', '/storage/', $teacher->image) : null,
            'profession' => $teacher->profession ?? 'Teacher'
        ];
    }
}
