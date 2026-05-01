<?php

namespace App\Services\AI;

use LLPhant\Chat\OllamaChat;
use LLPhant\OllamaConfig;

class ChatService
{
    public function ask(string $prompt): string
    {
        $config = new OllamaConfig();
        $config->model = 'llama3';

        $chat = new OllamaChat($config);

        return $chat->generateText($prompt);
    }
}
