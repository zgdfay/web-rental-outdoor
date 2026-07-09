<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Gear;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'gear_id' => 'required|exists:gears,id',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'rental_days' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'ktp_file' => 'required|image|max:2048',
            'transfer_file' => 'required|image|max:2048',
        ]);

        $gear = Gear::findOrFail($request->gear_id);
        
        $total_price = $gear->price_per_day * $request->rental_days * $request->quantity;
        $dp_amount = ceil(($total_price * 0.3) / 1000) * 1000;
        $remaining = $total_price - $dp_amount;

        $ktp_path = $request->file('ktp_file')->store('bookings/ktp', 'public');
        $transfer_path = $request->file('transfer_file')->store('bookings/transfer', 'public');

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'gear_id' => $gear->id,
            'quantity' => $request->quantity,
            'customer_name' => $request->name,
            'customer_phone' => $request->phone,
            'customer_address' => $request->address,
            'ktp_path' => $ktp_path,
            'transfer_proof_path' => $transfer_path,
            'rental_days' => $request->rental_days,
            'start_date' => null, // Set when handed over
            'end_date' => null, // Set when returned
            'total_price' => $total_price,
            'dp_amount' => $dp_amount,
            'remaining_balance' => $remaining,
            'status' => 'pending',
        ]);

        return to_route('booking.success', [
            'product_name' => $gear->name,
            'rental_days' => $request->rental_days,
            'quantity' => $request->quantity,
            'remaining_balance' => $remaining,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
    }
}
