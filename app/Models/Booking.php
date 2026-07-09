<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'gear_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'ktp_path',
        'transfer_proof_path',
        'rental_days',
        'start_date',
        'end_date',
        'total_price',
        'dp_amount',
        'remaining_balance',
        'quantity',
        'fine_amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gear()
    {
        return $this->belongsTo(Gear::class);
    }
}
