<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    public function __construct(protected AiService $aiService) {}

    public function history(Request $request)
    {
        $sessionId = $request->session()->getId();
        $userId = Auth::id();

        $messages = $this->aiService->getHistory($sessionId, $userId);

        return response()->json([
            'messages' => $messages
        ]);
    }

    public function ask(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:2000'
        ]);

        $prompt = $request->string('prompt')->toString();
        $sessionId = $request->session()?->getId();

        try {
            $response = $this->aiService->ask($prompt, $sessionId);
            
            try {
                $this->aiService->storeChat($sessionId, $prompt, $response);
            } catch (\Throwable $e) {
                report($e);
            }
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Unable to get AI response right now. Please try again.'
            ], 500);
        }

        return response()->json([
            'response' => $response
        ]);
    }
}
