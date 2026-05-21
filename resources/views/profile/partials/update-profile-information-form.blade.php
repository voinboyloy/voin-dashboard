<section>
    <header class="panel-header" style="border-bottom: none; padding-bottom: 0; margin-bottom: 20px;">
        <h2 class="panel-title">
            {{ __('Profile Information') }}
        </h2>

        <p class="panel-subtitle">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6">
        @csrf
        @method('patch')

        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="input" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @if($errors->get('name'))
                <p class="tiny" style="color: var(--color-error); margin-top: 4px;">{{ $errors->get('name')[0] }}</p>
            @endif
        </div>

        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="input" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @if($errors->get('email'))
                <p class="tiny" style="color: var(--color-error); margin-top: 4px;">{{ $errors->get('email')[0] }}</p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top: 12px;">
                    <p class="tiny" style="color: var(--text-secondary);">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="btn-ghost" style="padding: 0; font-size: inherit; text-decoration: underline;">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="tiny" style="color: var(--accent-teal); font-weight: 600; margin-top: 4px;">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div style="display: flex; align-items: center; gap: 16px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>

            @if (session('status') === 'profile-updated')
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
