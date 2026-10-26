<?php

namespace App\Models\Crm;

use App\Models\Admin;
use App\Models\Car;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_deals';

    protected $fillable = [
        'deal_number',
        'title',
        'lead_id',
        'customer_id',
        'corporate_account_id',
        'car_id',
        'stage',
        'value',
        'win_probability',
        'expected_close_date',
        'loss_reason',
        'notes',
        'assigned_admin_id',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'win_probability' => 'integer',
        'expected_close_date' => 'date',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function corporateAccount(): BelongsTo
    {
        return $this->belongsTo(CorporateAccount::class, 'corporate_account_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }
}
