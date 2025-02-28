<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comments;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class CommentsController extends Controller
{
    public function add_comment(Request $request){
        $rules = array(
            'content_id' => 'required',
            'user_id' => 'required',
            'teacher_id' => 'required',
            'comment' => 'required'
        );
        $messages = array(
            'content_id.required' => 'System error, try again later',
            'user_id.required' => 'You need to login, to write comments',
            'teacher_id.required' => 'System error, try again later',
            'comment.required' => 'Please, enter your comment'
        );
        $validator = Validator::make($request->input(), $rules, $messages);
       if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
            $comment = new Comments;
            $comment->content_id = $request->content_id;
            $comment->user_id = $request->user_id;
            $comment->teacher_id = $request->teacher_id;
            $comment->comment = $request->comment;
            $dt = Carbon::now();
            $comment->date = $dt->toDateString();
            $save = $comment->save();
            if(!$save) {
                return response()->json(['message' => array('Something went wrong, try again later!'), 'status' => 500], 500);
            } else {
                return response()->json(['message' => array('Your comment is successfully added!'), 'status' => 200], 200);
            }
    }

    public function count_comments(string $id) {
        return Comments::where('content_id', $id)->count();
    }

    public function get_video_comments(string $id) {
        $comments = Comments::where('content_id', $id)->get();
        $users = array();
        foreach($comments as $comment) {
            array_push($users, $comment->user);
        }
        return response()->json(['comments' => $comments, 'users' => $users]);
    }

    public function delete_comment(string $id) {
        $comment = Comments::find($id);
        $comment->delete();
    }

    public function find(string $id) {
        $comment = Comments::find($id);
        $content = $comment->content;
        $playlist = $content->playlist;
        return response()->json(['comment' => $comment, 'content' => $content, 'playlist' => $playlist]);
    }

    public function edit_comment(string $id, Request $request) {
        $rules = array(
            'comment' => 'required',
        );
        $messages = array(
            'comment.required' => 'Please, enter your comment'
        );
        $validator = Validator::make($request->input(), $rules, $messages);
       if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
        $comment = Comments::find($id);
        $comment->update(['comment' => $request->comment]);
        $save = $comment->save();
        if(!$save){
            return response()->json(['message' => 'Something went wrong! Try again later!', 'status' => 500], 500);
        } else {
            return response()->json(['message' => 'Comment has been corrected!', 'status' => 200], 200);
        }
    }

    public function count_user(string $id) {
        return response()->json([
            'data' => Comments::where('user_id', $id)->count()
        ]);
    }

    public function get_user_comments(string $id) {
        $comments = Comments::where('user_id', $id)->get();
        $contents = array();
        foreach($comments as $comment){
            array_push($contents, $comment->content);
        }
        return response()->json(['comments' => $comments, 'contents' => $contents]);
    }
}
