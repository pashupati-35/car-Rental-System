<?php

use App\Http\Controllers\AI\AIController;
use App\Http\Controllers\Auth\MFAController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// MFA and Auth verification API endpoints
Route::prefix('auth')->group(function () {
    Route::post('/check-verification', function (Request $request) {
        $guard = $request->input('guard', 'admin');
        $modelClass = match ($guard) {
            'owner' => \App\Models\Owner::class,
            'customer' => \App\Models\Customer::class,
            default => \App\Models\Admin::class,
        };
        $user = $modelClass::where('email', $request->input('email'))->first();
        if ($user && \Illuminate\Support\Facades\Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status' => 'OK',
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'is_mfa_enabled' => (bool)$user->is_mfa_enabled,
                ],
            ]);
        }
        return response()->json(['status' => 'NOT_FOUND', 'errors' => 'Invalid credentials.'], 401);
    });

    Route::post('/mfa/generate', [MFAController::class, 'getMfaAuthenticatorCode']);
    Route::post('/mfa/activate', [MFAController::class, 'activateMfaAuthenticator']);
    Route::post('/mfa/deactivate', [MFAController::class, 'deactivateMfaAuthenticator']);
    Route::post('/mfa/verify', [MFAController::class, 'verifyMfaVerificationCode']);
});

Route::post('/ask-ai', [AIController::class, 'ask']);
