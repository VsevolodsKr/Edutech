<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Likes;
use App\Models\Contents;
use App\Traits\Encryptable;

class LikesController extends Controller
{
    use Encryptable;

    public function check_like(Request $request){
        try {
            $contentId = $this->decryptId($request->content_id);
            if (!$contentId) {
                return response()->json([
                    'message' => 'Invalid content ID',
                    'status' => 404
                ], 404);
            }

            $check = Likes::where([
                ['user_id', '=', $request->user_id],
                ['teacher_id', '=', $request->teacher_id],
                ['content_id', '=', $contentId]
            ])->first();

            if($check){
                return response()->json(['status' => true, 'id' => $this->encryptId($check->id)]);
            }else{
                return response()->json(['status' => false]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neizdevās pārbaudīt favorītvideo statu',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function add_like(Request $request){
        try {
            $contentId = $this->decryptId($request->content_id);
            if (!$contentId) {
                return response()->json([
                    'message' => 'Nepareizs video ID',
                    'status' => 404
                ], 404);
            }

            $like = new Likes;
            $like->user_id = $request->user_id;
            $like->teacher_id = $request->teacher_id;
            $like->content_id = $contentId;
            $like->save();

            return response()->json([
                'message' => 'Favorītvideo ir pievienots',
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neizdevās pievienot favorītvideo',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function delete_like(string $encryptedId){
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs favorītvideo ID',
                'status' => 404
            ], 404);
        }
        $check = Likes::find($id);
        if (!$check) {
            return response()->json([
                'message' => 'Favorītvideo nav atrasts',
                'status' => 404
            ], 404);
        }
        $check->delete();
        return response()->json(['message' => 'Favorītvideo ir dzēsts', 'status' => 200], 200);
    }

    public function count_content_likes(string $encryptedId){
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs video ID',
                'status' => 404
            ], 404);
        }
        return Likes::where('content_id', $id)->count();
    }

    public function count_user_likes(string $encryptedId){
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs lietotāja ID',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'data' => Likes::where('user_id', $id)->count()
        ]);
    }

    public function count_teacher(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs pasniedzēja ID',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'data' => Likes::where('teacher_id', $id)->count()
        ]);
    }

    public function get_user_likes(string $encryptedId){
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs lietotāja ID',
                'status' => 404
            ], 404);
        }
        
        $likes = Likes::with(['content' => function($query) {
            $query->select('id', 'title', 'description', 'video', 'thumb', 'date', 'teacher_id', 'video_source_type');
        }, 'content.teacher' => function($query) {
            $query->select('id', 'name', 'image');
        }])
        ->where('user_id', $id)
        ->get();
        
        $transformedData = $likes->map(function($like) {
            $content = $like->content;
            if (!$content) return null;
            
            return [
                'id' => $content->id,
                'encrypted_id' => $content->encrypted_id,
                'title' => $content->title,
                'description' => $content->description,
                'video' => $content->video,
                'thumb' => $content->thumb,
                'date' => $content->date,
                'teacher_id' => $content->teacher_id,
                'video_source_type' => $content->video_source_type,
                'like_id' => $like->encrypted_id,
                'teacher' => $content->teacher ? [
                    'id' => $content->teacher->id,
                    'name' => $content->teacher->name,
                    'image' => $content->teacher->image
                ] : null
            ];
        })->filter()->values();
        
        return response()->json([
            'contents' => $transformedData
        ]);
    }
}
