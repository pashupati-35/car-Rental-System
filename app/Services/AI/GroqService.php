<?php

namespace App\Services\AI;

use GuzzleHttp\Client;
use Throwable;

class GroqService
{
    private Client $client;
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->model = config('services.groq.model', 'llama-3.3-70b-versatile');
        $this->client = new Client();
    }

    /**
     * Ask Groq AI a question with context and history
     * Responds in ~100-200ms typically
     */
    public function ask(string $prompt, array $history = [], string $databaseContext = ''): string
    {
        $messages = $this->buildMessages($prompt, $history, $databaseContext);

        try {
            $response = $this->client->post('https://api.groq.com/openai/v1/chat/completions', [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 1024,
                    'top_p' => 1,
                ],
                'timeout' => 10,
            ]);

            $data = json_decode($response->getBody(), true);

            return $data['choices'][0]['message']['content'] ?? 'No response from AI.';
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }

    private function buildMessages(string $prompt, array $history, string $databaseContext): array
    {
        $messages = [];

        $systemPrompt = 'You are a helpful car rental AI. Reply briefly and clearly. ';
        $systemPrompt .= 'Use conversation history to understand context. ';
        $systemPrompt .= 'For car/booking questions, reference the database info only. ';
        $systemPrompt .= 'Do not make up information. Keep responses short (1-3 sentences).';

        if (trim($databaseContext) !== '') {
            $systemPrompt .= "\n\nDatabase summary:\n" . $databaseContext;
        }

        $messages[] = [
            'role' => 'system',
            'content' => $systemPrompt,
        ];

        $historyTurns = array_slice($history, -6);
        foreach ($historyTurns as $turn) {
            if (isset($turn['prompt'])) {
                $messages[] = [
                    'role' => 'user',
                    'content' => $turn['prompt'],
                ];
            }
            if (isset($turn['response'])) {
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $turn['response'],
                ];
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $prompt,
        ];

        return $messages;
    }
}