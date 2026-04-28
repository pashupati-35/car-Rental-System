<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-4 px-4">
    <v-card class="w-full max-w-md shadow-2xl">
      <!-- Logo -->
      <div class="flex justify-center pt-8 pb-4">
        <div class="text-center">
          <v-icon size="48" color="primary" class="mb-2">mdi-car-key</v-icon>
          <h1 class="text-2xl font-bold text-gray-800">Car Rental</h1>
          <p class="text-sm text-gray-500">Owner Dashboard</p>
        </div>
      </div>

      <v-card-text class="pt-4">
        <!-- Title -->
        <h2 class="text-xl font-semibold text-center mb-2">Login to Your Account</h2>
        <p class="text-center text-sm text-gray-600 mb-6">Enter your email & password to login</p>

        <!-- Error Alert -->
        <v-alert
          v-if="form.errors.email || form.errors.password"
          type="error"
          variant="tonal"
          class="mb-4"
          icon="mdi-alert-circle"
          closable
        >
          <div v-if="form.errors.email">{{ form.errors.email[0] }}</div>
          <div v-if="form.errors.password">{{ form.errors.password[0] }}</div>
        </v-alert>

        <!-- Form -->
        <v-form @submit.prevent="submitForm">
          <!-- Email -->
          <v-text-field
            v-model="form.email"
            label="Email Address"
            type="email"
            prepend-inner-icon="mdi-email"
            variant="outlined"
            outlined
            required
            :error="!!form.errors.email"
            :error-messages="form.errors.email"
            class="mb-4"
          />

          <!-- Password -->
          <v-text-field
            v-model="form.password"
            label="Password"
            type="password"
            prepend-inner-icon="mdi-lock"
            variant="outlined"
            outlined
            required
            :error="!!form.errors.password"
            :error-messages="form.errors.password"
            class="mb-4"
          />

          <!-- Remember Me -->
          <v-checkbox
            v-model="form.remember"
            label="Remember me"
            class="mb-6"
          />

          <!-- Login Button -->
          <v-btn
            type="submit"
            block
            color="primary"
            size="large"
            :loading="form.processing"
          >
            Login
          </v-btn>
        </v-form>

        <!-- Divider -->
        <div class="my-4 flex items-center">
          <div class="flex-1 border-t border-gray-300"></div>
          <span class="px-3 text-sm text-gray-500">Or</span>
          <div class="flex-1 border-t border-gray-300"></div>
        </div>

        <!-- Register Link -->
        <p class="text-center text-sm text-gray-600">
          Don't have an account?
          <Link
            href="/owner/register"
            class="text-blue-600 font-semibold hover:text-blue-800 transition"
          >
            Create one here
          </Link>
        </p>
      </v-card-text>
    </v-card>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submitForm = () => {
  form.post('/owner/login', {
    onError: () => {
      // Errors are automatically available in form.errors
    },
  });
};
</script>

<style scoped>
.min-h-screen {
  min-height: 100vh;
}

.max-w-md {
  max-width: 28rem;
}

.bg-gradient-to-br {
  background: linear-gradient(to bottom right, #eff6ff, #e0e7ff);
}

.shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.text-gray-800 {
  color: #1f2937;
}

.text-gray-600 {
  color: #4b5563;
}

.text-blue-600 {
  color: #2563eb;
}

.hover\:text-blue-800:hover {
  color: #1e40af;
}
</style>
