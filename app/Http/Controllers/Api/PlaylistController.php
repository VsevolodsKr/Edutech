<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Playlists;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class PlaylistController extends Controller
{
    public function get_playlists(string $id){
        return Playlists::where('teacher_id', $id)->orderBy('date', 'desc')->get();
    }

    public function add_playlist(Request $request){
        $rules = array(
            'status' => 'required',
            'title' => 'required|max:50',
            'description' => 'required',
        );
        $messages = array(
            'status.required' => 'Please, select playlist status',
            'title.required' => 'Please, enter playlist title',
            'description.required' => 'Please, enter playlist description',
        );
        $validator = Validator::make($request->input(), $rules, $messages);
       if($validator->fails()){
            $errors = $validator->messages()->all();
            return response()->json(['message' => $errors, 'status' => 500], 500);
        }
        if($request->status == ''){
            return response()->json(['message' => array('Plase, select playlist status'), 'status' => 500], 500);
        }
        $thumb_name = time() . '_' . $request->image->getClientOriginalName();
        $thumb_path = $request->image->storeAs('playlist_thumbs', $thumb_name, 'public');
        $thumb = '/storage/app/public/' . $thumb_path;
        $dt = Carbon::now();
        $playlist = new Playlists;
        $playlist->teacher_id = $request->teacher_id;
        $playlist->title = $request->title;
        $playlist->description = $request->description;
        $playlist->thumb = $thumb;
        $playlist->date = $dt->toDateString();
        $playlist->status = $request->status;
        $save = $playlist->save();
        if(!$save){
            return response()->json(['message' => array('Something went wrong, please try again later!'), 'status' => 500], 500);
        }else{
            return response()->json(['message' => array('You have succesfully created new playlist!'), 'status' => 200], 200);
        }
    }
}
