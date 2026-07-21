<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Gear;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $monthlyChart = [];
            for ($m = 6; $m >= 0; $m--) {
                $date = now()->subMonths($m);
                $monthName = $date->format('M');
                $revenue = Booking::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->whereIn('status', ['validated', 'handed_over', 'returned'])
                    ->sum('total_price');
                $monthlyChart[] = ['month' => $monthName, 'revenue' => $revenue, 'total' => $revenue];
            }

            return inertia('admin/dashboard', [
                'stats' => [
                    'total_revenue' => Booking::whereIn('status', ['validated', 'handed_over', 'returned'])->sum('total_price'),
                    'customers' => Booking::whereNotNull('customer_phone')->distinct('customer_phone')->count('customer_phone'),
                    'active_rentals' => Booking::where('status', 'handed_over')->count(),
                    'inventory' => Gear::count(),
                    'revenue_chart' => $monthlyChart,
                ],
                'recent_bookings' => Booking::with('gear')->latest()->limit(5)->get()
            ]);
        } elseif ($user->role === 'staff') {
            return inertia('staff/dashboard', [
                'stats' => [
                    'pending' => Booking::where('status', 'pending')->count(),
                    'handover' => Booking::where('status', 'confirmed')
                        ->whereDate('start_date', now())
                        ->count(),
                    'returns' => Booking::where('status', 'handover')
                        ->whereDate('end_date', '<=', now())
                        ->count(),
                ]
            ]);
        }
        return inertia('dashboard');
    }
}
