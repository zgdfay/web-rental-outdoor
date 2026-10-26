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
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('gear', function ($gq) use ($search) {
                      $gq->where('name', 'like', "%{$search}%");
                  });
            });
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
                'search' => $request->search ?: '',
            ]
        ]);
    }

    public function export(Request $request)
    {
        $query = Booking::with(['gear', 'user'])->latest();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('gear', function ($gq) use ($search) {
                      $gq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->get();

        $fileName = 'Laporan_Pendapatan_OutdoorSpecies_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        return response()->stream(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($handle, [
                'ID Booking',
                'Tanggal',
                'Customer',
                'No HP',
                'Alamat',
                'Item Sewa',
                'Jumlah Unit',
                'Durasi (Hari)',
                'Total Harga (Rp)',
                'DP (Rp)',
                'Pelunasan (Rp)',
                'Denda (Rp)',
                'Status'
            ]);

            foreach ($bookings as $b) {
                fputcsv($handle, [
                    'BO-' . $b->id,
                    $b->created_at ? $b->created_at->format('d/m/Y') : '-',
                    $b->customer_name ?: '-',
                    $b->customer_phone ? "'" . $b->customer_phone : '-',
                    str_replace(["\r", "\n"], ' ', $b->customer_address ?: '-'),
                    $b->gear ? $b->gear->name : 'Item Sewa',
                    $b->quantity ?: 1,
                    $b->rental_days ?: 1,
                    $b->total_price ?: 0,
                    $b->dp_amount ?: 0,
                    $b->remaining_balance ?: 0,
                    $b->fine_amount ?: 0,
                    $b->status ?: '-'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
