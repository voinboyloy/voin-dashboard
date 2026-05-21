<section>
    <header class="panel-header" style="border-bottom: none; padding-bottom: 0; margin-bottom: 20px;">
        <h2 class="panel-title">
            {{ __('Update Password') }}
        </h2>

        <p class="panel-subtitle">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="update_password_current_password">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="input" autocomplete="current-password" />
            @if($errors->updatePassword->get('current_password'))
                <p class="tiny" style="color: var(--color-error); margin-top: 4px;">{{ $errors->updatePassword->get('current_password')[0] }}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="update_password_password">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="input" autocomplete="new-password" />
            @if($errors->updatePassword->get('password'))
                <p class="tiny" style="color: var(--color-error); margin-top: 4px;">{{ $errors->updatePassword->get('password')[0] }}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="input" autocomplete="new-password" />
            @if($errors->updatePassword->get('password_confirmation'))
                <p class="tiny" style="color: var(--color-error); margin-top: 4px;">{{ $errors->updatePassword->get('password_confirmation')[0] }}</p>
            @endif
        </div>

        <div style="display: flex; align-items: center; gap: 16px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">{{ __('Update Password') }}</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="tiny"
                    style="color: var(--text-secondary);"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
