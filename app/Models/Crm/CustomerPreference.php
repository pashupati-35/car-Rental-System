<?php

namespace App\Models\Crm;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPreference extends Model
{
    use HasFactory;

    protected $table = 'crm_customer_preferences';

    protected $fillable = [
        'customer_id',
        'preferred_car_type',
        'preferred_transmission',
        'preferred_fuel_type',
        'needs_child_seat',
        'needs_chauffeur',
        'vip_status',
        'loyalty_tier',
        'special_requests',
    ];

    protected $casts = [
        'needs_child_seat' => 'boolean',
        'needs_chauffeur' => 'boolean',
        'vip_status' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
