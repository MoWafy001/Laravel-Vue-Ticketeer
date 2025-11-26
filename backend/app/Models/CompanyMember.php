<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CompanyMember extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'company_id',
        'organizer_id',
        'can_view_analytics',
        'can_manage_members',
        'can_manage_settings',
        'can_create_events',
        'can_manage_all_events',
        'can_manage_wallet',
    ];

    protected $casts = [
        'can_view_analytics' => 'boolean',
        'can_manage_members' => 'boolean',
        'can_manage_settings' => 'boolean',
        'can_create_events' => 'boolean',
        'can_manage_all_events' => 'boolean',
        'can_manage_wallet' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }
}
