<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\AiChat;
use App\Services\AI\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    public function history(Request $request)
    {
        $sessionId = $request->session()->getId();
        $userId = Auth::id();

        $query = AiChat::query()->orderBy('id');

        if ($userId) {
            $query->where(function ($builder) use ($userId, $sessionId) {
                $builder->where('user_id', $userId)
                    ->orWhere('session_id', $sessionId);
            });
        } else {
            $query->where('session_id', $sessionId);
        }

        return response()->json([
            'messages' => $query->limit(50)->get(['prompt', 'response'])
        ]);
    }

    public function ask(Request $request, ChatService $chat)
    {
        $request->validate([
            'prompt' => 'required|string|max:2000'
        ]);

        $prompt = $request->string('prompt')->toString();

        try {
            $response = $chat->ask($prompt);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Unable to get AI response right now. Please try again.'
            ], 500);
        }

        $userId = Auth::id() ?? null;
        if ($userId) {
            $userName = Auth::user()->name ?? 'User';
        } else {
            $userName = 'Guest';
        }
        AiChat::create([
            'user_id' => $userId,
            'user_name' => $userName,
            'session_id' => $request->session()?->getId(),
            'prompt' => $prompt,
            'response' => $response,
        ]);

        return response()->json([
            'response' => $response
        ]);
    }
}
