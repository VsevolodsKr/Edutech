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
use App\Models\Contents;

class PlaylistsController extends Controller
{
    private const PLAYLIST_THUMBS_PATH = 'playlist_thumbs';

    /**
     * Get all playlists
     */
    public function all()
    {
        return Playlists::orderBy('date', 'desc')->get();
    }

    public function active()
    {
        return Playlists::orderBy('date', 'desc')->where('status', 'active')->get();
    }

    /**
     * Get latest 7 playlists
     */
    public function latest()
    {
        try {
            $playlists = Playlists::with('teacher')
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
        $playlist = Playlists::findOrFail($id);
        $teacher = Teachers::findOrFail($playlist->teacher_id);
        
        return response()->json([
            'playlist' => $playlist,
            'teacher' => $teacher
        ]);
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
            $playlist->status = $request->status;
            $playlist->teacher_id = $request->teacher_id;
            $playlist->title = $request->title;
            $playlist->description = $request->description;
            $playlist->date = Carbon::now();

            if ($request->hasFile('thumb')) {
                $playlist->thumb = $this->handleFileUpload($request->file('thumb'));
            }

            $playlist->save();

            return $this->successResponse('Playlist created successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to create playlist', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(['Failed to create playlist: ' . $e->getMessage()]);
        }
    }

    /**
     * Update playlist
     */
    public function update(Request $request, $id)
    {
        try {
            \Log::info('Updating playlist', [
                'id' => $id,
                'request_data' => $request->except(['thumb']),
                'has_thumb' => $request->hasFile('thumb')
            ]);

            $validator = $this->validatePlaylist($request);
            if ($validator->fails()) {
                \Log::error('Validation failed', ['errors' => $validator->messages()->all()]);
                return $this->errorResponse($validator->messages()->all());
            }

            $playlist = Playlists::findOrFail($id);
            
            $updateData = [
                'teacher_id' => $request->teacher_id,
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status
            ];

            // Handle thumbnail update only if a new file is provided
            if ($request->hasFile('thumb')) {
                try {
                    $updateData['thumb'] = $this->handleFileUpload($request->file('thumb'));
                    
                    // Delete old thumbnail if exists
                    if ($playlist->thumb && Storage::disk('public')->exists($playlist->thumb)) {
                        Storage::disk('public')->delete($playlist->thumb);
                    }
                } catch (\Exception $e) {
                    \Log::error('Thumbnail upload failed', ['error' => $e->getMessage()]);
                    return $this->errorResponse(['Failed to upload thumbnail: ' . $e->getMessage()]);
                }
            }
            // If no new thumb is provided, keep the existing one

            \Log::info('Updating playlist with data', ['update_data' => $updateData]);
            
            $playlist->update($updateData);

            return $this->successResponse('Playlist updated successfully');
        } catch (\Exception $e) {
            \Log::error('Playlist update error', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(['Failed to update playlist: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete playlist
     */
    public function delete($id)
    {
        try {
            $playlist = Playlists::findOrFail($id);
            
            // Delete associated contents
            Contents::where('playlist_id', $id)->delete();
            
            // Delete the playlist
            $playlist->delete();
            
            return $this->successResponse('Playlist deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to delete playlist', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(['Failed to delete playlist: ' . $e->getMessage()]);
        }
    }

    /**
     * Get teacher's playlists
     */
    public function teacher_playlists($id)
    {
        try {
            $playlists = Playlists::where('teacher_id', $id)
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($playlist) {
                    return [
                        'id' => $playlist->id,
                        'title' => $playlist->title,
                        'description' => $playlist->description,
                        'thumb' => $playlist->thumb ? '/storage/' . $playlist->thumb : null,
                        'date' => $playlist->date,
                        'status' => $playlist->status,
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
                'message' => 'Failed to retrieve playlists',
                'data' => []
            ], 500);
        }
    }

    public function active_teacher_playlists($id)
    {
        try {
            $playlists = Playlists::where('teacher_id', $id)
                ->where('status', 'active')
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($playlist) {
                    return [
                        'id' => $playlist->id,
                        'title' => $playlist->title,
                        'description' => $playlist->description,
                        'thumb' => $playlist->thumb ? '/storage/' . $playlist->thumb : null,
                        'date' => $playlist->date,
                        'status' => $playlist->status,
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
                'message' => 'Failed to retrieve playlists',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get playlist's teacher
     */
    public function playlist_teacher($id)
    {
        $playlist = Playlists::findOrFail($id);
        return Teachers::findOrFail($playlist->teacher_id);
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

        try {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs(self::PLAYLIST_THUMBS_PATH, $fileName, 'public');
            
            if (!$filePath) {
                throw new \Exception('Failed to store file');
            }
            
            return $filePath;
        } catch (\Exception $e) {
            \Log::error('File upload error', [
                'error' => $e->getMessage(),
                'file_name' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Validate playlist request
     */
    private function validatePlaylist(Request $request)
    {
        $rules = [
            'title' => 'required|max:100',
            'description' => 'required',
            'teacher_id' => 'required|exists:teachers,id',
            'status' => 'required|in:active,deactive'
        ];

        // Check if this is a new playlist or an update
        $isNewPlaylist = !$request->route('id');

        // Only require thumb for new playlists
        if ($isNewPlaylist) {
            $rules['thumb'] = 'required|image|mimes:jpeg,png|max:2048';
        } else {
            // For updates, thumb is completely optional
            if ($request->hasFile('thumb')) {
                $rules['thumb'] = 'image|mimes:jpeg,png|max:2048';
            }
        }

        return Validator::make($request->all(), $rules, [
            'title.required' => 'Please enter playlist title',
            'description.required' => 'Please enter playlist description',
            'teacher_id.required' => 'Teacher ID is required',
            'teacher_id.exists' => 'Invalid teacher ID',
            'status.required' => 'Please select playlist status',
            'status.in' => 'Invalid status value',
            'thumb.required' => 'Please select a thumbnail image',
            'thumb.image' => 'The file must be an image',
            'thumb.mimes' => 'The thumbnail must be a JPEG or PNG file',
            'thumb.max' => 'The thumbnail size must not exceed 2MB'
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