<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Playlists;
use App\Models\Teachers;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class PlaylistController extends Controller
{
    public function get_all(){
        return Playlists::where('status', 'active')->get();
    }

    public function get_teacher_playlists(string $id){
        return Playlists::where('teacher_id', $id)->orderBy('date', 'desc')->get();
    }

    public function get_single(string $id){
        $playlist =  Playlists::find($id);
        $teacher = $playlist->teacher;
        return response()->json(['playlist' => $playlist, 'teacher' => $teacher]);
    }

    public function get_amount(string $id){
        return Playlists::where('teacher_id', $id)->count();
    }

    public function get_teacher(string $id){
        $teacher = Teachers::find($id);
        return $teacher;
    }

    public function search_playlists(Request $request){
        $playlists = Playlists::where('title', 'like', '%'.$request->name.'%')->orWhere('description', 'like', '%'.$request->name.'%')->get();
        if(count($playlists) > 0){
            return $playlists;
        } else {
            $playlists = Playlists::where('status', 'active')->get();
            $needed_playlists = array();
            foreach($playlists as $playlist){
                $teacher_name = strtolower($playlist->teacher->name);
                if(str_contains($teacher_name, strtolower($request->name))){
                    array_push($needed_playlists, $playlist);
                }
            }
            return $needed_playlists;
        }
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

    public function store(Request $request, string $id){
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
        $playlist = Playlists::find($id);
        if(!$playlist){
            return response()->json(['message' => array('Something went wrong, please try again later!'), 'status' => 500], 500);
        }
        if($request->image == ''){
            $playlist->update([
                'status' => $request->status,
                'title' => $request->title,
                'description' => $request->description 
            ]);
        }else{
            $thumb_name = time() . '_' . $request->image->getClientOriginalName();
            $thumb_path = $request->image->storeAs('playlist_thumbs', $thumb_name, 'public');
            $thumb = '/storage/app/public/' . $thumb_path;
            $playlist->update([
                'status' => $request->status,
                'title' => $request->title,
                'description' => $request->description ,
                'thumb' => $thumb
            ]);
        }
        $save = $playlist->save();
        if(!$save){
            return response()->json(['message' => array('Something went wrong, please try again later!'), 'status' => 500], 500);
        }else{
            return response()->json(['message' => array('You have successfully updated your playlist!'), 'status' => 200], 200);
        }
    }

    public function delete(string $id){
        $playlist = Playlists::find($id);
        $playlist->delete();
    }
}
