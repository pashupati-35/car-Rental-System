<?php

namespace App\Models\Crm;

use App\Models\Admin;
use App\Models\Car;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_leads';

    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'source',
        'status',
        'priority',
        'estimated_value',
        'interested_car_id',
        'pickup_date',
        'return_date',
        'notes',
        'converted_customer_id',
        'converted_at',
        'assigned_admin_id',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'pickup_date' => 'date',
        'return_date' => 'date',
        'converted_at' => 'datetime',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function interestedCar(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'interested_car_id');
    }

    public function convertedCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'converted_customer_id');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'lead_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'lead_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmTask::class, 'related_id')->where('related_type', 'lead');
    }
}
