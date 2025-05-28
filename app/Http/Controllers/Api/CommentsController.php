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
        try {
            $rules = array(
                'content_id' => 'required',
                'user_id' => 'required',
                'teacher_id' => 'required',
                'comment' => 'required'
            );
            $messages = array(
                'content_id.required' => 'Sistēmas kļūda, mēģiniet vēlreiz',
                'user_id.required' => 'Jums ir jāpiesakās, lai rakstītu komentārus',
                'teacher_id.required' => 'Kļūda, mēģiniet vēlreiz',
                'comment.required' => 'Lūdzu, ievadiet savu komentāru'
            );
            
            $validator = Validator::make($request->all(), $rules, $messages);
            if($validator->fails()){
                $errors = $validator->messages()->all();
                return response()->json(['message' => $errors, 'status' => 500], 500);
            }

            $comment = new Comments;
            $comment->content_id = $request->content_id;
            $comment->user_id = $request->user_id;
            $comment->teacher_id = $request->teacher_id;
            $comment->comment = $request->comment;
            $comment->date = Carbon::now()->toDateString();
            
            $save = $comment->save();
            
            if(!$save) {
                return response()->json(['message' => ['Kaut kas nogāja greizi, mēģiniet vēlreiz!'], 'status' => 500], 500);
            }
            
            return response()->json(['message' => ['Jūsu komentārs ir veiksmīgi pievienots!'], 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => ['Neizdevās pievienot komentāru: ' . $e->getMessage()], 'status' => 500], 500);
        }
    }

    public function count_comments(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs saturs ID',
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
                    'message' => 'Nepareizs saturs ID',
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
                'message' => 'Komentāru ielādei ir radusies kļūda',
                'status' => 500
            ], 500);
        }
    }

    public function delete_comment(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs komentāra ID',
                'status' => 404
            ], 404);
        }
        $comment = Comments::find($id);
        if (!$comment) {
            return response()->json([
                'message' => 'Komentārs nav atrasts',
                'status' => 404
            ], 404);
        }
        $comment->delete();
        return response()->json(['message' => 'Komentārs veiksmīgi dzēsts', 'status' => 200], 200);
    }

    public function find(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs komentāra ID',
                'status' => 404
            ], 404);
        }
        $comment = Comments::find($id);
        if (!$comment) {
            return response()->json([
                'message' => 'Komentārs nav atrasts',
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
                'message' => 'Nepareizs komentāra ID',
                'status' => 404
            ], 404);
        }
        $rules = array(
            'comment' => 'required',
        );
        $messages = array(
            'comment.required' => 'Lūdzu, ievadiet savu komentāru'
        );
        $validator = Validator::make($request->input(), $rules, $messages);
       if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
        $comment = Comments::find($id);
        if (!$comment) {
            return response()->json([
                'message' => 'Komentārs nav atrasts',
                'status' => 404
            ], 404);
        }
        $comment->update(['comment' => $request->comment]);
        $save = $comment->save();
        if(!$save){
            return response()->json(['message' => 'Kaut kas nogāja greizi, mēģiniet vēlreiz!', 'status' => 500], 500);
        } else {
            return response()->json(['message' => 'Komentārs ir veiksmīgi rediģēts!', 'status' => 200], 200);
        }
    }

    public function count_user(string $encryptedId) {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs lietotāja ID',
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
                'message' => 'Nepareizs pasniedzēja ID',
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
                'message' => 'Nepareizs lietotāja ID',
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
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'message' => 'Nepareizs pasniedzēja ID',
                    'status' => 404
                ], 404);
            }
            
            $comments = Comments::with(['user', 'content'])
                ->whereHas('content', function($query) use ($id) {
                    $query->where('teacher_id', $id);
                })
                ->orderBy('date', 'desc')
                ->get();

            if (!$comments) {
                return response()->json([
                    'comments' => [],
                    'users' => [],
                    'contents' => []
                ]);
            }

            $formattedComments = $comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'encrypted_id' => $comment->encrypted_id,
                    'content_id' => $comment->content_id,
                    'user_id' => $comment->user_id,
                    'teacher_id' => $comment->teacher_id,
                    'comment' => $comment->comment,
                    'date' => $comment->date,
                    'user' => $comment->user ? [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'image' => $comment->user->image
                    ] : null,
                    'content' => $comment->content ? [
                        'id' => $comment->content->id,
                        'encrypted_id' => $comment->content->encrypted_id,
                        'title' => $comment->content->title,
                        'thumb' => $comment->content->thumb,
                        'video' => $comment->content->video,
                        'video_source_type' => $comment->content->video_source_type
                    ] : null
                ];
            });

            return response()->json([
                'comments' => $formattedComments,
                'users' => $comments->map(function($comment) {
                    return $comment->user;
                })->filter(),
                'contents' => $comments->map(function($comment) {
                    return $comment->content;
                })->filter()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in get_teacher_comments: ' . $e->getMessage());
            return response()->json([
                'message' => 'Neizdevās ielādēt komentārus',
                'status' => 500
            ], 500);
        }
    }
}
