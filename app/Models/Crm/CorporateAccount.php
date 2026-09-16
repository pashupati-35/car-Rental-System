<?php

namespace App\Models\Crm;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_corporate_accounts';

    protected $fillable = [
        'company_name',
        'business_reg_number',
        'tax_id',
        'contact_person',
        'email',
        'phone',
        'address',
        'credit_limit',
        'contract_discount_percent',
        'payment_terms',
        'status',
        'notes',
        'assigned_admin_id',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'contract_discount_percent' => 'decimal:2',
    ];

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'corporate_account_id');
    }
}
