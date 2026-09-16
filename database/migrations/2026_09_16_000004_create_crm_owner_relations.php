<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Owner 360 CRM suite relations.
     */
    public function up(): void
    {
        // 1. Add owner_id to crm_customer_interactions and make customer_id nullable
        Schema::table('crm_customer_interactions', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->unsignedBigInteger('owner_id')->nullable()->after('customer_id');
            $table->foreign('owner_id')->references('id')->on('owners')->cascadeOnDelete();
            $table->index(['owner_id', 'type', 'interaction_date'], 'crm_owner_interact_idx');
        });

        // 2. Add owner_id to crm_tasks
        Schema::table('crm_tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('related_id');
            $table->foreign('owner_id')->references('id')->on('owners')->cascadeOnDelete();
        });

        // 3. Add owner_id to crm_support_tickets
        Schema::table('crm_support_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('customer_id');
            $table->foreign('owner_id')->references('id')->on('owners')->cascadeOnDelete();
        });

        // 4. Create crm_owner_preferences table for Fleet Owner 360 settings
        Schema::create('crm_owner_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->unique();
            $table->string('partner_tier')->default('Standard'); // Standard, Silver Partner, Gold Partner, Platinum Partner
            $table->string('payout_frequency')->default('Monthly'); // Weekly, Bi-weekly, Monthly
            $table->decimal('commission_rate', 5, 2)->default(15.00);
            $table->string('payout_method')->default('Bank Transfer');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('routing_number')->nullable();
            $table->boolean('vip_partner')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('owners')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_owner_preferences');

        Schema::table('crm_support_tickets', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });

        Schema::table('crm_tasks', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });

        Schema::table('crm_customer_interactions', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });
    }
};
