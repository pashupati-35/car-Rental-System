<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import axios from 'axios'

interface Message {
  role: 'user' | 'assistant'
  content: string
}

const messages = ref<Message[]>([
  {
    role: 'assistant',
    content: 'Hello! I am your AI Rental Assistant. How can I help you today with finding a car, checking rates, or rental policies?',
  },
])

const userInput = ref('')
const loading = ref(false)

const sendMessage = async () => {
  if (!userInput.value.trim() || loading.value) return

  const prompt = userInput.value
  messages.value.push({ role: 'user', content: prompt })
  userInput.value = ''
  loading.value = true

  try {
    const res = await axios.post('/ai-chat', { message: prompt })
    messages.value.push({
      role: 'assistant',
      content: res.data.reply || res.data.response || 'I am here to assist with your rental needs!',
    })
  } catch (err) {
    messages.value.push({
      role: 'assistant',
      content: 'Sorry, I encountered an issue processing your request. Please try again.',
    })
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <FrontendLayout>
    <Head title="AI Fleet Assistant" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden flex flex-col h-[75vh]">
        <!-- Chat Header -->
        <div class="p-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-xl">
            <i class="ri-sparkling-fill"></i>
          </div>
          <div>
            <h3 class="font-bold text-base">Car Rental AI Assistant</h3>
            <p class="text-xs text-blue-100">Powered by Llama & LLPhant</p>
          </div>
        </div>

        <!-- Messages list -->
        <div class="flex-1 p-6 overflow-y-auto space-y-4">
          <div
            v-for="(msg, i) in messages"
            :key="i"
            :class="msg.role === 'user' ? 'justify-end' : 'justify-start'"
            class="flex items-start gap-3"
          >
            <div
              v-if="msg.role === 'assistant'"
              class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs shrink-0 font-bold"
            >
              AI
            </div>
            <div
              :class="msg.role === 'user' ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-tl-none'"
              class="max-w-xl p-4 rounded-2xl text-sm leading-relaxed shadow-sm whitespace-pre-wrap"
            >
              {{ msg.content }}
            </div>
          </div>
        </div>

        <!-- Chat Input -->
        <div class="p-4 bg-gray-50 dark:bg-gray-850 border-t border-gray-100 dark:border-gray-800 flex gap-2">
          <input
            v-model="userInput"
            @keyup.enter="sendMessage"
            type="text"
            placeholder="Ask about car recommendations, pricing, availability..."
            class="flex-1 px-4 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
          />
          <button
            @click="sendMessage"
            :disabled="loading"
            class="px-5 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-500/20 disabled:opacity-50 transition-all flex items-center gap-1.5"
          >
            <span>Send</span>
            <i class="ri-send-plane-fill"></i>
          </button>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>
