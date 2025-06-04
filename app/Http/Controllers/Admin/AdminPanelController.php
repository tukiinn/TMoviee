<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Movie;
use App\Models\Transaction;
use App\Models\User;
use App\Models\MembershipPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class AdminPanelController extends Controller
{
    public function getStats(): JsonResponse
    {
        try {
            $totalMovies = Movie::count();
            $newMovies = Movie::whereDate('created_at', today())->count();
            $totalActors = Actor::count();
            $newActors = Actor::whereDate('created_at', today())->count();
            $totalDirectors = Director::count();
            $newDirectors = Director::whereDate('created_at', today())->count();

            // Check if movie_views table exists
            if (Schema::hasTable('movie_views')) {
                $todayViews = DB::table('movie_views')
                    ->whereDate('created_at', today())
                    ->sum('views') ?? 0;
                $yesterdayViews = DB::table('movie_views')
                    ->whereDate('created_at', today()->subDay())
                    ->sum('views') ?? 0;
            } else {
                $todayViews = 0;
                $yesterdayViews = 0;
            }

            // Tính % tăng trưởng
            $viewsIncrease = $yesterdayViews > 0
                ? round((($todayViews - $yesterdayViews) / $yesterdayViews) * 100, 2)
                : ($todayViews > 0 ? 100 : 0);

            return response()->json([
                'totalMovies' => $totalMovies,
                'newMovies' => $newMovies,
                'totalActors' => $totalActors,
                'newActors' => $newActors,
                'totalDirectors' => $totalDirectors,
                'newDirectors' => $newDirectors,
                'todayViews' => $todayViews,
                'viewsIncrease' => $viewsIncrease
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getRecentActivities(): JsonResponse
    {
        try {
            // Check if activities table exists
            if (!Schema::hasTable('activities')) {
                return response()->json([]);
            }

            $activities = Activity::with('user')
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'user' => [
                            'name' => $activity->user->name,
                            'avatar' => $activity->user->avatar
                        ],
                        'type' => $activity->type,
                        'description' => $this->formatActivityDescription($activity),
                        'created_at' => $activity->created_at->toDateTimeString()
                    ];
                });

            return response()->json($activities);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function formatActivityDescription($activity): string
    {
        $subject = $activity->subject;
        if (!$subject) {
            return $activity->description;
        }

        $name = match (get_class($subject)) {
            Movie::class => $subject->title,
            Actor::class => $subject->name,
            Director::class => $subject->name,
            default => 'Unknown'
        };

        return match ($activity->type) {
            'created' => "đã thêm mới {$name}",
            'updated' => "đã cập nhật {$name}",
            'deleted' => "đã xóa {$name}",
            default => $activity->description
        };
    }

    public function getRecentTransactions(): JsonResponse
    {
        $transactions = Transaction::with(['user', 'package'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'user' => $t->user ? $t->user->name : 'N/A',
                    'avatar' => $t->user && $t->user->avatar ? $t->user->avatar : null,
                    'package' => $t->package ? $t->package->name : 'N/A',
                    'amount' => $t->amount,
                    'status' => $t->status,
                    'created_at' => $t->created_at->format('d/m/Y H:i')
                ];
            });

        return response()->json($transactions);
    }

    public function getRevenueChartData(Request $request): JsonResponse
    {
        $type = $request->input('type', 'month'); // 'day', 'month', 'year', 'range'
        $methods = ['momo', 'vnpay', 'bank', 'zalopay', 'other'];

        if ($type === 'day') {
            $format = '%Y-%m-%d';
            // Lấy tất cả các ngày có giao dịch
            $range = \App\Models\Transaction::selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d") as period')
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('period');
        } elseif ($type === 'month') {
            $format = '%Y-%m';
            $range = \App\Models\Transaction::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period')
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('period');
        } elseif ($type === 'year') {
            $format = '%Y';
            $range = \App\Models\Transaction::selectRaw('DATE_FORMAT(created_at, "%Y") as period')
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('period');
        } elseif ($type === 'range') {
            $format = '%Y-%m-%d';
            $from = $request->input('from');
            $to = $request->input('to');
            $period = \Carbon\CarbonPeriod::create($from, $to);
            $range = collect();
            foreach ($period as $date) {
                $range->push($date->format('Y-m-d'));
            }
        } else {
            $format = '%Y-%m';
            $range = \App\Models\Transaction::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period')
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('period');
        }

        // 2. Tổng doanh thu theo thời gian (line chart)
        $lineData = \App\Models\Transaction::selectRaw("DATE_FORMAT(created_at, '{$format}') as period, SUM(amount) as total")
            ->where(function($q) {
                $q->where('status', 'success')->orWhere('status', 'completed');
            });

        // Chỉ lọc theo range
        if ($type === 'range') {
            $from = $request->input('from');
            $to = $request->input('to');
            if ($from && $to) {
                $lineData->whereBetween('created_at', [$from, $to]);
            }
        }

        $lineData = $lineData->groupBy('period')->pluck('total', 'period');
        $lineLabels = $range->toArray();
        $lineValues = array_map(fn($period) => (float)($lineData[$period] ?? 0), $lineLabels);

        // 3. Doanh thu theo phương thức thanh toán (doughnut chart)
        $doughnutQuery = \App\Models\Transaction::selectRaw('payment_method, SUM(amount) as total')
            ->where(function($q) {
                $q->where('status', 'success')->orWhere('status', 'completed');
            });
        if ($type === 'range') {
            $from = $request->input('from');
            $to = $request->input('to');
            if ($from && $to) {
                $doughnutQuery->whereBetween('created_at', [$from, $to]);
            }
        }
        $doughnutData = $doughnutQuery->groupBy('payment_method')->pluck('total', 'payment_method');
        $doughnutLabels = array_map('strtoupper', $methods);
        $doughnutValues = array_map(fn($m) => (float)($doughnutData[$m] ?? 0), $methods);

        return response()->json([
            'line' => [
                'labels' => $lineLabels,
                'data' => $lineValues
            ],
            'doughnut' => [
                'labels' => $doughnutLabels,
                'data' => $doughnutValues
            ]
        ]);
    }

    public function getRevenuePieData(Request $request): JsonResponse
    {
        $type = $request->input('type', 'month'); // 'day', 'month', 'year', 'range'
        $methods = ['momo', 'vnpay', 'bank', 'zalopay', 'other'];
        $query = \App\Models\Transaction::selectRaw('payment_method, SUM(amount) as total')
            ->where(function($q) {
                $q->where('status', 'success')->orWhere('status', 'completed');
            });

        if ($type === 'day') {
            $date = $request->input('value', now()->format('Y-m-d'));
            $query->whereDate('created_at', $date);
        } elseif ($type === 'month') {
            $month = $request->input('value', now()->format('Y-m'));
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month]);
        } elseif ($type === 'year') {
            $year = $request->input('value', now()->format('Y'));
            $query->whereYear('created_at', $year);
        } elseif ($type === 'range') {
            $from = $request->input('from');
            $to = $request->input('to');
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            }
        }

        $data = $query->groupBy('payment_method')->pluck('total', 'payment_method');
        $result = [];
        foreach ($methods as $method) {
            $result[] = [
                'name' => strtoupper($method),
                'value' => $data[$method] ?? 0
            ];
        }

        return response()->json([
            'data' => $result
        ]);
    }
} 