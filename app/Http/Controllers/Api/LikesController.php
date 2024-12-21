<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Likes;

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
}
