<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Likes;
use App\Models\Contents;

class LikesController extends Controller
{

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

    public function delete_like(string $id){
        $check = Likes::find($id);
        $check->delete();
    }

    public function count_content_likes(string $id){
        return Likes::where('content_id', $id)->count();
    }

    public function count_user_likes(string $id){
        return Likes::where('user_id', $id)->count();
    }

    public function get_user_likes(string $id){
        $liked_contents = Likes::where('user_id', $id)->get();
        $teachers = array();
        $contents = array();
        foreach($liked_contents as $content){
            array_push($teachers, $content->teacher);
            array_push($contents, $content->content);
        }
        return response()->json(['teachers' => $teachers, 'contents' => $contents]);
    }
}
