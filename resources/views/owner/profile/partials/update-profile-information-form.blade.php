<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form method="post" action="{{ route('owner.profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Full Name -->
        <div class="form-group">
            <x-input-label for="full_name" class="x-input-label" :value="__('Full Name')" />
            <x-text-input id="full_name" name="full_name" type="text" class="input-field" :value="old('full_name', Auth::user()->full_name)" required autofocus autocomplete="name" />
            <x-input-error class="input-error" :messages="$errors->get('full_name')" />
        </div>

        <!-- Contact Number -->
        <div class="form-group">
            <x-input-label for="contact_number" class="x-input-label" :value="__('Contact Number')" />
            <x-text-input id="contact_number" name="contact_number" type="text" class="input-field" :value="old('contact_number', Auth::user()->contact_number)" required autocomplete="tel" />
            <x-input-error class="input-error" :messages="$errors->get('contact_number')" />
        </div>

        <!-- Address -->
        <div class="form-group">
            <x-input-label for="address" class="x-input-label" :value="__('Address')" />
            <x-text-input id="address" name="address" type="text" class="input-field" :value="old('address', Auth::user()->address)" required autocomplete="address" />
            <x-input-error class="input-error" :messages="$errors->get('address')" />
        </div>

        <!-- Gender -->
        <div class="form-group">
            <x-input-label for="gender" class="x-input-label" :value="__('Gender')" />
            <select id="gender" name="gender" class="select-field">
                <option value="Male" {{ old('gender', Auth::user()->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender', Auth::user()->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Other" {{ old('gender', Auth::user()->gender) == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            <x-input-error class="input-error" :messages="$errors->get('gender')" />
        </div>

        <!-- Email -->
        <div class="form-group">
            <x-input-label for="email" class="x-input-label" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="input-field" :value="old('email', Auth::user()->email)" required autocomplete="username" />
            <x-input-error class="input-error" :messages="$errors->get('email')" />

            @if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !Auth::user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Save Button -->
        <div class="flex items-center gap-4">
            <button type="submit" class="button-save">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
