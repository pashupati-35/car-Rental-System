<?php

namespace App\Services\Auth;

use App\Mail\Auth\PasswordResetMail;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Owner;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
     * Send password reset link to user/owner/admin.
     */
    public static function sendResetLink(string $email, string $guard = 'customer'): array
    {
        $user = match ($guard) {
            'admin' => Admin::where('email', $email)->first(),
            'owner' => Owner::where('email', $email)->first(),
            default => Customer::where('email', $email)->first(),
        };

        if (!$user) {
            return ['status' => 'error', 'message' => 'We could not find an account with that email address.'];
        }

        $token = Str::random(64);

        // Store or replace in password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = match ($guard) {
            'admin' => url("/admin/reset-password/{$token}?email=" . urlencode($email)),
            'owner' => url("/owner/reset-password/{$token}?email=" . urlencode($email)),
            default => url("/customer/reset-password/{$token}?email=" . urlencode($email)),
        };

        $roleName = match ($guard) {
            'admin' => 'Admin',
            'owner' => 'Fleet Owner',
            default => 'Customer',
        };

        $name = $user->name ?? $user->full_name ?? 'User';

        try {
            Mail::to($email)->send(new PasswordResetMail($name, $resetUrl, $roleName));
        } catch (\Exception $e) {
            // Log or proceed
        }

        return [
            'status' => 'success',
            'message' => 'We have emailed your password reset link!',
            'reset_url' => $resetUrl, // Provided for instant testing or fallback
        ];
    }

    /**
     * Reset password given email, token, and new password.
     */
    public static function resetPassword(string $email, string $token, string $newPassword, string $guard = 'customer'): array
    {
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return ['status' => 'error', 'message' => 'Invalid or expired password reset token.'];
        }

        // Check if token expired (60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return ['status' => 'error', 'message' => 'This password reset link has expired. Please request a new one.'];
        }

        // Verify token hash
        if (!Hash::check($token, $record->token)) {
            return ['status' => 'error', 'message' => 'Invalid password reset token.'];
        }

        $user = match ($guard) {
            'admin' => Admin::where('email', $email)->first(),
            'owner' => Owner::where('email', $email)->first(),
            default => Customer::where('email', $email)->first(),
        };

        if (!$user) {
            return ['status' => 'error', 'message' => 'User account not found.'];
        }

        $user->password = Hash::make($newPassword);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Remove token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return ['status' => 'success', 'message' => 'Your password has been successfully updated!'];
    }
}
