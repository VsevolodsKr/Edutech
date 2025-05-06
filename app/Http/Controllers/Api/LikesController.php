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
        $check = Likes::where([['user_id', '=', $request->user_id], ['teacher_id', '=', $request->teacher_id], ['content_id', '=', $request->content_id]])->first();
        if($check){
            return response()->json(['status' => true, 'id' => $check->id]);
        }else{
            return response()->json(['status' => false]);
        }
    }

    public function add_like(Request $request){
        $like = new Likes;
        $like->user_id = $request->user_id;
        $like->teacher_id = $request->teacher_id;
        $like->content_id = $request->content_id;
        $like->save();
    }

    public function delete_like(string $encryptedId){
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid like ID',
                'status' => 404
            ], 404);
        }
        $check = Likes::find($id);
        if (!$check) {
            return response()->json([
                'message' => 'Like not found',
                'status' => 404
            ], 404);
        }
        $check->delete();
        return response()->json(['message' => 'Like deleted successfully', 'status' => 200], 200);
    }

    public function count_content_likes(string $encryptedId){
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid content ID',
                'status' => 404
            ], 404);
        }
        return Likes::where('content_id', $id)->count();
    }

    public function count_user_likes(string $encryptedId){
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid user ID',
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
                'message' => 'Invalid teacher ID',
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
                'message' => 'Invalid user ID',
                'status' => 404
            ], 404);
        }
        
        $likes = Likes::where('user_id', $id)->get();
        $contents = [];
        
        foreach($likes as $like) {
            $content = Contents::find($like->content_id);
            if ($content) {
                $contents[] = [
                    'id' => $content->id,
                    'encrypted_id' => $content->encrypted_id,
                    'title' => $content->title,
                    'description' => $content->description,
                    'video' => $content->video,
                    'thumb' => $content->thumb,
                    'date' => $content->date,
                    'teacher_id' => $content->teacher_id,
                    'video_source_type' => $content->video_source_type
                ];
            }
        }
        
        return response()->json([
            'likes' => $likes,
            'contents' => $contents
        ]);
    }
}
