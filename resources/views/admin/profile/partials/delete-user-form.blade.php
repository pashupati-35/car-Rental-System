<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <form method="post" action="{{ route('admin.profile.destroy') }}">
        @csrf
        @method('delete')
        <div class="form-group">
            <label for="password" class="sr-only">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}" required>
            @if ($errors->has('password'))
                <div class="text-danger mt-2">{{ $errors->first('password') }}</div>
            @endif
        </div>
        <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
    </form>
</section>
