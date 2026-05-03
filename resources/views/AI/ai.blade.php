<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Chat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0d1117;
            --surface: #161b22;
            --surface2: #21262d;
            --border: #30363d;
            --accent: #58a6ff;
            --accent-glow: rgba(88, 166, 255, 0.15);
            --user-bg: #1f4060;
            --user-text: #cae3ff;
            --ai-bg: #1e2a1e;
            --ai-text: #b3d9b3;
            --ai-border: #2d5a2d;
            --error-bg: #3d1f1f;
            --error-text: #ff9999;
            --muted: #8b949e;
            --text: #e6edf3;
            --text-dim: #c9d1d9;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Subtle grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(88,166,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(88,166,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .chat-wrap {
            width: 100%;
            max-width: 720px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 0 0 1px rgba(88,166,255,0.05), 0 24px 64px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            height: 85vh;
            max-height: 700px;
            position: relative;
            animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Header */
        .chat-head {
            padding: 16px 20px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .chat-head-icon {
            width: 34px;
            height: 34px;
            background: var(--accent-glow);
            border: 1px solid rgba(88,166,255,0.3);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .chat-head-info h2 {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .chat-head-info p {
            font-size: 12px;
            color: var(--muted);
            margin-top: 1px;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #3fb950;
            box-shadow: 0 0 6px #3fb950;
            margin-left: auto;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Messages */
        .messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            scroll-behavior: smooth;
        }

        .messages::-webkit-scrollbar { width: 4px; }
        .messages::-webkit-scrollbar-track { background: transparent; }
        .messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        /* Message rows */
        .msg-row {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            animation: msgIn 0.25s ease both;
        }

        @keyframes msgIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .msg-row.user { flex-direction: row-reverse; }

        .avatar {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
        }

        .avatar.ai-avatar {
            background: var(--ai-bg);
            border: 1px solid var(--ai-border);
            color: #3fb950;
        }

        .avatar.user-avatar {
            background: var(--user-bg);
            border: 1px solid rgba(88,166,255,0.3);
            color: var(--accent);
        }

        .msg {
            padding: 10px 14px;
            border-radius: 12px;
            max-width: 78%;
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-wrap;
            word-break: break-word;
            color: var(--text-dim);
        }

        .msg.ai {
            background: var(--ai-bg);
            border: 1px solid var(--ai-border);
            border-bottom-left-radius: 4px;
            color: var(--ai-text);
        }

        .msg.user {
            background: var(--user-bg);
            border: 1px solid rgba(88,166,255,0.2);
            border-bottom-right-radius: 4px;
            color: var(--user-text);
        }

        .msg.error {
            background: var(--error-bg);
            border: 1px solid rgba(255,100,100,0.2);
            color: var(--error-text);
            border-radius: 12px;
            align-self: center;
            max-width: 90%;
        }

        /* Typing indicator */
        .typing-indicator {
            display: flex;
            gap: 4px;
            align-items: center;
            padding: 12px 16px;
        }

        .typing-indicator span {
            width: 6px;
            height: 6px;
            background: var(--muted);
            border-radius: 50%;
            animation: typing 1.2s infinite;
        }

        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30% { transform: translateY(-5px); opacity: 1; }
        }

        /* Empty state */
        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--muted);
            text-align: center;
            padding: 40px;
        }

        .empty-state .icon { font-size: 32px; margin-bottom: 4px; }
        .empty-state h3 { font-size: 15px; font-weight: 500; color: var(--text-dim); }
        .empty-state p { font-size: 13px; line-height: 1.5; }

        /* Suggestions */
        .suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-top: 8px;
        }

        .suggestion-chip {
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text-dim);
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'DM Sans', sans-serif;
        }

        .suggestion-chip:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-glow);
        }

        /* Input area */
        .input-area {
            padding: 14px 16px;
            border-top: 1px solid var(--border);
            background: var(--surface);
            flex-shrink: 0;
        }

        .input-row {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 8px 8px 8px 14px;
            transition: border-color 0.2s;
        }

        .input-row:focus-within {
            border-color: rgba(88,166,255,0.5);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        #prompt {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            resize: none;
            line-height: 1.5;
            max-height: 120px;
            padding: 4px 0;
        }

        #prompt::placeholder { color: var(--muted); }

        #sendBtn {
            background: var(--accent);
            color: #0d1117;
            border: none;
            border-radius: 8px;
            width: 36px;
            height: 36px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s;
            font-size: 16px;
        }

        #sendBtn:hover:not(:disabled) {
            background: #79b8ff;
            transform: scale(1.05);
        }

        #sendBtn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }

        .input-hint {
            font-size: 11px;
            color: var(--muted);
            margin-top: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="chat-wrap">
        <!-- Header -->
        <div class="chat-head">
            <div class="chat-head-icon">🚗</div>
            <div class="chat-head-info">
                <h2>Car Rental Assistant</h2>
                <p>Powered by Groq · Llama 3.3</p>
            </div>
            <div class="status-dot"></div>
        </div>

        <!-- Messages -->
        <div id="messages" class="messages">
            <div class="empty-state" id="emptyState">
                <div class="icon">🚗</div>
                <h3>How can I help you today?</h3>
                <p>Ask me about available cars,<br>bookings, or rental information.</p>
                <div class="suggestions">
                    <button class="suggestion-chip" onclick="useSuggestion(this)">Available cars</button>
                    <button class="suggestion-chip" onclick="useSuggestion(this)">Recent bookings</button>
                    <button class="suggestion-chip" onclick="useSuggestion(this)">How to rent a car?</button>
                </div>
            </div>
        </div>

        <!-- Input -->
        <div class="input-area">
            <div class="input-row">
                <textarea id="prompt" placeholder="Ask about cars, bookings..." rows="1" autocomplete="off"></textarea>
                <button id="sendBtn" type="button" title="Send message">&#9658;</button>
            </div>
            <p class="input-hint">Press Enter to send &nbsp;·&nbsp; Shift+Enter for new line</p>
        </div>
    </div>

    <script>
        const promptInput = document.getElementById('prompt');
        const messagesEl  = document.getElementById('messages');
        const sendBtn     = document.getElementById('sendBtn');
        const emptyState  = document.getElementById('emptyState');
        const csrf        = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const historyUrl  = "{{ route('ask-ai.history') }}";
        const askUrl      = "{{ route('ask-ai.submit') }}";

        // Auto-resize textarea
        promptInput.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Enter to send, Shift+Enter for newline
        promptInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                handleSend();
            }
        });

        sendBtn.addEventListener('click', handleSend);

        function hideEmptyState() {
            if (emptyState) emptyState.style.display = 'none';
        }

        function useSuggestion(btn) {
            promptInput.value = btn.textContent;
            handleSend();
        }

        function appendMessage(text, role) {
            hideEmptyState();

            const row = document.createElement('div');
            row.className = `msg-row ${role}`;

            if (role !== 'error') {
                const avatar = document.createElement('div');
                avatar.className = `avatar ${role}-avatar`;
                avatar.textContent = role === 'ai' ? '🤖' : 'U';
                row.appendChild(avatar);
            }

            const bubble = document.createElement('div');
            bubble.className = `msg ${role}`;
            bubble.textContent = text;
            row.appendChild(bubble);

            messagesEl.appendChild(row);
            messagesEl.scrollTop = messagesEl.scrollHeight;
            return bubble;
        }

        function appendTyping() {
            hideEmptyState();
            const row = document.createElement('div');
            row.className = 'msg-row ai';
            row.id = 'typingRow';

            const avatar = document.createElement('div');
            avatar.className = 'avatar ai-avatar';
            avatar.textContent = '🤖';

            const bubble = document.createElement('div');
            bubble.className = 'msg ai';
            bubble.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';

            row.appendChild(avatar);
            row.appendChild(bubble);
            messagesEl.appendChild(row);
            messagesEl.scrollTop = messagesEl.scrollHeight;
            return { row, bubble };
        }

        async function loadHistory() {
            try {
                const res  = await fetch(historyUrl, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                const history = Array.isArray(data.messages) ? data.messages : [];

                if (history.length === 0) return; // keep empty state

                history.forEach(function (item) {
                    if (item.prompt)   appendMessage(item.prompt, 'user');
                    if (item.response) appendMessage(item.response, 'ai');
                });
            } catch (e) {
                // silently ignore history load errors
            }
        }

        async function handleSend() {
            const prompt = promptInput.value.trim();
            if (!prompt || sendBtn.disabled) return;

            appendMessage(prompt, 'user');
            promptInput.value = '';
            promptInput.style.height = 'auto';
            sendBtn.disabled = true;

            const { row: typingRow, bubble: typingBubble } = appendTyping();

            try {
                const res = await fetch(askUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ prompt })
                });

                // Remove typing indicator
                typingRow.remove();

                if (!res.ok) {
                    const errData = await res.json().catch(() => ({}));
                    appendMessage(errData.message || 'Something went wrong. Please try again.', 'error');
                    return;
                }

                const data = await res.json();

                // Extract just the text — handles both plain string and JSON-wrapped responses
                let text = '';
                if (typeof data.response === 'string') {
                    // Try to unwrap if the AI returned JSON as a string e.g. {"response":"..."}
                    try {
                        const inner = JSON.parse(data.response);
                        text = inner.response ?? data.response;
                    } catch {
                        text = data.response;
                    }
                } else {
                    text = 'No response received.';
                }

                appendMessage(text.trim() || 'No response received.', 'ai');

            } catch (err) {
                typingRow.remove();
                appendMessage('Network error. Please check your connection and try again.', 'error');
            } finally {
                sendBtn.disabled = false;
                promptInput.focus();
            }
        }

        loadHistory();
    </script>
</body>
</html>