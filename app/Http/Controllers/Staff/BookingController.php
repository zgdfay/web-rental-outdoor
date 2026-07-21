<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Gear;

use Inertia\Inertia;
use Carbon\Carbon;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        return Inertia::render('staff/bookings', [
            'bookings' => Booking::with('gear', 'user')->get()
        ]);
    }

    public function validateBooking(Booking $booking)
    {
        $booking->update(['status' => 'validated']);
        return back()->with('success', 'Booking validated.');
    }

    public function handover(Booking $booking)
    {
        return DB::transaction(function () use ($booking) {
            $quantity = $booking->quantity ?: 1;
            // Kunci baris gear agar tidak terjadi race condition saat dua admin/sistem memproses bersamaan
            $gear = Gear::where('id', $booking->gear_id)->lockForUpdate()->firstOrFail();

            if ($gear->stock >= $quantity) {
                $gear->decrement('stock', $quantity);
                $startDate = now();
                $booking->update([
                    'status' => 'handed_over',
                    'start_date' => $startDate
                ]);

                $dueDate = $startDate->copy()->addDays($booking->rental_days);
                WhatsAppService::sendHandoverNotification(
                    $booking->customer_phone,
                    $booking->customer_name,
                    $gear->name . " ({$quantity}x)",
                    $dueDate
                );

                return back()->with('success', 'Items handed over. Stock updated.');
            }
            return back()->with('error', 'Insufficient stock for this order.');
        });
    }

    public function processReturn(Request $request, Booking $booking)
    {
        $fine = DB::transaction(function () use ($booking) {
            $returnDate = now();
            $dueDate = Carbon::parse($booking->start_date)->addDays($booking->rental_days);

            $fineCalc = 0;
            if ($returnDate->gt($dueDate)) {
                $daysLate = $returnDate->diffInDays($dueDate);
                $fineCalc = $daysLate * ($booking->gear->price_per_day * 1.5); // 1.5x penalty
            }

            // Lock gear to safely increase stock back
            $gear = Gear::where('id', $booking->gear_id)->lockForUpdate()->firstOrFail();
            $gear->increment('stock', $booking->quantity ?: 1);

            $booking->update([
                'status' => 'returned',
                'end_date' => $returnDate,
                'fine_amount' => $fineCalc,
            ]);

            return $fineCalc;
        });

        WhatsAppService::sendReturnConfirmation(
            $booking->customer_phone,
            $booking->customer_name,
            $booking->gear->name . " (" . ($booking->quantity ?: 1) . "x)",
            $fine
        );

        return back()->with('success', $fine > 0 
            ? 'Return processed. Fine calculated: Rp ' . number_format($fine) 
            : 'Return processed successfully.');
    }
}
