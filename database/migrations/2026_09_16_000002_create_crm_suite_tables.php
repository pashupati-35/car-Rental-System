<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. CRM Leads
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->default('website'); // website, phone, walk_in, referral, corporate, ai_chat, other
            $table->string('status')->default('new'); // new, contacted, qualified, proposal_sent, converted, lost
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->decimal('estimated_value', 12, 2)->default(0.00);
            $table->unsignedBigInteger('interested_car_id')->nullable();
            $table->date('pickup_date')->nullable();
            $table->date('return_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('converted_customer_id')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->unsignedBigInteger('assigned_admin_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('interested_car_id')->references('id')->on('cars')->nullOnDelete();
            $table->foreign('converted_customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('assigned_admin_id')->references('id')->on('admins')->nullOnDelete();
            $table->index(['status', 'priority', 'source']);
        });

        // 2. CRM Corporate Accounts (B2B)
        Schema::create('crm_corporate_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('business_reg_number')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0.00);
            $table->decimal('contract_discount_percent', 5, 2)->default(0.00);
            $table->string('payment_terms')->default('Net 30');
            $table->string('status')->default('active'); // active, pending, suspended
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_admin_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('assigned_admin_id')->references('id')->on('admins')->nullOnDelete();
        });

        // 3. CRM Deals & Sales Pipeline
        Schema::create('crm_deals', function (Blueprint $table) {
            $table->id();
            $table->string('deal_number')->unique();
            $table->string('title');
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('corporate_account_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->string('stage')->default('lead_in'); // lead_in, needs_analysis, vehicle_proposed, negotiation, won, lost
            $table->decimal('value', 12, 2)->default(0.00);
            $table->unsignedTinyInteger('win_probability')->default(20);
            $table->date('expected_close_date')->nullable();
            $table->string('loss_reason')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_admin_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('id')->on('crm_leads')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('corporate_account_id')->references('id')->on('crm_corporate_accounts')->nullOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
            $table->foreign('assigned_admin_id')->references('id')->on('admins')->nullOnDelete();
            $table->index(['stage', 'assigned_admin_id']);
        });

        // 4. CRM Customer Interactions
        Schema::create('crm_customer_interactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('type')->default('note'); // call, email, meeting, note, whatsapp, sms
            $table->string('subject');
            $table->text('details');
            $table->dateTime('interaction_date');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->foreign('admin_id')->references('id')->on('admins')->nullOnDelete();
            $table->index(['customer_id', 'type', 'interaction_date'], 'crm_cust_interact_idx');
        });

        // 5. CRM Customer Preferences
        Schema::create('crm_customer_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->unique();
            $table->string('preferred_car_type')->nullable();
            $table->string('preferred_transmission')->nullable();
            $table->string('preferred_fuel_type')->nullable();
            $table->boolean('needs_child_seat')->default(false);
            $table->boolean('needs_chauffeur')->default(false);
            $table->boolean('vip_status')->default(false);
            $table->string('loyalty_tier')->default('Standard'); // Standard, Silver, Gold, Platinum
            $table->text('special_requests')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });

        // 6. CRM Tasks & Reminders
        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('related_type')->nullable(); // customer, lead, deal, ticket, car
            $table->unsignedBigInteger('related_id')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->unsignedBigInteger('assigned_admin_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('assigned_admin_id')->references('id')->on('admins')->nullOnDelete();
            $table->index(['status', 'priority', 'due_date']);
        });

        // 7. CRM Quotations (CPQ)
        Schema::create('crm_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('days_count')->default(1);
            $table->decimal('daily_rate', 12, 2)->default(0.00);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->string('status')->default('draft'); // draft, sent, accepted, rejected, expired
            $table->date('valid_until')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('lead_id')->references('id')->on('crm_leads')->nullOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('admins')->nullOnDelete();
            $table->index(['status', 'quotation_number']);
        });

        // 8. CRM Quotation Items
        Schema::create('crm_quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->string('description');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('total_price', 12, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('quotation_id')->references('id')->on('crm_quotations')->cascadeOnDelete();
        });

        // 9. CRM Support Tickets & Incidents
        Schema::create('crm_support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->string('subject');
            $table->string('category')->default('general'); // roadside_assistance, billing, extension, vehicle_complaint, general
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('open'); // open, in_progress, waiting_customer, resolved, closed
            $table->unsignedBigInteger('assigned_admin_id')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->nullOnDelete();
            $table->foreign('booking_id')->references('id')->on('booking_car')->nullOnDelete();
            $table->foreign('assigned_admin_id')->references('id')->on('admins')->nullOnDelete();
            $table->index(['status', 'priority', 'category']);
        });

        // 10. CRM Ticket Messages
        Schema::create('crm_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->string('sender_type')->default('admin'); // admin, customer
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('sender_name')->nullable();
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('crm_support_tickets')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_ticket_messages');
        Schema::dropIfExists('crm_support_tickets');
        Schema::dropIfExists('crm_quotation_items');
        Schema::dropIfExists('crm_quotations');
        Schema::dropIfExists('crm_tasks');
        Schema::dropIfExists('crm_customer_preferences');
        Schema::dropIfExists('crm_customer_interactions');
        Schema::dropIfExists('crm_deals');
        Schema::dropIfExists('crm_corporate_accounts');
        Schema::dropIfExists('crm_leads');
    }
};
