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

    public function get_teacher_contents(string $encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'message' => 'Nepareizs pasniedzēja ID',
                    'status' => 404
                ], 404);
            }

            $contents = Contents::where('teacher_id', $id)
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($content) {
                    return [
                        'id' => $content->id,
                        'encrypted_id' => $this->encryptId($content->id),
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
        } catch (\Exception $e) {
            \Log::error('Error getting teacher contents: ' . $e->getMessage());
            return response()->json([
                'message' => 'Neizdevās iegūt video sarakstu',
                'status' => 500
            ], 500);
        }
    }

    public function get_playlist_contents($encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'message' => 'Nepareizs kursa ID',
                    'status' => 404
                ], 404);
            }

            $contents = Contents::with('teacher')
                ->where('playlist_id', $id)
                ->orderBy('date', 'asc')
                ->get()
                ->filter(function ($content) {
                    return $content->teacher && $content->teacher->status === 'aktīvs';
                })
                ->map(function ($content) {
                    return [
                        'id' => $content->id,
                        'encrypted_id' => $this->encryptId($content->id),
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neizdevās iegūt kursa saturus',
                'status' => 500
            ], 500);
        }
    }

    public function get_single($encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'message' => 'Nepareizs video ID',
                    'status' => 404
                ], 404);
            }

            $content = Contents::with(['teacher', 'playlist'])->find($id);
            
            if (!$content) {
                return response()->json([
                    'message' => 'Video nav atrasts',
                    'status' => 404
                ], 404);
            }

            if (!$content->teacher || $content->teacher->status === 'neaktīvs') {
                return response()->json([
                    'message' => 'Šis video nav pieejams, jo pasniedzējs ir deaktivizēts',
                    'status' => 403
                ], 403);
            }

            $content->encrypted_playlist_id = $content->playlist->encrypted_id;

            return response()->json([
                'content' => $content,
                'teacher' => $content->teacher,
                'playlist' => $content->playlist
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neizdevās iegūt video datus',
                'status' => 500
            ], 500);
        }
    }

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

    public function add_content(Request $request)
    {
        try {
            $validator = $this->validateContent($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            if (empty($request->status)) {
                return $this->errorResponse(['Izvēlējiet video statusu']);
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

            if ($request->hasFile('thumb')) {
                $contentData['thumb'] = $this->handleFileUpload($request->thumb, self::CONTENT_THUMBS_PATH);
            }

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

    public function store(Request $request, string $encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return $this->errorResponse(['Nepareizs video ID']);
            }

            $validator = $this->validateContent($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            $content = Contents::findOrFail($id);
            
            $updateData = [
                'status' => $request->status,
                'title' => $request->title,
                'description' => $request->description,
                'playlist_id' => $request->playlist_id
            ];

            $content->update($updateData);

            return $this->successResponse('Video veiksmīgi rediģēts');
        } catch (\Exception $e) {
            return $this->errorResponse(['Neizdevās rediģēt video: ' . $e->getMessage()]);
        }
    }

    public function delete($encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return $this->errorResponse(['Nepareizs video ID']);
            }

            $content = Contents::findOrFail($id);
            
            Comments::where('content_id', $id)->delete();
            
            Likes::where('content_id', $id)->delete();
            
            $content->delete();
            
            return $this->successResponse('Video veiksmīgi dzēsts');
        } catch (\Exception $e) {
            return $this->errorResponse(['Neizdevās dzēst video: ' . $e->getMessage()]);
        }
    }

    private function handleFileUpload($file, string $path)
    {
        if (!$file) {
            throw new \Exception('File not provided');
        }

        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($path, $fileName, 'public');
        return '/storage/app/public/' . $filePath;
    }

    private function validateContent(Request $request)
    {
        return Validator::make($request->all(), [
            'playlist_id' => 'required',
            'title' => 'required|max:50',
            'description' => 'required',
            'status' => 'required'
        ], [
            'playlist_id.required' => 'Izvēlēties kursu',
            'title.required' => 'Ievadiet video nosaukumu',
            'description.required' => 'Ievadiet video aprakstu',
            'status.required' => 'Izvēlēties video statusu'
        ]);
    }

    private function errorResponse(array $messages, int $status = 500)
    {
        return response()->json([
            'message' => $messages,
            'status' => $status
        ], $status);
    }

    private function successResponse(string $message, array $data = [], int $status = 200)
    {
        return response()->json(array_merge([
            'message' => [$message],
            'status' => $status
        ], $data), $status);
    }
}
