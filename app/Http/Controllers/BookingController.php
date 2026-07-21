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
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'rental_days' => 'required|integer|min:1',
            'ktp_file' => 'required|image|max:2048',
            'transfer_file' => 'required|image|max:2048',
        ]);

        $items = [];
        if ($request->filled('items')) {
            $rawItems = is_string($request->items) ? json_decode($request->items, true) : $request->items;
            if (is_array($rawItems) && count($rawItems) > 0) {
                $items = $rawItems;
            }
        }
        
        if (empty($items)) {
            $request->validate([
                'gear_id' => 'required|exists:gears,id',
                'quantity' => 'required|integer|min:1',
            ]);
            $items = [
                ['gear_id' => $request->gear_id, 'quantity' => $request->quantity]
            ];
        }

        $ktp_path = $request->file('ktp_file')->store('bookings/ktp', 'public');
        $transfer_path = $request->file('transfer_file')->store('bookings/transfer', 'public');

        $totalRemaining = 0;
        $totalQty = 0;
        $bookedItemsSummary = [];

        foreach ($items as $item) {
            $gear = Gear::findOrFail($item['gear_id'] ?? $item['id']);
            $qty = intval($item['quantity'] ?? 1);
            if ($qty < 1) $qty = 1;

            $total_price = $gear->price_per_day * $request->rental_days * $qty;
            $dp_amount = ceil(($total_price * 0.3) / 1000) * 1000;
            $remaining = $total_price - $dp_amount;

            Booking::create([
                'user_id' => auth()->id(),
                'gear_id' => $gear->id,
                'quantity' => $qty,
                'customer_name' => $request->name,
                'customer_phone' => $request->phone,
                'customer_address' => $request->address,
                'ktp_path' => $ktp_path,
                'transfer_proof_path' => $transfer_path,
                'rental_days' => $request->rental_days,
                'start_date' => null,
                'end_date' => null,
                'total_price' => $total_price,
                'dp_amount' => $dp_amount,
                'remaining_balance' => $remaining,
                'status' => 'pending',
            ]);

            $totalRemaining += $remaining;
            $totalQty += $qty;
            $bookedItemsSummary[] = [
                'name' => $gear->name,
                'quantity' => $qty,
                'price_per_day' => $gear->price_per_day,
                'total_price' => $total_price,
            ];
        }

        $productNames = implode(', ', array_column($bookedItemsSummary, 'name'));

        return to_route('booking.success', [
            'product_name' => $productNames,
            'rental_days' => $request->rental_days,
            'quantity' => $totalQty,
            'remaining_balance' => $totalRemaining,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'booked_items' => $bookedItemsSummary,
        ]);
    }
}
