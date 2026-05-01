<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Chat</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #222;
        }

        .chat-wrap {
            max-width: 760px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .chat-head {
            background: #0f172a;
            color: #fff;
            padding: 16px 20px;
            font-size: 18px;
            font-weight: 600;
        }

        .messages {
            min-height: 360px;
            max-height: 58vh;
            overflow-y: auto;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #f8fafc;
        }

        .msg {
            padding: 10px 12px;
            border-radius: 10px;
            max-width: 88%;
            line-height: 1.45;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .msg.user {
            align-self: flex-end;
            background: #dbeafe;
        }

        .msg.ai {
            align-self: flex-start;
            background: #e2e8f0;
        }

        .msg.error {
            align-self: center;
            background: #fee2e2;
            color: #991b1b;
        }

        form {
            display: flex;
            gap: 10px;
            padding: 14px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }

        input[type="text"] {
            flex: 1;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 15px;
        }

        button {
            border: none;
            background: #0f172a;
            color: #fff;
            border-radius: 8px;
            padding: 10px 16px;
            cursor: pointer;
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="chat-wrap">
        <div class="chat-head">AI Chat Assistant</div>

        <div id="messages" class="messages"></div>

        <form id="aiForm">
            <input type="text" id="prompt" placeholder="Type your message..." autocomplete="off" required>
            <button id="sendBtn" type="submit">Send</button>
        </form>
    </div>

    <script>
        const form = document.getElementById('aiForm');
        const promptInput = document.getElementById('prompt');
        const messages = document.getElementById('messages');
        const sendBtn = document.getElementById('sendBtn');
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const historyUrl = "{{ route('ask-ai.history') }}";
        const askUrl = "{{ route('ask-ai.submit') }}";

        function appendMessage(text, role) {
            const message = document.createElement('div');
            message.className = `msg ${role}`;
            message.textContent = text;
            messages.appendChild(message);
            messages.scrollTop = messages.scrollHeight;
        }

        async function loadHistory() {
            try {
                const res = await fetch(historyUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                const history = Array.isArray(data.messages) ? data.messages : [];

                if (history.length === 0) {
                    appendMessage('Hello! Ask me anything about your car rental project.', 'ai');
                    return;
                }

                history.forEach(function (item) {
                    appendMessage(item.prompt, 'user');
                    appendMessage(item.response, 'ai');
                });
            } catch (error) {
                appendMessage('Unable to load previous chat history.', 'error');
            }
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const prompt = promptInput.value.trim();
            if (!prompt) return;

            appendMessage(prompt, 'user');
            promptInput.value = '';
            sendBtn.disabled = true;

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

                const data = await res.json();

                if (!res.ok) {
                    appendMessage(data.message || 'Something went wrong.', 'error');
                    return;
                }

                appendMessage(data.response || 'No response received.', 'ai');
            } catch (error) {
                appendMessage('Network/server error. Please try again.', 'error');
            } finally {
                sendBtn.disabled = false;
                promptInput.focus();
            }
        });

        loadHistory();
    </script>
</body>
</html>