<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bookmarks;

class BookmarksController extends Controller
{

    public function check_bookmark(Request $request){
        $check = Bookmarks::where([['user_id', '=', $request->user_id], ['playlist_id', '=', $request->playlist_id]])->first();
        if($check){
            return response()->json(['status' => true, 'id' => $check->id]);
        }else{
            return response()->json(['status' => false]);
        }
    }

    public function add_bookmark(Request $request){
        $bookmark = new Bookmarks;
        $bookmark->user_id = $request->user_id;
        $bookmark->playlist_id = $request->playlist_id;
        $bookmark->save();
    }

    public function delete_bookmark(string $id){
        $check = Bookmarks::find($id);
        $check->delete();
    }

    public function count_user_bookmarks(string $id){
        return Bookmarks::where('user_id', $id)->count();
    }

    public function get_user_bookmarks(string $id){
        $bookmarks = Bookmarks::where('user_id', $id)->get();
        $playlists = array();
        $teachers = array();
        foreach($bookmarks as $bookmark){
            array_push($playlists, $bookmark->playlist);
        }
        foreach($playlists as $playlist){
            array_push($teachers, $playlist->teacher);
        }
        return response()->json(['playlists' => $playlists, 'bookmarks' => $bookmarks, 'teachers' => $teachers]);
    }
}
