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
                    'start_date' => $startDate,
                    'remaining_balance' => 0, // Pelunasan dilakukan saat serah terima barang di kasir
                ]);

                $dueDate = $startDate->copy()->addDays($booking->rental_days);
                WhatsAppService::sendHandoverNotification(
                    $booking->customer_phone,
                    $booking->customer_name,
                    $gear->name . " ({$quantity}x)",
                    $dueDate
                );

                return back()->with('success', 'Barang berhasil diserahterimakan. Sisa sewa dilunasi (balance Rp 0) dan stok diperbarui.');
            }
            return back()->with('error', 'Insufficient stock for this order.');
        });
    }

    public function processReturn(Request $request, Booking $booking)
    {
        $request->validate([
            'damage_fee' => 'nullable|numeric|min:0',
            'damage_notes' => 'nullable|string|max:1000',
        ]);

        $damageFee = intval($request->input('damage_fee', 0));
        $damageNotes = $request->input('damage_notes');

        $result = DB::transaction(function () use ($booking, $damageFee, $damageNotes) {
            $returnDate = now();
            $dueDate = Carbon::parse($booking->start_date)->addDays($booking->rental_days);

            $lateFine = 0;
            if ($returnDate->gt($dueDate)) {
                $daysLate = $returnDate->diffInDays($dueDate);
                $pricePerDay = $booking->gear ? $booking->gear->price_per_day : 0;
                $lateFine = $daysLate * ($pricePerDay * 1.5); // 1.5x penalty
            }

            $totalFine = $lateFine + $damageFee;

            // Lock gear to safely increase stock back
            $gear = Gear::where('id', $booking->gear_id)->lockForUpdate()->firstOrFail();
            $gear->increment('stock', $booking->quantity ?: 1);

            $booking->update([
                'status' => 'returned',
                'end_date' => $returnDate,
                'fine_amount' => $totalFine,
                'damage_fee' => $damageFee,
                'damage_notes' => $damageNotes,
                'remaining_balance' => 0, // Barang sudah dikembalikan, saldo sewa pokok balance 0
            ]);

            return [
                'total_fine' => $totalFine,
                'late_fine' => $lateFine,
                'damage_fee' => $damageFee,
                'damage_notes' => $damageNotes
            ];
        });

        WhatsAppService::sendReturnConfirmation(
            $booking->customer_phone,
            $booking->customer_name,
            ($booking->gear->name ?? 'Alat Outdoor') . " (" . ($booking->quantity ?: 1) . "x)",
            $result['total_fine'],
            $result['damage_fee'],
            $result['damage_notes']
        );

        $msg = 'Pengembalian barang berhasil diproses.';
        if ($result['total_fine'] > 0) {
            $parts = [];
            if ($result['late_fine'] > 0) $parts[] = 'Denda telat: Rp ' . number_format($result['late_fine']);
            if ($result['damage_fee'] > 0) $parts[] = 'Biaya kerusakan: Rp ' . number_format($result['damage_fee']);
            $msg .= ' Total denda/ganti rugi: Rp ' . number_format($result['total_fine']) . ' (' . implode(', ', $parts) . ').';
        }

        return back()->with('success', $msg);
    }
}
