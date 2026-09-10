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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('sender');                    // sender_type + sender_id (AdminUser, Employee, User)
            $table->string('from')->nullable();                   // From email address
            $table->text('to');                                   // Recipient email address(es)
            $table->text('cc')->nullable();                       // CC address(es)
            $table->text('bcc')->nullable();                      // BCC address(es)
            $table->string('reply_to')->nullable();               // Reply-To address
            $table->string('subject')->nullable();                // Email subject
            $table->longText('body')->nullable();                 // Rendered HTML/Text body
            $table->string('status', 50)->default('sent');        // sent, failed, queued, sending
            $table->string('mailable_class')->nullable();         // Mailable class name if applicable
            $table->string('transport', 50)->nullable();          // Mailer transport (smtp, log, mailgun, etc.)
            $table->text('error_message')->nullable();            // Error message if sending failed
            $table->string('ip_address', 45)->nullable();         // Requester's IP address
            $table->text('user_agent')->nullable();               // Requester's user agent
            $table->json('attachments')->nullable();              // Filenames / sizes
            $table->json('headers')->nullable();                  // Extra metadata/headers
            $table->timestamp('sent_at')->nullable();             // Timestamp when sent
            $table->timestamps();
            $table->softDeletes()->nullable();

            $table->index('status');
            $table->index('created_at');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
