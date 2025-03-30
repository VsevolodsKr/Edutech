<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    private const CACHE_TTL = 3600; // 1 hour
    private const CONTENT_THUMBS_PATH = 'content_thumbs';
    private const CONTENT_VIDEOS_PATH = 'content_videos';

    /**
     * Get teacher's contents
     */
    public function get_teacher_contents(string $id)
    {
        return Cache::remember("teacher.contents.{$id}", self::CACHE_TTL, function () use ($id) {
            return Contents::where('teacher_id', $id)
                ->orderBy('date', 'desc')
                ->get();
        });
    }

    /**
     * Get playlist contents
     */
    public function get_playlist_contents(string $id)
    {
        return Cache::remember("playlist.contents.{$id}", self::CACHE_TTL, function () use ($id) {
            return Contents::where('playlist_id', $id)
                ->orderBy('date', 'asc')
                ->get();
        });
    }

    /**
     * Get single content
     */
    public function get_single(string $id)
    {
        return Cache::remember("content.{$id}", self::CACHE_TTL, function () use ($id) {
            $content = Contents::with(['teacher', 'playlist'])->find($id);
            return response()->json([
                'content' => $content,
                'teacher' => $content->teacher,
                'playlist' => $content->playlist
            ]);
        });
    }

    /**
     * Get teacher's content amount
     */
    public function get_amount(string $id)
    {
        return Cache::remember("teacher.contents.count.{$id}", self::CACHE_TTL, function () use ($id) {
            return Contents::where('teacher_id', $id)->count();
        });
    }

    /**
     * Get playlist's content amount
     */
    public function get_playlist_contents_amount(string $id)
    {
        return Cache::remember("playlist.contents.count.{$id}", self::CACHE_TTL, function () use ($id) {
            return Contents::where('playlist_id', $id)->count();
        });
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
            $content->fill([
                'teacher_id' => $request->teacher_id,
                'playlist_id' => $request->playlist_id,
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'date' => Carbon::now(),
                'thumb' => $this->handleFileUpload($request->thumb, self::CONTENT_THUMBS_PATH),
                'video' => $this->handleFileUpload($request->video, self::CONTENT_VIDEOS_PATH)
            ]);

            $content->save();
            $this->clearContentCaches($content);

            return $this->successResponse('Content uploaded successfully');
        } catch (\Exception $e) {
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
            $this->clearContentCaches($content);

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
     * Clear content related caches
     */
    private function clearContentCaches($content)
    {
        Cache::forget("teacher.contents.{$content->teacher_id}");
        Cache::forget("playlist.contents.{$content->playlist_id}");
        Cache::forget("content.{$content->id}");
        Cache::forget("teacher.contents.count.{$content->teacher_id}");
        Cache::forget("playlist.contents.count.{$content->playlist_id}");
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
