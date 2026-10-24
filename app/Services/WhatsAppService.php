<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message via Fonnte / Gateway API.
     */
    public static function send($targetPhone, $message)
    {
        $token = env('WHATSAPP_TOKEN') ?: env('FONNTE_TOKEN');
        $apiUrl = env('WHATSAPP_API_URL', 'https://api.fonnte.com/send');

        if (!$token) {
            Log::info("[WhatsAppService] Token not set. Skipped sending WA to {$targetPhone}:\n{$message}");
            return false;
        }

        // Format phone number to international string without '+'
        $phone = preg_replace('/[^0-9]/', '', $targetPhone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post($apiUrl, [
                'target' => $phone,
                'message' => $message,
            ]);

            Log::info("[WhatsAppService] Response for {$phone}: " . $response->body());
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("[WhatsAppService] Error sending WA to {$phone}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Notification sent automatically when user submits booking (/booking form).
     */
    public static function sendBookingConfirmation($phone, $name, $productNames, $quantity, $rentalDays, $remainingBalance, $dpAmount = 0, $totalPrice = 0)
    {
        $sisa = number_format($remainingBalance, 0, ',', '.');
        $text = "🏕️ *OUTDOOR SPECIES - KONFIRMASI BOOKING* 🏕️\n\n"
              . "Halo kak *{$name}*,\n"
              . "Terima kasih telah melakukan pemesanan alat outdoor di toko kami! Berikut adalah ringkasan pesanan Anda:\n\n"
              . "📦 *Alat Dipesan*: {$productNames} ({$quantity} unit)\n"
              . "⏱️ *Durasi Sewa*: {$rentalDays} Hari\n";

        if ($totalPrice > 0) {
            $text .= "🏷️ *Total Biaya*: *Rp " . number_format($totalPrice, 0, ',', '.') . "*\n";
        }
        if ($dpAmount > 0) {
            $text .= "💵 *DP Ditransfer*: *Rp " . number_format($dpAmount, 0, ',', '.') . "*\n";
        }

        $text .= "💰 *Sisa Pelunasan*: *Rp {$sisa}* (" . ($remainingBalance <= 0 ? "LUNAS" : "Dibayar saat pengambilan alat") . ")\n\n"
              . "📋 *Catatan Pengambilan*:\n"
              . "- Mohon membawa *KTP Asli* sebagai jaminan identitas.\n"
              . "- Tunjukkan pesan ini atau bukti booking saat tiba di toko Outdoor Species.\n\n"
              . "📍 *Lokasi Toko*:\n"
              . "Jl. Veteran No. 08, Malang, Jawa Timur\n\n"
              . "Salam hangat,\n*Tim Outdoor Species*";

        return self::send($phone, $text);
    }

    /**
     * Notification sent automatically when staff validates / hands over the gear.
     */
    public static function sendHandoverNotification($phone, $name, $productNames, $dueDate)
    {
        $tglKembali = \Carbon\Carbon::parse($dueDate)->translatedFormat('d F Y');
        $text = "🏕️ *OUTDOOR SPECIES - ALAT TELAH DIAMBIL* 🏕️\n\n"
              . "Halo kak *{$name}*,\n"
              . "Penyewaan alat *{$productNames}* telah dikonfirmasi dan diserahkan.\n\n"
              . "📅 *Jatuh Tempo Pengembalian*: *{$tglKembali}*\n\n"
              . "Mohon merawat perlengakapan dengan baik dan mengembalikannya tepat waktu sebelum batas hari jatuh tempo untuk menghindari denda sewa.\n\n"
              . "Selamat berpetualang!\n*Tim Outdoor Species*";

        return self::send($phone, $text);
    }

    /**
     * Notification sent when gear is returned.
     */
    public static function sendReturnConfirmation($phone, $name, $productNames, $fineAmount = 0)
    {
        $dendaInfo = $fineAmount > 0 
            ? "⚠️ *Denda Keterlambatan*: Rp " . number_format($fineAmount, 0, ',', '.') . "\n\n" 
            : "";

        $text = "🏕️ *OUTDOOR SPECIES - ALAT TELAH DIKEMBALIKAN* 🏕️\n\n"
              . "Halo kak *{$name}*,\n"
              . "Terima kasih telah mengembalikan alat *{$productNames}* ke Outdoor Species.\n\n"
              . $dendaInfo
              . "Semoga petualangannya menyenangkan! Sampai jumpa di perjalanan berikutnya.\n\n"
              . "*Tim Outdoor Species*";

        return self::send($phone, $text);
    }

    /**
     * Notification sent for H+1 late returns.
     */
    public static function sendLateReminder($phone, $name, $productNames, $dueDate, $finePerDay)
    {
        $tglJatuhTempo = \Carbon\Carbon::parse($dueDate)->translatedFormat('d F Y');
        $denda = number_format($finePerDay, 0, ',', '.');

        $text = "🚨 *OUTDOOR SPECIES - PERINGATAN KETERLAMBATAN (H+1)* 🚨\n\n"
              . "Halo kak *{$name}*,\n"
              . "Masa penyewaan untuk alat *{$productNames}* telah *MELEWATI BATAS WAKTU PENGEMBALIAN* sejak {$tglJatuhTempo}.\n\n"
              . "Saat ini denda keterlambatan berjalan sebesar *Rp {$denda}/hari*.\n"
              . "Mohon untuk segera mengembalikan perlengkapan ke toko Outdoor Species hari ini atau hubungi admin kami apabila ada kendala.\n\n"
              . "Terima kasih,\n*Tim Outdoor Species*";

        return self::send($phone, $text);
    }
}
