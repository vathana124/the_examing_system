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
        Schema::table('users', function (Blueprint $table) {
            $table->string('token_2fa')->nullable();
            $table->datetime('token_2fa_expiry')->nullable();
            $table->string('email_otp')->nullable();
            $table->boolean('verify_otp_false')->default(false)->nullable();
            $table->string('otp_input_email')->nullable();
            $table->datetime('otp_resend_duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('token_2fa');
            $table->dropColumn('token_2fa_expiry');
            $table->dropColumn('email_otp');
            $table->dropColumn('verify_otp_false');
            $table->dropColumn(['otp_input_email', 'otp_resend_duration']);
        });
    }
};
