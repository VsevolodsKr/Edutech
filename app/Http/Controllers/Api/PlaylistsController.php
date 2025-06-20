<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Encryptable;
use Illuminate\Http\Request;
use App\Models\Playlists;
use App\Models\Teachers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Contents;

class PlaylistsController extends Controller
{
    use Encryptable;

    private const PLAYLIST_THUMBS_PATH = 'playlist_thumbs';

    public function all()
    {
        try {
            $playlists = Playlists::with('teacher')
                ->orderBy('date', 'desc')
                ->get()
                ->filter(function ($playlist) {
                    return $playlist->teacher && $playlist->teacher->status === 'aktīvs';
                })
                ->map(function ($playlist) {
                    return [
                        'id' => $playlist->id,
                        'encrypted_id' => $this->encryptId($playlist->id),
                        'title' => $playlist->title,
                        'description' => $playlist->description,
                        'thumb' => $playlist->thumb,
                        'date' => $playlist->date,
                        'teacher_id' => $playlist->teacher_id,
                        'teacher' => $playlist->teacher ? [
                            'id' => $playlist->teacher->id,
                            'name' => $playlist->teacher->name,
                            'image' => $playlist->teacher->image,
                        ] : null,
                        'content_count' => $playlist->contents()->where('status', 'Aktīvs')->count()
                    ];
                });

            return response()->json($playlists);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neizdevās iegūt kursus',
                'status' => 500
            ], 500);
        }
    }

    public function active()
    {
        return Playlists::with(['teacher', 'contents'])
            ->orderBy('date', 'desc')
            ->where('status', 'Aktīvs')
            ->get()
            ->filter(function ($playlist) {
                return $playlist->teacher && $playlist->teacher->status === 'aktīvs';
            })
            ->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'encrypted_id' => $this->encryptId($playlist->id),
                    'title' => $playlist->title,
                    'description' => $playlist->description,
                    'thumb' => $playlist->thumb,
                    'date' => $playlist->date,
                    'teacher_id' => $playlist->teacher_id,
                    'contents' => $playlist->contents->map(function ($content) {
                        return [
                            'id' => $content->id,
                            'status' => $content->status
                        ];
                    }),
                    'content_count' => $playlist->contents->where('status', 'Aktīvs')->count(),
                    'teacher' => $playlist->teacher ? [
                        'id' => $playlist->teacher->id,
                        'name' => $playlist->teacher->name,
                        'image' => $playlist->teacher->image,
                        'status' => $playlist->teacher->status
                    ] : null
                ];
            });
    }

    public function latest()
    {
        return Playlists::with(['teacher', 'contents'])
            ->orderBy('date', 'desc')
            ->where('status', 'Aktīvs')
            ->get()
            ->filter(function ($playlist) {
                return $playlist->teacher && $playlist->teacher->status === 'aktīvs';
            })
            ->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'encrypted_id' => $this->encryptId($playlist->id),
                    'title' => $playlist->title,
                    'description' => $playlist->description,
                    'thumb' => $playlist->thumb,
                    'date' => $playlist->date,
                    'teacher_id' => $playlist->teacher_id,
                    'contents' => $playlist->contents->map(function ($content) {
                        return [
                            'id' => $content->id,
                            'status' => $content->status
                        ];
                    }),
                    'content_count' => $playlist->contents->where('status', 'Aktīvs')->count(),
                    'teacher' => $playlist->teacher ? [
                        'id' => $playlist->teacher->id,
                        'name' => $playlist->teacher->name,
                        'image' => $playlist->teacher->image,
                        'status' => $playlist->teacher->status
                    ] : null
                ];
            });
    }

    public function find($encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'message' => 'Nepareizs kursa ID',
                    'status' => 404
                ], 404);
            }

            $playlist = Playlists::with('teacher')->find($id);
            if (!$playlist) {
                return response()->json([
                    'message' => 'Kursa dati nav atrasts',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'playlist' => [
                    'id' => $playlist->id,
                    'encrypted_id' => $this->encryptId($playlist->id),
                    'title' => $playlist->title,
                    'description' => $playlist->description,
                    'thumb' => $playlist->thumb,
                    'date' => $playlist->date,
                    'teacher_id' => $playlist->teacher_id,
                    'content_count' => $playlist->contents()->where('status', 'Aktīvs')->count()
                ],
                'teacher' => $playlist->teacher ? [
                    'id' => $playlist->teacher->id,
                    'encrypted_id' => $this->encryptId($playlist->teacher->id),
                    'name' => $playlist->teacher->name,
                    'image' => $playlist->teacher->image,
                ] : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Neizdevās iegūt kursa datus',
                'status' => 500
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100'
        ]);

        if ($validator->fails()) {
            return $this->error_response($validator->messages()->all());
        }

        return Playlists::with(['teacher', 'contents'])
            ->where('title', 'like', '%' . $request->name . '%')
            ->orderBy('date', 'desc')
            ->get()
            ->filter(function ($playlist) {
                return $playlist->teacher && $playlist->teacher->status === 'aktīvs';
            })
            ->map(function ($playlist) {
                return [
                    'id' => $playlist->id,
                    'encrypted_id' => $this->encryptId($playlist->id),
                    'title' => $playlist->title,
                    'description' => $playlist->description,
                    'thumb' => $playlist->thumb,
                    'date' => $playlist->date,
                    'teacher_id' => $playlist->teacher_id,
                    'contents' => $playlist->contents->map(function ($content) {
                        return [
                            'id' => $content->id,
                            'status' => $content->status
                        ];
                    }),
                    'content_count' => $playlist->contents->where('status', 'Aktīvs')->count(),
                    'teacher' => $playlist->teacher ? [
                        'id' => $playlist->teacher->id,
                        'name' => $playlist->teacher->name,
                        'image' => $playlist->teacher->image,
                        'status' => $playlist->teacher->status
                    ] : null
                ];
            });
    }

    public function add(Request $request)
    {
        try {
            $validator = $this->validatePlaylist($request);
            if ($validator->fails()) {
                return $this->error_response($validator->messages()->all());
            }

            $playlist = new Playlists();
            $playlist->status = $request->status;
            $playlist->teacher_id = $request->teacher_id;
            $playlist->title = $request->title;
            $playlist->description = $request->description;
            $playlist->date = Carbon::now();

            if ($request->hasFile('thumb')) {
                $playlist->thumb = $this->handle_file_upload($request->file('thumb'));
            }

            $playlist->save();

            return $this->success_response('Kurss ir veiksmīgi izveidots!');
        } catch (\Exception $e) {
            return $this->error_response(['Neizdevās izveidot kursu: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return $this->error_response(['Nepareizs kursa ID']);
            }

            $validator = $this->validatePlaylist($request);
            if ($validator->fails()) {
                return $this->error_response($validator->messages()->all());
            }

            $playlist = Playlists::findOrFail($id);
            
            $updateData = [
                'teacher_id' => $request->teacher_id,
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status
            ];

            if ($request->hasFile('thumb')) {
                try {
                    $updateData['thumb'] = $this->handle_file_upload($request->file('thumb'));
                    
                    if ($playlist->thumb && Storage::disk('public')->exists($playlist->thumb)) {
                        Storage::disk('public')->delete($playlist->thumb);
                    }
                } catch (\Exception $e) {
                    return $this->error_response(['Neizdevās augšupielādēt kursa attēlu: ' . $e->getMessage()]);
                }
            }
            
            $playlist->update($updateData);

            return $this->success_response('Kurss ir veiksmīgi rediģēts!');
        } catch (\Exception $e) {
            return $this->error_response(['Neizdevās rediģēt kursu: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $playlist = Playlists::findOrFail($id);
            
            Contents::where('playlist_id', $id)->delete();
            
            $playlist->delete();
            
            return $this->success_response('Kurss ir veiksmīgi dzēsts!');
        } catch (\Exception $e) {
            return $this->error_response(['Neizdevās dzēst kursu: ' . $e->getMessage()]);
        }
    }

    public function teacher_playlists($id)
    {
        try {
            $playlists = Playlists::where('teacher_id', $id)
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($playlist) {
                    return [
                        'id' => $playlist->id,
                        'encrypted_id' => $this->encryptId($playlist->id),
                        'title' => $playlist->title,
                        'description' => $playlist->description,
                        'thumb' => $playlist->thumb ? '/storage/' . $playlist->thumb : null,
                        'date' => $playlist->date,
                        'status' => $playlist->status,
                        'content_count' => $playlist->contents()->where('status', 'Aktīvs')->count()
                    ];
                });

            return response()->json([
                'status' => 200,
                'message' => 'Kursi ir veiksmīgi iegūti',
                'data' => $playlists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās iegūt kursus',
                'data' => []
            ], 500);
        }
    }

    public function active_teacher_playlists($encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Nepareizs pasniedzēja ID',
                    'data' => []
                ], 404);
            }

            $teacher = Teachers::find($id);
            if (!$teacher) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Pasniedzējs nav atrasts',
                    'data' => []
                ], 404);
            }

            $playlists = Playlists::where('teacher_id', $id)
                ->where('status', 'Aktīvs')
                ->orderBy('date', 'desc')
                ->get()
                ->map(function ($playlist) {
                    return [
                        'id' => $playlist->id,
                        'encrypted_id' => $this->encryptId($playlist->id),
                        'title' => $playlist->title,
                        'description' => $playlist->description,
                        'thumb' => $playlist->thumb,
                        'date' => $playlist->date,
                        'teacher_id' => $playlist->teacher_id,
                        'content_count' => $playlist->contents()->where('status', 'Aktīvs')->count()
                    ];
                });

            return response()->json([
                'status' => 200,
                'message' => 'Kursi ir veiksmīgi iegūti',
                'data' => $playlists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Neizdevās iegūt pasniedzēja kursus',
                'data' => []
            ], 500);
        }
    }

    public function playlist_teacher($id)
    {
        $playlist = Playlists::findOrFail($id);
        return Teachers::findOrFail($playlist->teacher_id);
    }

    public function get_amount($encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return response()->json([
                    'data' => 0
                ]);
            }
            return response()->json([
                'data' => Playlists::where('teacher_id', $id)->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => 0
            ]);
        }
    }

    private function handle_file_upload($file)
    {
        if (!$file) {
            throw new \Exception('Nav norādīts fails');
        }

        try {
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs(self::PLAYLIST_THUMBS_PATH, $fileName, 'public');
            
            if (!$filePath) {
                throw new \Exception('Neizdevās saglabāt failu');
            }
            
            return $filePath;
        } catch (\Exception $e) {
            throw new \Exception('Neizdevās augšupielādēt failu: ' . $e->getMessage());
        }
    }

    private function validatePlaylist(Request $request)
    {
        $rules = [
            'title' => 'required|max:100',
            'description' => 'required',
            'teacher_id' => 'required|exists:teachers,id',
            'status' => 'required|in:Aktīvs,Neaktīvs'
        ];

        $isNewPlaylist = !$request->route('id');

        if ($isNewPlaylist) {
            $rules['thumb'] = 'required|image|mimes:jpeg,png|max:2048';
        } else {
            if ($request->hasFile('thumb')) {
                $rules['thumb'] = 'image|mimes:jpeg,png|max:2048';
            }
        }

        return Validator::make($request->all(), $rules, [
            'title.required' => 'Ievadiet kursa nosaukumu',
            'description.required' => 'Ievadiet kursa aprakstu',
            'teacher_id.required' => 'Pasniedzēja ID ir obligāts',
            'teacher_id.exists' => 'Nepareizs pasniedzēja ID',
            'status.required' => 'Izvēlieties kursa statusu',
            'status.in' => 'Nepareizs statusa vērtības',
            'thumb.required' => 'Izvēlieties kursa attēlu',
            'thumb.image' => 'Attēls ir jābūt JPEG vai PNG failam',
            'thumb.mimes' => 'Attēls ir jābūt JPEG vai PNG failam',
            'thumb.max' => 'Attēla izmērs ir jābūt mazākam par 2MB'
        ]);
    }

    private function error_response(array $messages, int $status = 500)
    {
        return response()->json([
            'message' => $messages,
            'status' => $status
        ], $status);
    }

    private function success_response(string $message, array $data = [], int $status = 200)
    {
        return response()->json(array_merge([
            'message' => [$message],
            'status' => $status
        ], $data), $status);
    }

    public function get_playlist_contents($encryptedId)
    {
        try {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs kursa ID',
                'status' => 404
            ], 404);
        }

            $contents = Contents::where('playlist_id', $id)
            ->orderBy('date', 'asc')
                ->get()
                ->map(function ($content) {
                    return [
                        'id' => $content->id,
                        'encrypted_id' => $this->encryptId($content->id),
                        'title' => $content->title,
                        'description' => $content->description,
                        'video' => $content->video,
                        'thumb' => $content->thumb,
                        'date' => $content->date,
                        'status' => $content->status,
                        'video_source_type' => $content->video_source_type,
                        'teacher_id' => $content->teacher_id,
                        'playlist_id' => $content->playlist_id
                    ];
                });

            return response()->json($contents);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function get_playlist_contents_amount($encryptedId)
    {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs kursa ID',
                'status' => 404
            ], 404);
        }

        return Contents::where('playlist_id', $id)->count();
    }
} 