<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BoughtTicket extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'ticket_id',
        'order_id',
        'buyer_id',
        'qr_code',
        'valid_until',
        'used_at',
        'status',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }
}
