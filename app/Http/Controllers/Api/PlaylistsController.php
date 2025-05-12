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
                        'content_count' => $playlist->contents()->count()
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
        return Playlists::with('teacher')
            ->orderBy('date', 'desc')
            ->where('status', 'active')
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
                    ] : null
                ];
            });
    }

    public function latest()
    {
        try {
            $playlists = Playlists::with('teacher')
                ->where('status', 'active')
                ->orderBy('date', 'desc')
                ->take(7)
                ->get()
                ->filter(function ($playlist) {
                    return $playlist->teacher && $playlist->teacher->status === 'aktīvs';
                })
                ->map(function ($playlist) {
                    // Format teacher image
                    $teacherImage = null;
                    if ($playlist->teacher) {
                        $teacherImage = $playlist->teacher->formatted_image ?? $playlist->teacher->image;
                    }

                    // Format playlist thumbnail
                    $thumb = $playlist->thumb;
                    if ($thumb && !str_starts_with($thumb, 'http')) {
                        $thumb = str_replace([
                            'storage/app/public/',
                            'storage/',
                            '/app/public/',
                            'app/public/',
                            'uploads/uploads/',
                            'uploads/'
                        ], '', $thumb);
                        $thumb = '/storage/' . $thumb;
                    }

                    return [
                        'id' => $playlist->id,
                        'encrypted_id' => $this->encryptId($playlist->id),
                        'title' => $playlist->title,
                        'description' => $playlist->description,
                        'thumb' => $thumb,
                        'date' => $playlist->date,
                        'teacher_id' => $playlist->teacher_id,
                        'teacher' => $playlist->teacher ? [
                            'id' => $playlist->teacher->id,
                            'name' => $playlist->teacher->name,
                            'image' => $teacherImage,
                            'status' => $playlist->teacher->status
                        ] : null,
                        'content_count' => $playlist->contents()->count()
                    ];
                })
                ->values(); // Re-index the array after filtering

            return response()->json($playlists);
        } catch (\Exception $e) {
            \Log::error('Error in latest playlists: ' . $e->getMessage());
            return response()->json([
                'message' => 'Neizdevās iegūt jaunākos kursus',
                'error' => $e->getMessage()
            ], 500);
        }
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
                    'content_count' => $playlist->contents()->count()
                ],
                'teacher' => $playlist->teacher ? [
                    'id' => $playlist->teacher->id,
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
            return $this->errorResponse($validator->messages()->all());
        }

        return Playlists::where('title', 'like', '%' . $request->name . '%')
            ->orderBy('date', 'desc')
            ->get();
    }

    public function add(Request $request)
    {
        try {
            $validator = $this->validatePlaylist($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
            }

            $playlist = new Playlists();
            $playlist->status = $request->status;
            $playlist->teacher_id = $request->teacher_id;
            $playlist->title = $request->title;
            $playlist->description = $request->description;
            $playlist->date = Carbon::now();

            if ($request->hasFile('thumb')) {
                $playlist->thumb = $this->handleFileUpload($request->file('thumb'));
            }

            $playlist->save();

            return $this->successResponse('Kurss ir veiksmīgi izveidots!');
        } catch (\Exception $e) {
            return $this->errorResponse(['Neizdevās izveidot kursu: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $encryptedId)
    {
        try {
            $id = $this->decryptId($encryptedId);
            if (!$id) {
                return $this->errorResponse(['Nepareizs kursa ID']);
            }

            $validator = $this->validatePlaylist($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->messages()->all());
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
                    $updateData['thumb'] = $this->handleFileUpload($request->file('thumb'));
                    
                    if ($playlist->thumb && Storage::disk('public')->exists($playlist->thumb)) {
                        Storage::disk('public')->delete($playlist->thumb);
                    }
                } catch (\Exception $e) {
                    return $this->errorResponse(['Neizdevās augšupielādēt kursa attēlu: ' . $e->getMessage()]);
                }
            }
            
            $playlist->update($updateData);

            return $this->successResponse('Kurss ir veiksmīgi rediģēts!');
        } catch (\Exception $e) {
            return $this->errorResponse(['Neizdevās rediģēt kursu: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $playlist = Playlists::findOrFail($id);
            
            Contents::where('playlist_id', $id)->delete();
            
            $playlist->delete();
            
            return $this->successResponse('Kurss ir veiksmīgi dzēsts!');
        } catch (\Exception $e) {
            return $this->errorResponse(['Neizdevās dzēst kursu: ' . $e->getMessage()]);
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
                        'content_count' => $playlist->contents()->count()
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
                ->where('status', 'active')
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
                        'content_count' => $playlist->contents()->count()
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

    private function handleFileUpload($file)
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
            'status' => 'required|in:active,deactive'
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

    private function errorResponse(array $messages, int $status = 500)
    {
        return response()->json([
            'message' => $messages,
            'status' => $status
        ], $status);
    }

    private function successResponse(string $message, array $data = [], int $status = 200)
    {
        return response()->json(array_merge([
            'message' => [$message],
            'status' => $status
        ], $data), $status);
    }

    public function get_playlist_contents($encryptedId)
    {
        $id = $this->decryptId($encryptedId);
        if (!$id) {
            return response()->json([
                'message' => 'Nepareizs kursa ID',
                'status' => 404
            ], 404);
        }

        return Contents::where('playlist_id', $id)
            ->orderBy('date', 'asc')
            ->get();
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