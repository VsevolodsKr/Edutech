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
use App\Models\Users;
use App\Models\Contacts;

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
                return DB::table('likes')
                    ->join('contents', 'likes.content_id', '=', 'contents.id')
                    ->where('contents.teacher_id', $teacherId)
                    ->whereDate('likes.created_at', $date)
                    ->count();
            })->values();

            $comments = $dates->map(function ($date) use ($teacherId) {
                return DB::table('comments')
                    ->join('contents', 'comments.content_id', '=', 'contents.id')
                    ->where('contents.teacher_id', $teacherId)
                    ->whereDate('comments.date', $date)
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

            $contents = DB::table('contents')
                ->select('contents.*')
                ->selectRaw('(SELECT COUNT(*) FROM likes WHERE contents.id = likes.content_id) as likes_count')
                ->selectRaw('(SELECT COUNT(*) FROM comments WHERE contents.id = comments.content_id) as comments_count')
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

    public function getSystemActivity()
    {
        $days = 7;
        $dates = collect();
        $now = Carbon::now();

        // Generate last 7 days dates
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates->push($now->copy()->subDays($i)->format('Y-m-d'));
        }

        // Get user registrations per day
        $users = Users::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereIn(DB::raw('DATE(created_at)'), $dates)
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Get messages per day
        $messages = Contacts::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereIn(DB::raw('DATE(created_at)'), $dates)
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Format data for chart
        $data = [
            'labels' => $dates->map(function ($date) {
                return Carbon::parse($date)->format('d.m');
            })->values(),
            'users' => $dates->map(function ($date) use ($users) {
                return $users->get($date)?->count ?? 0;
            })->values(),
            'messages' => $dates->map(function ($date) use ($messages) {
                return $messages->get($date)?->count ?? 0;
            })->values()
        ];

        return response()->json($data);
    }

    public function getDashboardStats()
    {
        $stats = [
            'teachers' => DB::table('teachers')->count(),
            'users' => DB::table('users')->count(),
            'messages' => DB::table('contact')->count(),
            'activity' => $this->getSystemActivity()->original,
            'messageStatus' => app(ContactController::class)->getMessageStatusDistribution()->original,
            'topTeachers' => app(TeacherController::class)->getTopTeachers()->original,
            'unreadMessages' => app(ContactController::class)->getUnreadMessages()->original
        ];

        return response()->json($stats);
    }
} 