<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comments;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Traits\Encryptable;

class CommentsController extends Controller
{
    use Encryptable;

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

    public function count_comments(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid content ID',
                'status' => 404
            ], 404);
        }
        return Comments::where('content_id', $id)->count();
    }

    public function get_video_comments($encryptedId) {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'message' => 'Invalid content ID',
                    'status' => 404
                ], 404);
            }

            $comments = Comments::with(['user' => function($query) {
                $query->select('id', 'name', 'image');
            }])
            ->where('content_id', $id)
            ->orderBy('date', 'desc')
            ->get();

            if ($comments->isEmpty()) {
                return response()->json([
                    'comments' => [],
                    'status' => 200
                ]);
            }

            $formattedComments = $comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'encrypted_id' => $comment->encrypted_id,
                    'comment' => $comment->comment,
                    'date' => $comment->date,
                    'user' => $comment->user ? [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'image' => $comment->user->image
                    ] : null
                ];
            });

            return response()->json([
                'comments' => $formattedComments,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting video comments: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'message' => 'Error loading comments',
                'status' => 500
            ], 500);
        }
    }

    public function delete_comment(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid comment ID',
                'status' => 404
            ], 404);
        }
        $comment = Comments::find($id);
        if (!$comment) {
            return response()->json([
                'message' => 'Comment not found',
                'status' => 404
            ], 404);
        }
        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully', 'status' => 200], 200);
    }

    public function find(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid comment ID',
                'status' => 404
            ], 404);
        }
        $comment = Comments::find($id);
        if (!$comment) {
            return response()->json([
                'message' => 'Comment not found',
                'status' => 404
            ], 404);
        }
        $content = $comment->content;
        $playlist = $content->playlist;
        return response()->json(['comment' => $comment, 'content' => $content, 'playlist' => $playlist]);
    }

    public function edit_comment(string $encryptedId, Request $request) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid comment ID',
                'status' => 404
            ], 404);
        }
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
        if (!$comment) {
            return response()->json([
                'message' => 'Comment not found',
                'status' => 404
            ], 404);
        }
        $comment->update(['comment' => $request->comment]);
        $save = $comment->save();
        if(!$save){
            return response()->json(['message' => 'Something went wrong! Try again later!', 'status' => 500], 500);
        } else {
            return response()->json(['message' => 'Comment has been corrected!', 'status' => 200], 200);
        }
    }

    public function count_user(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid user ID',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'data' => Comments::where('user_id', $id)->count()
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
            'data' => Comments::where('teacher_id', $id)->count()
        ]);
    }

    public function get_user_comments(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid user ID',
                'status' => 404
            ], 404);
        }
        $comments = Comments::with('content')->where('user_id', $id)->get();
        
        return response()->json([
            'comments' => $comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'encrypted_id' => $comment->encrypted_id,
                    'content_id' => $comment->content_id,
                    'user_id' => $comment->user_id,
                    'teacher_id' => $comment->teacher_id,
                    'comment' => $comment->comment,
                    'date' => $comment->date,
                    'content' => $comment->content ? [
                        'id' => $comment->content->id,
                        'encrypted_id' => $comment->content->encrypted_id,
                        'title' => $comment->content->title,
                        'description' => $comment->content->description,
                        'video' => $comment->content->video,
                        'thumb' => $comment->content->thumb,
                        'date' => $comment->content->date,
                        'status' => $comment->content->status,
                        'video_source_type' => $comment->content->video_source_type,
                        'teacher_id' => $comment->content->teacher_id,
                        'playlist_id' => $comment->content->playlist_id
                    ] : null
                ];
            })
        ]);
    }

    public function get_teacher_comments(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Invalid teacher ID',
                'status' => 404
            ], 404);
        }
        $comments = Comments::where('teacher_id', $id)
            ->orderBy('date', 'desc')
            ->get();
        
        $users = array();
        $contents = array();
        foreach($comments as $comment) {
            array_push($users, $comment->user);
            array_push($contents, $comment->content);
        }
        
        return response()->json([
            'comments' => $comments->map(function($comment) {
                return [
                    ...$comment->toArray(),
                    'encrypted_id' => $comment->encrypted_id
                ];
            }),
            'users' => $users,
            'contents' => $contents
        ]);
    }
}
