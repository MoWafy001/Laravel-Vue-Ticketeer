<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class EventsMember extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'event_id',
        'member_id',
        'can_edit_details',
        'can_manage_tickets',
        'can_view_analytics',
        'can_view_buyer_contacts',
        'can_cancel_tickets',
        'can_scan_tickets',
    ];

    protected $casts = [
        'can_edit_details' => 'boolean',
        'can_manage_tickets' => 'boolean',
        'can_view_analytics' => 'boolean',
        'can_view_buyer_contacts' => 'boolean',
        'can_cancel_tickets' => 'boolean',
        'can_scan_tickets' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function member()
    {
        return $this->belongsTo(CompanyMember::class, 'member_id');
    }
}
