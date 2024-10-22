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

class ContentsController extends Controller
{
    public function get_teacher_contents(string $id){
        return Contents::where('teacher_id', $id)->orderBy('date', 'desc')->get();
    }

    public function add_content(Request $request){
        $rules = array(
            'playlist_id' => 'required',    
            'title' => 'required',
            'description' => 'required|max:50',
            'status' => 'required',
        );
        $messages = array(
            'playlist_id.required' => 'Please select playlist',
            'title.required' => 'Please, enter content title',
            'description.required' => 'Please, enter content description',
            'status.required' => 'Please, select content status',
        );
        $validator = Validator::make($request->input(), $rules, $messages);
       if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
        if($request->status == ''){
            return response()->json(['message' => array('Plase, select content status'), 'status' => 500], 500);
        }
        $thumb_name = time() . '_' . $request->thumb->getClientOriginalName();
        $thumb_path = $request->thumb->storeAs('content_thumbs', $thumb_name, 'public');
        $thumb = '/storage/app/public/' . $thumb_path;
        $video_name = time() . '_' . $request->video->getClientOriginalName();
        $video_path = $request->video->storeAs('content_videos', $video_name, 'public');
        $video = '/storage/app/public/' . $video_path;
        $dt = Carbon::now();
        $content = new Contents;
        $content->teacher_id = $request->teacher_id;
        $content->playlist_id = $request->playlist_id;
        $content->title = $request->title;
        $content->description = $request->description;
        $content->video = $video;
        $content->thumb = $thumb;
        $content->date = $dt;
        $content->status = $request->status;
        $save = $content->save();
        if(!$save){
            return response()->json(['message' => array('Something went wrong, please try again later!'), 'status' => 500], 500);
        }else{
            return response()->json(['message' => array('You have succesfully uploaded new content!'), 'status' => 200], 200);
        }
    }

    public function delete(string $id){
        $content = Contents::find($id);
        $content->delete();
    }
}
