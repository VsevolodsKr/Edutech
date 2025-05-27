<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bookmarks;
use App\Traits\Encryptable;

class BookmarksController extends Controller
{
    use Encryptable;

    public function check_bookmark(Request $request){
        try {
            $playlistId = $this->decryptId($request->playlist_id);
            if (!$playlistId) {
                return response()->json([
                    'message' => 'Nepareizs kursa ID',
                    'status' => 404
                ], 404);
            }

            $check = Bookmarks::where([
                ['user_id', '=', $request->user_id],
                ['playlist_id', '=', $playlistId]
            ])->first();

            if($check){
                return response()->json(['status' => true, 'id' => $check->id]);
            }else{
                return response()->json(['status' => false]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neizdevās pārbaudīt atzīmēšanas statu',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function add_bookmark(Request $request){
        try {
            $playlistId = $this->decryptId($request->playlist_id);
            if (!$playlistId) {
                return response()->json([
                    'message' => 'Nepareizs kursa ID',
                    'status' => 404
                ], 404);
            }

            $bookmark = new Bookmarks;
            $bookmark->user_id = $request->user_id;
            $bookmark->playlist_id = $playlistId;
            $bookmark->save();

            return response()->json([
                'message' => 'Grāmatzīme veiksmīgi pievienota',
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neizdevās pievienot grāmatzīmi',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function delete_bookmark(string $id){
        try {
            $bookmark = Bookmarks::find($id);
            if (!$bookmark) {
                return response()->json([
                    'message' => 'Grāmatzīme nav atrasta',
                    'status' => 404
                ], 404);
            }

            $bookmark->delete();
            return response()->json([
                'message' => 'Grāmatzīme veiksmīgi dzēsta',
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neizdevās dzēst grāmatzīmi',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function count_user_bookmarks(string $encryptedId){
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs lietotāja ID',
                'status' => 404
            ], 404);
        }
        return response()->json([
            'data' => Bookmarks::where('user_id', $id)->count()
        ]);
    }

    public function get_user_bookmarks(string $encryptedId){
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'message' => 'Nepareizs lietotāja ID',
                    'status' => 404
                ], 404);
            }

            // Eager load relationships with specific attributes
            $bookmarks = Bookmarks::with(['playlist' => function($query) {
                $query->with(['teacher' => function($query) {
                    $query->select('id', 'name', 'image', 'profession');
                }]);
            }])->where('user_id', $id)->get();

            $playlists = [];
            $teachers = [];

            foreach($bookmarks as $bookmark) {
                if ($bookmark->playlist) {
                    // Format the playlist thumbnail URL if it exists
                    if ($bookmark->playlist->thumb) {
                        $bookmark->playlist->thumb = str_replace('/storage/app/public/', '/storage/', $bookmark->playlist->thumb);
                    }
                    
                    // Add bookmark ID to playlist for reference
                    $bookmark->playlist->bookmark_id = $bookmark->id;
                    
                    $playlists[] = $bookmark->playlist;

                    if ($bookmark->playlist->teacher) {
                        $teachers[] = $bookmark->playlist->teacher;
                    }
                }
            }

            return response()->json([
                'message' => 'Grāmatzīmes veiksmīgi ielādētas',
                'playlists' => $playlists,
                'bookmarks' => $bookmarks,
                'teachers' => array_values(array_unique($teachers, SORT_REGULAR)),
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neizdevās ielādēt grāmatzīmes',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
