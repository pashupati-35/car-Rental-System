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
        Schema::table('site_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('site_settings', 'support_email')) {
                $table->string('support_email')->nullable()->after('email');
            }
            if (!Schema::hasColumn('site_settings', 'viber')) {
                $table->string('viber')->nullable()->after('whatsapp');
            }
            if (!Schema::hasColumn('site_settings', 'pininterest')) {
                $table->string('pininterest')->nullable()->after('viber');
            }
            if (!Schema::hasColumn('site_settings', 'cookie_content_text')) {
                $table->text('cookie_content_text')->nullable()->after('copy_right_text');
            }
            if (!Schema::hasColumn('site_settings', 'terms_condition')) {
                $table->longText('terms_condition')->nullable()->after('cookie_content_text');
            }
            if (!Schema::hasColumn('site_settings', 'zoom_link')) {
                $table->string('zoom_link')->nullable()->after('map_url');
            }
            if (!Schema::hasColumn('site_settings', 'email_logo_image')) {
                $table->string('email_logo_image')->nullable()->after('footer_logo');
            }
            if (!Schema::hasColumn('site_settings', 'enable_cookies')) {
                $table->boolean('enable_cookies')->default(1)->after('terms_condition');
            }
            if (!Schema::hasColumn('site_settings', 'facebook_chat_widgets')) {
                $table->text('facebook_chat_widgets')->nullable()->after('pininterest');
            }
            if (!Schema::hasColumn('site_settings', 'google_analytics')) {
                $table->text('google_analytics')->nullable()->after('facebook_chat_widgets');
            }
            if (!Schema::hasColumn('site_settings', 'pixels')) {
                $table->text('pixels')->nullable()->after('google_analytics');
            }
            if (!Schema::hasColumn('site_settings', 'login_bg_image')) {
                $table->string('login_bg_image')->nullable()->after('email_logo_image');
            }
            if (!Schema::hasColumn('site_settings', 'login_bg_color')) {
                $table->string('login_bg_color')->nullable()->after('login_bg_image');
            }
            if (!Schema::hasColumn('site_settings', 'primary_color')) {
                $table->string('primary_color')->nullable()->after('login_bg_color');
            }
            if (!Schema::hasColumn('site_settings', 'secondary_color')) {
                $table->string('secondary_color')->nullable()->after('primary_color');
            }
            if (!Schema::hasColumn('site_settings', 'colors_variables')) {
                $table->text('colors_variables')->nullable()->after('secondary_color');
            }
            if (!Schema::hasColumn('site_settings', 'date_format')) {
                $table->string('date_format')->nullable()->default('Y-m-d')->after('colors_variables');
            }
            if (!Schema::hasColumn('site_settings', 'tax_percentage')) {
                $table->decimal('tax_percentage', 8, 2)->nullable()->default(13.00)->after('date_format');
            }
            if (!Schema::hasColumn('site_settings', 'pan_no')) {
                $table->string('pan_no')->nullable()->after('tax_percentage');
            }
            if (!Schema::hasColumn('site_settings', 'vat_no')) {
                $table->string('vat_no')->nullable()->after('pan_no');
            }
            if (!Schema::hasColumn('site_settings', 'address_type')) {
                $table->string('address_type')->nullable()->after('address');
            }
            if (!Schema::hasColumn('site_settings', 'is_admission_form_active')) {
                $table->boolean('is_admission_form_active')->default(0)->after('enable_cookies');
            }
            if (!Schema::hasColumn('site_settings', 'display_storage')) {
                $table->boolean('display_storage')->default(0)->after('is_admission_form_active');
            }
            if (!Schema::hasColumn('site_settings', 'display_smtp')) {
                $table->boolean('display_smtp')->default(1)->after('display_storage');
            }
            if (!Schema::hasColumn('site_settings', 'mail_driver')) {
                $table->string('mail_driver')->nullable()->after('display_smtp');
            }
            if (!Schema::hasColumn('site_settings', 'mail_host')) {
                $table->string('mail_host')->nullable()->after('mail_driver');
            }
            if (!Schema::hasColumn('site_settings', 'mail_port')) {
                $table->string('mail_port')->nullable()->after('mail_host');
            }
            if (!Schema::hasColumn('site_settings', 'mail_user_name')) {
                $table->string('mail_user_name')->nullable()->after('mail_port');
            }
            if (!Schema::hasColumn('site_settings', 'mail_password')) {
                $table->text('mail_password')->nullable()->after('mail_user_name');
            }
            if (!Schema::hasColumn('site_settings', 'mail_encryption')) {
                $table->string('mail_encryption')->nullable()->after('mail_password');
            }
            if (!Schema::hasColumn('site_settings', 'mail_sender_name')) {
                $table->string('mail_sender_name')->nullable()->after('mail_encryption');
            }
            if (!Schema::hasColumn('site_settings', 'mail_sender_address')) {
                $table->string('mail_sender_address')->nullable()->after('mail_sender_name');
            }
            if (!Schema::hasColumn('site_settings', 'storage_type')) {
                $table->string('storage_type')->nullable()->default('local')->after('mail_sender_address');
            }
            if (!Schema::hasColumn('site_settings', 'storage_endpoint')) {
                $table->string('storage_endpoint')->nullable()->after('storage_type');
            }
            if (!Schema::hasColumn('site_settings', 'storage_access_key')) {
                $table->text('storage_access_key')->nullable()->after('storage_endpoint');
            }
            if (!Schema::hasColumn('site_settings', 'storage_secret_key')) {
                $table->text('storage_secret_key')->nullable()->after('storage_access_key');
            }
            if (!Schema::hasColumn('site_settings', 'storage_region')) {
                $table->string('storage_region')->nullable()->after('storage_secret_key');
            }
            if (!Schema::hasColumn('site_settings', 'storage_bucket_name')) {
                $table->string('storage_bucket_name')->nullable()->after('storage_region');
            }
            if (!Schema::hasColumn('site_settings', 'storage_url')) {
                $table->string('storage_url')->nullable()->after('storage_bucket_name');
            }
            if (!Schema::hasColumn('site_settings', 'recaptcha_site_key')) {
                $table->string('recaptcha_site_key')->nullable()->after('storage_url');
            }
            if (!Schema::hasColumn('site_settings', 'recaptcha_secret_key')) {
                $table->text('recaptcha_secret_key')->nullable()->after('recaptcha_site_key');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $columns = [
                'support_email', 'viber', 'pininterest', 'cookie_content_text',
                'terms_condition', 'zoom_link', 'email_logo_image', 'enable_cookies',
                'facebook_chat_widgets', 'google_analytics', 'pixels', 'login_bg_image',
                'login_bg_color', 'primary_color', 'secondary_color', 'colors_variables',
                'date_format', 'tax_percentage', 'pan_no', 'vat_no', 'address_type',
                'is_admission_form_active', 'display_storage', 'display_smtp',
                'mail_driver', 'mail_host', 'mail_port', 'mail_user_name',
                'mail_password', 'mail_encryption', 'mail_sender_name',
                'mail_sender_address', 'storage_type', 'storage_endpoint',
                'storage_access_key', 'storage_secret_key', 'storage_region',
                'storage_bucket_name', 'storage_url', 'recaptcha_site_key',
                'recaptcha_secret_key',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
