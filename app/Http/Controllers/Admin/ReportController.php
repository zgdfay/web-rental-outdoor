<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Inertia\Inertia;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['gear', 'user'])->latest();

        // Optional date range filtering
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        // Calculate metrics from relevant transactions (handed_over, returned, validated, or even pending)
        // Usually revenue counts confirmed/handed over/returned orders
        $revenueBookings = $bookings->filter(function ($b) {
            return in_array($b->status, ['validated', 'handed_over', 'returned']);
        });

        $stats = [
            'total_revenue' => $revenueBookings->sum('total_price'),
            'total_dp' => $revenueBookings->sum('dp_amount'),
            'total_remaining' => $revenueBookings->sum('remaining_balance'),
            'total_fines' => $bookings->where('status', 'returned')->sum('fine_amount'),
            'total_transactions' => $bookings->count(),
            'completed_rentals' => $bookings->where('status', 'returned')->count(),
        ];

        // Monthly trend for current year
        $monthlyChart = [];
        $currentYearBookings = Booking::whereYear('created_at', now()->year)
            ->whereIn('status', ['validated', 'handed_over', 'returned'])
            ->get();

        for ($m = 1; $m <= 12; $m++) {
            $monthlyChart[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'revenue' => $currentYearBookings->filter(function ($b) use ($m) {
                    return Carbon::parse($b->created_at)->month === $m;
                })->sum('total_price')
            ];
        }

        return Inertia::render('admin/reports', [
            'bookings' => $bookings,
            'stats' => $stats,
            'monthly_chart' => $monthlyChart,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status ?: 'all',
            ]
        ]);
    }
}
