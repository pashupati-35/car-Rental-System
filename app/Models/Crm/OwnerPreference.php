<?php

namespace App\Models\Crm;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerPreference extends Model
{
    use HasFactory;

    protected $table = 'crm_owner_preferences';

    protected $fillable = [
        'owner_id',
        'partner_tier',
        'payout_frequency',
        'commission_rate',
        'payout_method',
        'bank_name',
        'account_number',
        'routing_number',
        'vip_partner',
        'notes',
    ];

    protected $casts = [
        'vip_partner' => 'boolean',
        'commission_rate' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
