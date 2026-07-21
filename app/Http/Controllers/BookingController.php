<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Gear;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

        $phoneFormatted = preg_replace('/[^0-9]/', '', $request->phone);
        if (str_starts_with($phoneFormatted, '62')) {
            $phoneFormatted = '0' . substr($phoneFormatted, 2);
        } elseif (!str_starts_with($phoneFormatted, '0')) {
            $phoneFormatted = '0' . $phoneFormatted;
        }

        [$totalRemaining, $totalQty, $bookedItemsSummary] = DB::transaction(function () use ($items, $request, $phoneFormatted, $ktp_path, $transfer_path) {
            $totalRemaining = 0;
            $totalQty = 0;
            $bookedItemsSummary = [];

            foreach ($items as $item) {
                $gearId = $item['gear_id'] ?? $item['id'];
                $gear = Gear::where('id', $gearId)->lockForUpdate()->firstOrFail();
                $qty = intval($item['quantity'] ?? 1);
                if ($qty < 1) $qty = 1;

                if ($gear->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok untuk perlengkapan '{$gear->name}' tidak mencukupi (tersisa {$gear->stock} unit)."
                    ]);
                }

                $total_price = $gear->price_per_day * $request->rental_days * $qty;
                $dp_amount = ceil(($total_price * 0.3) / 1000) * 1000;
                $remaining = $total_price - $dp_amount;

                Booking::create([
                    'user_id' => Auth::id(),
                    'gear_id' => $gear->id,
                    'quantity' => $qty,
                    'customer_name' => $request->name,
                    'customer_phone' => $phoneFormatted,
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

            return [$totalRemaining, $totalQty, $bookedItemsSummary];
        });

        $productNames = implode(', ', array_column($bookedItemsSummary, 'name'));

        // Kirim notifikasi WhatsApp otomatis saat booking berhasil dibuat
        WhatsAppService::sendBookingConfirmation(
            $phoneFormatted,
            $request->name,
            $productNames,
            $totalQty,
            $request->rental_days,
            $totalRemaining
        );

        return to_route('booking.success', [
            'product_name' => $productNames,
            'rental_days' => $request->rental_days,
            'quantity' => $totalQty,
            'remaining_balance' => $totalRemaining,
            'name' => $request->name,
            'phone' => $phoneFormatted,
            'address' => $request->address,
            'booked_items' => $bookedItemsSummary,
        ]);
    }
}
