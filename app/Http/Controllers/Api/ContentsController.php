<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Encryptable;
use Illuminate\Http\Request;
use App\Models\Contents;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Comments;
use App\Models\Likes;

class ContentsController extends Controller
{
    use Encryptable;

    private const CONTENT_THUMBS_PATH = 'content_thumbs';
    private const CONTENT_VIDEOS_PATH = 'content_videos';

    /**
     * Get teacher's contents
     */
    public function get_teacher_contents(string $id)
    {
        return Contents::where('teacher_id', $id)
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Get playlist contents
     */
    public function get_playlist_contents($encryptedId)
    {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid playlist ID',
                'status' => 404
            ], 404);
        }

        $contents = Contents::where('playlist_id', $id)
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($content) {
                return [
                    'id' => $content->id,
                    'encrypted_id' => $content->encrypted_id,
                    'title' => $content->title,
                    'description' => $content->description,
                    'video' => $content->video,
                    'thumb' => $content->thumb,
                    'date' => $content->date,
                    'status' => $content->status,
                    'video_source_type' => $content->video_source_type,
                    'teacher_id' => $content->teacher_id,
                    'playlist_id' => $content->playlist_id
                ];
            });

        return response()->json($contents);
    }

    /**
     * Get single content
     */
    public function get_single($encryptedId)
    {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid content ID',
                'status' => 404
            ], 404);
        }

        $content = Contents::with(['teacher', 'playlist'])->find($id);
        
        if (!$content) {
            return response()->json([
                'message' => 'Content not found',
                'status' => 404
            ], 404);
        }

        // The encrypted_id is automatically added by the accessor in the model
        // Add the encrypted_playlist_id from the playlist relationship
        $content->encrypted_playlist_id = $content->playlist->encrypted_id;

        return response()->json([
            'content' => $content,
            'teacher' => $content->teacher,
            'playlist' => $content->playlist
        ]);
    }

    /**
     * Get teacher's content amount
     */
    public function get_amount($encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'data' => 0
                ]);
            }
            return response()->json([
                'data' => Contents::where('teacher_id', $id)->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => 0
            ]);
        }
    }

    /**
     * Get playlist's content amount
     */
    public function get_playlist_contents_amount($encryptedId)
    {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid playlist ID',
                'status' => 404
            ], 404);
        }

        return Contents::where('playlist_id', $id)->count();
    }

    /**
     * Add new content
     */
    public function add_content(Request $request)
    {
        try {
            $validator = $this->validateContent($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            if (empty($request->status)) {
                return $this->errorResponse(['Please select content status']);
            }

            $content = new Contents();
            $contentData = [
                'teacher_id' => $request->teacher_id,
                'playlist_id' => $request->playlist_id,
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'date' => Carbon::now(),
                'video_source_type' => $request->video_source_type
            ];

            // Handle thumbnail upload
            if ($request->hasFile('thumb')) {
                $contentData['thumb'] = $this->handleFileUpload($request->thumb, self::CONTENT_THUMBS_PATH);
            }

            // Handle video source based on type
            if ($request->video_source_type === 'file') {
                if (!$request->hasFile('video')) {
                    return $this->errorResponse(['Please upload a video file']);
                }
                $contentData['video'] = $this->handleFileUpload($request->video, self::CONTENT_VIDEOS_PATH);
            } else if ($request->video_source_type === 'youtube') {
                if (empty($request->youtube_link)) {
                    return $this->errorResponse(['Please provide a YouTube video URL']);
                }
                $contentData['video'] = $request->youtube_link;
                
                // Handle YouTube thumbnail
                if ($request->has('youtube_thumb')) {
                    $contentData['thumb'] = $request->youtube_thumb;
                }
            }

            $content->fill($contentData);
            $content->save();

            return $this->successResponse('Content uploaded successfully');
        } catch (\Exception $e) {
            \Log::error('Error adding content: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return $this->errorResponse(['Failed to upload content. Please try again later.']);
        }
    }

    /**
     * Update content
     */
    public function store(Request $request, string $id)
    {
        try {
            \Log::info('Content update request received', [
                'content_id' => $id,
                'playlist_id' => $request->playlist_id,
                'all_data' => $request->all()
            ]);

            $validator = $this->validateContent($request);
            if ($validator->fails()) {
                \Log::error('Content update validation failed', [
                    'errors' => $validator->messages()->all()
                ]);
                return $this->errorResponse($validator->messages()->all());
            }

            $content = Contents::findOrFail($id);
            \Log::info('Current content state', [
                'content_id' => $id,
                'old_playlist_id' => $content->playlist_id
            ]);
            
            $updateData = [
                'status' => $request->status,
                'title' => $request->title,
                'description' => $request->description,
                'playlist_id' => $request->playlist_id
            ];

            \Log::info('Updating content with data', [
                'content_id' => $id,
                'update_data' => $updateData
            ]);

            $content->update($updateData);

            \Log::info('Content updated successfully', [
                'content_id' => $id,
                'new_playlist_id' => $content->fresh()->playlist_id
            ]);

            return $this->successResponse('Content updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update content', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(['Failed to update content: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete content
     */
    public function delete($id)
    {
        try {
            $content = Contents::findOrFail($id);
            
            // Delete associated comments
            Comments::where('content_id', $id)->delete();
            
            // Delete associated likes
            Likes::where('content_id', $id)->delete();
            
            // Delete the content
            $content->delete();
            
            return $this->successResponse('Content deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to delete content', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(['Failed to delete content: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($file, string $path)
    {
        if (!$file) {
            throw new \Exception('File not provided');
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($path, $fileName, 'public');
        return '/storage/app/public/' . $filePath;
    }

    /**
     * Validate content request
     */
    private function validateContent(Request $request)
    {
        return Validator::make($request->all(), [
            'playlist_id' => 'required',
            'title' => 'required|max:50',
            'description' => 'required',
            'status' => 'required'
        ], [
            'playlist_id.required' => 'Please select playlist',
            'title.required' => 'Please enter content title',
            'description.required' => 'Please enter content description',
            'status.required' => 'Please select content status'
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
