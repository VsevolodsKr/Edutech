<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Likes;
use App\Models\Comments;
use App\Models\Contents;
use App\Models\Teachers;
use App\Models\Users;
use App\Models\Contacts;
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
            $teacher_id = $this->decryptId($id);
            if (!$teacher_id) {
                return response()->json([
                    'error' => 'Nepareizs pasniedzēja ID',
                    'message' => 'Nepareizs pasniedzēja ID'
                ], 400);
            }

            $dates = collect(range(6, 0))->map(function ($days) {
                return Carbon::now()->subDays($days)->format('Y-m-d');
            });

            $labels = $dates->map(function ($date) {
                return Carbon::parse($date)->format('d.m.Y');
            });

            $likes = $dates->map(function ($date) use ($teacher_id) {
                return Likes::whereHas('content', function ($query) use ($teacher_id) {
                    $query->where('teacher_id', $teacher_id);
                })
                ->whereDate('created_at', $date)
                ->count();
            })->values();

            $comments = $dates->map(function ($date) use ($teacher_id) {
                return Comments::whereHas('content', function ($query) use ($teacher_id) {
                    $query->where('teacher_id', $teacher_id);
                })
                ->whereDate('date', $date)
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
            $teacher_id = $this->decryptId($id);
            if (!$teacher_id) {
                return response()->json([
                    'error' => 'Nepareizs pasniedzēja ID',
                    'message' => 'Nepareizs pasniedzēja ID'
                ], 400);
            }

            $contents = Contents::where('teacher_id', $teacher_id)
                ->withCount(['likes', 'comments'])
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

    public function get_system_activity()
    {
        $days = 7;
        $dates = collect();
        $now = Carbon::now();

        for ($i = $days - 1; $i >= 0; $i--) {
            $dates->push($now->copy()->subDays($i)->format('Y-m-d'));
        }

        $users = Users::selectRaw('DATE(created_at) as date, count(*) as count')
            ->whereIn(DB::raw('DATE(created_at)'), $dates)
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $messages = Contacts::selectRaw('DATE(created_at) as date, count(*) as count')
            ->whereIn(DB::raw('DATE(created_at)'), $dates)
            ->groupBy('date')
            ->get()
            ->keyBy('date');

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

    public function get_dashboard_stats()
    {
        $stats = [
            'teachers' => Teachers::count(),
            'users' => Users::count(),
            'messages' => Contacts::count(),
            'activity' => $this->get_system_activity()->original,
            'message_status' => app(ContactController::class)->get_message_status_distribution()->original,
            'top_teachers' => app(TeacherController::class)->get_top_teachers()->original,
            'unread_messages' => app(ContactController::class)->get_unread_messages()->original
        ];

        return response()->json($stats);
    }
} 