<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Gear;

use Inertia\Inertia;
use Carbon\Carbon;

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
        $gear = $booking->gear;
        $quantity = $booking->quantity ?: 1;

        if ($gear->stock >= $quantity) {
            $gear->decrement('stock', $quantity);
            $booking->update([
                'status' => 'handed_over',
                'start_date' => now()
            ]);
            return back()->with('success', 'Items handed over. Stock updated.');
        }
        return back()->with('error', 'Insufficient stock for this order.');
    }

    public function processReturn(Request $request, Booking $booking)
    {
        $returnDate = now();
        $dueDate = Carbon::parse($booking->start_date)->addDays($booking->rental_days);

        $fine = 0;
        if ($returnDate->gt($dueDate)) {
            $daysLate = $returnDate->diffInDays($dueDate);
            $fine = $daysLate * ($booking->gear->price_per_day * 1.5); // 1.5x penalty
        }

        // Increase stock back
        $booking->gear->increment('stock', $booking->quantity ?: 1);

        $booking->update([
            'status' => 'returned',
            'end_date' => $returnDate,
            'fine_amount' => $fine,
        ]);

        return back()->with('success', $fine > 0 
            ? 'Return processed. Fine calculated: Rp ' . number_format($fine) 
            : 'Return processed successfully.');
    }
}
