<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Likes;
use App\Models\Comments;
use App\Models\Contents;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Traits\Encryptable;

class EngagementController extends Controller
{
    use Encryptable;

    public function get_teacher_engagement(string $id)
    {
        try {
            $teacherId = $this->decryptId($id);
            if (!$teacherId) {
                return response()->json([
                    'error' => 'Nepareizs pasniedzēja ID',
                    'message' => 'Nepareizs pasniedzēja ID'
                ], 400);
            }

            $dates = collect(range(6, 0))->map(function ($days) {
                return Carbon::now()->subDays($days)->format('Y-m-d');
            });

            $labels = $dates->map(function ($date) {
                return Carbon::parse($date)->format('M d');
            });

            $likes = $dates->map(function ($date) use ($teacherId) {
                return DB::table('Likes')
                    ->join('Contents', 'Likes.content_id', '=', 'Contents.id')
                    ->where('Contents.teacher_id', $teacherId)
                    ->whereDate('Likes.created_at', $date)
                    ->count();
            })->values();

            $comments = $dates->map(function ($date) use ($teacherId) {
                return DB::table('Comments')
                    ->join('Contents', 'Comments.content_id', '=', 'Contents.id')
                    ->where('Contents.teacher_id', $teacherId)
                    ->whereDate('Comments.date', $date)
                    ->count();
            })->values();

            return response()->json([
                'labels' => $labels->values(),
                'likes' => $likes,
                'comments' => $comments
            ]);
        } catch (\Exception $e) {
            Log::error('Error in get_teacher_engagement: ' . $e->getMessage());
            return response()->json([
                'error' => 'Neizdevās iegūt ieguldījumu datus',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function get_popular_contents(string $id)
    {
        try {
            $teacherId = $this->decryptId($id);
            if (!$teacherId) {
                return response()->json([
                    'error' => 'Nepareizs pasniedzēja ID',
                    'message' => 'Nepareizs pasniedzēja ID'
                ], 400);
            }

            $contents = DB::table('Contents')
                ->select('Contents.*')
                ->selectRaw('(SELECT COUNT(*) FROM Likes WHERE Contents.id = Likes.content_id) as likes_count')
                ->selectRaw('(SELECT COUNT(*) FROM Comments WHERE Contents.id = Comments.content_id) as comments_count')
                ->where('teacher_id', $teacherId)
                ->orderByDesc('likes_count')
                ->orderByDesc('comments_count')
                ->limit(5)
                ->get()
                ->map(function ($content) {
                    return [
                        'id' => $content->id,
                        'title' => $content->title,
                        'likes' => $content->likes_count,
                        'comments' => $content->comments_count
                    ];
                });

            return response()->json($contents);
        } catch (\Exception $e) {
            Log::error('Error in get_popular_contents: ' . $e->getMessage());
            return response()->json([
                'error' => 'Neizdevās iegūt populārākos saturus',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 