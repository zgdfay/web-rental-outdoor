<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Gear;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return inertia('admin/dashboard', [
                'stats' => [
                    'total_revenue' => Booking::sum('total_price'),
                    'customers' => User::where('role', 'user')->count(),
                    'active_rentals' => Booking::where('status', 'handover')->count(),
                    'inventory' => Gear::count(),
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
