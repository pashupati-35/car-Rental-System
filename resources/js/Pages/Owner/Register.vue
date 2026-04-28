<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-4 px-4">
    <v-card class="w-full max-w-md shadow-2xl">
      <!-- Logo -->
      <div class="flex justify-center pt-8 pb-4">
        <div class="text-center">
          <v-icon size="48" color="primary" class="mb-2">mdi-car-key</v-icon>
          <h1 class="text-2xl font-bold text-gray-800">Car Rental</h1>
          <p class="text-sm text-gray-500">Owner Registration</p>
        </div>
      </div>

      <v-card-text class="pt-4">
        <!-- Title -->
        <h2 class="text-xl font-semibold text-center mb-2">Create Your Account</h2>
        <p class="text-center text-sm text-gray-600 mb-6">Register to start managing your cars</p>

        <!-- Error Alert -->
        <v-alert
          v-if="Object.keys(form.errors).length > 0"
          type="error"
          variant="tonal"
          class="mb-4"
          icon="mdi-alert-circle"
          closable
        >
          <div v-for="(messages, field) in form.errors" :key="field">
            {{ messages[0] }}
          </div>
        </v-alert>

        <!-- Form -->
        <v-form @submit.prevent="submitForm">
          <!-- Name -->
          <v-text-field
            v-model="form.name"
            label="Full Name"
            prepend-inner-icon="mdi-account"
            variant="outlined"
            outlined
            required
            :error="!!form.errors.name"
            :error-messages="form.errors.name"
            class="mb-4"
          />

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

          <!-- Phone -->
          <v-text-field
            v-model="form.phone"
            label="Phone Number"
            prepend-inner-icon="mdi-phone"
            variant="outlined"
            outlined
            :error="!!form.errors.phone"
            :error-messages="form.errors.phone"
            class="mb-4"
          />

          <!-- Address -->
          <v-text-field
            v-model="form.address"
            label="Address"
            prepend-inner-icon="mdi-map-marker"
            variant="outlined"
            outlined
            :error="!!form.errors.address"
            :error-messages="form.errors.address"
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

          <!-- Confirm Password -->
          <v-text-field
            v-model="form.password_confirmation"
            label="Confirm Password"
            type="password"
            prepend-inner-icon="mdi-lock-check"
            variant="outlined"
            outlined
            required
            :error="!!form.errors.password_confirmation"
            :error-messages="form.errors.password_confirmation"
            class="mb-6"
          />

          <!-- Register Button -->
          <v-btn
            type="submit"
            block
            color="primary"
            size="large"
            :loading="form.processing"
          >
            Register
          </v-btn>
        </v-form>

        <!-- Divider -->
        <div class="my-4 flex items-center">
          <div class="flex-1 border-t border-gray-300"></div>
          <span class="px-3 text-sm text-gray-500">Or</span>
          <div class="flex-1 border-t border-gray-300"></div>
        </div>

        <!-- Login Link -->
        <p class="text-center text-sm text-gray-600">
          Already have an account?
          <Link
            href="/owner/login"
            class="text-blue-600 font-semibold hover:text-blue-800 transition"
          >
            Login here
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
  name: '',
  email: '',
  phone: '',
  address: '',
  password: '',
  password_confirmation: '',
});

const submitForm = () => {
  form.post('/owner/register', {
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
