<?php

namespace App\Services\AI;

use LLPhant\Chat\Message;
use LLPhant\Chat\OllamaChat;
use LLPhant\OllamaConfig;
use Psr\Http\Message\StreamInterface;

class ChatService
{
    public function ask(string $prompt, array $conversationHistory = [], string $databaseContext = ''): string
    {
        $config = new OllamaConfig();
        $config->model = 'llama3';
        $chat = new OllamaChat($config);
        $messages = $this->buildMessages($prompt, $conversationHistory, $databaseContext);

        return $chat->generateChat($messages);
    }

    public function askStream(string $prompt, array $conversationHistory = [], string $databaseContext = ''): StreamInterface
    {
        $config = new OllamaConfig();
        $config->model = 'llama3';
        $chat = new OllamaChat($config);
        $messages = $this->buildMessages($prompt, $conversationHistory, $databaseContext);

        return $chat->generateChatStream($messages);
    }

    /**
     * @param array<int, array{prompt?: string, response?: string}> $conversationHistory
     * @return Message[]
     */
    private function buildMessages(string $prompt, array $conversationHistory, string $databaseContext): array
    {
        $messages = [
            Message::system($this->buildSystemPrompt($databaseContext)),
        ];

        $recentHistory = array_slice($conversationHistory, -4);

        foreach ($recentHistory as $message) {
            $userPrompt = trim((string) ($message['prompt'] ?? ''));
            $assistantResponse = trim((string) ($message['response'] ?? ''));

            if ($userPrompt !== '') {
                $messages[] = Message::user($userPrompt);
            }

            if ($assistantResponse !== '') {
                $messages[] = Message::assistant($assistantResponse);
            }
        }

        $messages[] = Message::user($prompt);

        return $messages;
    }

    private function buildSystemPrompt(string $databaseContext): string
    {
        $prompt = 'You are a helpful car rental assistant. Reply briefly and clearly. Use conversation history to understand context. Answer car/booking questions from the provided data only. Do not invent information. Keep responses short.';

        if (trim($databaseContext) !== '') {
            $prompt .= "\n\nData:\n".$databaseContext;
        }

        return $prompt;
    }
}
