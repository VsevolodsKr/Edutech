<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Playlists;
use App\Models\Teachers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PlaylistsController extends Controller
{
    private const CACHE_TTL = 3600; // 1 hour
    private const PLAYLIST_THUMBS_PATH = 'playlist_thumbs';

    /**
     * Get all playlists
     */
    public function all()
    {
        return Cache::remember('playlists.all', self::CACHE_TTL, function () {
            return Playlists::orderBy('date', 'desc')->get();
        });
    }

    /**
     * Get latest 7 playlists
     */
    public function latest()
    {
        try {
            $playlists = Cache::remember('playlists.latest', self::CACHE_TTL, function () {
                return Playlists::with('teacher')
                    ->where('status', 'active')
                    ->orderBy('date', 'desc')
                    ->take(7)
                    ->get()
                    ->map(function ($playlist) {
                        return [
                            'id' => $playlist->id,
                            'title' => $playlist->title,
                            'description' => $playlist->description,
                            'thumb' => $playlist->thumb,
                            'date' => $playlist->date,
                            'teacher_id' => $playlist->teacher_id,
                            'teacher' => $playlist->teacher ? [
                                'id' => $playlist->teacher->id,
                                'name' => $playlist->teacher->name,
                                'image' => $playlist->teacher->image,
                            ] : null
                        ];
                    });
            });

            return response()->json($playlists);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch latest playlists',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find playlist by ID
     */
    public function find($id)
    {
        return Cache::remember("playlist.{$id}", self::CACHE_TTL, function () use ($id) {
            $playlist = Playlists::findOrFail($id);
            $teacher = Teachers::findOrFail($playlist->teacher_id);
            
            return response()->json([
                'playlist' => $playlist,
                'teacher' => $teacher
            ]);
        });
    }

    /**
     * Search playlists
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->all());
        }

        return Playlists::where('title', 'like', '%' . $request->name . '%')
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Add new playlist
     */
    public function add(Request $request)
    {
        try {
            $validator = $this->validatePlaylist($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            $playlist = new Playlists();
            $playlist->fill([
                'teacher_id' => $request->teacher_id,
                'title' => $request->title,
                'description' => $request->description,
                'date' => Carbon::now(),
                'thumb' => $this->handleFileUpload($request->thumb)
            ]);

            $playlist->save();
            $this->clearPlaylistCaches();

            return $this->successResponse('Playlist created successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(['Failed to create playlist. Please try again later.']);
        }
    }

    /**
     * Update playlist
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = $this->validatePlaylist($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            $playlist = Playlists::findOrFail($id);
            
            $updateData = [
                'title' => $request->title,
                'description' => $request->description
            ];

            if ($request->hasFile('thumb')) {
                $updateData['thumb'] = $this->handleFileUpload($request->thumb);
            }

            $playlist->update($updateData);
            $this->clearPlaylistCaches();

            return $this->successResponse('Playlist updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(['Failed to update playlist. Please try again later.']);
        }
    }

    /**
     * Delete playlist
     */
    public function delete($id)
    {
        try {
            $playlist = Playlists::findOrFail($id);
            $playlist->delete();
            $this->clearPlaylistCaches();

            return $this->successResponse('Playlist deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(['Failed to delete playlist']);
        }
    }

    /**
     * Get teacher's playlists
     */
    public function teacher_playlists($id)
    {
        return Cache::remember("teacher.playlists.{$id}", self::CACHE_TTL, function () use ($id) {
            return Playlists::where('teacher_id', $id)
                ->orderBy('date', 'desc')
                ->get();
        });
    }

    /**
     * Get playlist's teacher
     */
    public function playlist_teacher($id)
    {
        return Cache::remember("playlist.teacher.{$id}", self::CACHE_TTL, function () use ($id) {
            $playlist = Playlists::findOrFail($id);
            return Teachers::findOrFail($playlist->teacher_id);
        });
    }

    /**
     * Get teacher's playlist amount
     */
    public function get_amount($id)
    {
        try {
            return response()->json([
                'data' => Playlists::where('teacher_id', $id)->count()
            ]);
        } catch (\Exception $e) {
            return response()->json(0);
        }
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($file)
    {
        if (!$file) {
            throw new \Exception('File not provided');
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs(self::PLAYLIST_THUMBS_PATH, $fileName, 'public');
        return $filePath;
    }

    /**
     * Validate playlist request
     */
    private function validatePlaylist(Request $request)
    {
        return Validator::make($request->all(), [
            'title' => 'required|max:100',
            'description' => 'required',
            'teacher_id' => 'required|exists:teachers,id'
        ], [
            'title.required' => 'Please enter playlist title',
            'description.required' => 'Please enter playlist description',
            'teacher_id.required' => 'Teacher ID is required',
            'teacher_id.exists' => 'Invalid teacher ID'
        ]);
    }

    /**
     * Clear playlist related caches
     */
    private function clearPlaylistCaches()
    {
        Cache::forget('playlists.all');
        Cache::forget('playlists.latest');
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