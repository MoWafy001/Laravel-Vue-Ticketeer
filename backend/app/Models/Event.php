<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Event extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'created_by',
        'name',
        'description',
        'start_time',
        'end_time',
        'sale_start_time',
        'sale_end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'sale_start_time' => 'datetime',
        'sale_end_time' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(Organizer::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(EventsMember::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function boughtTickets()
    {
        return $this->hasManyThrough(BoughtTicket::class, Ticket::class);
    }
}
