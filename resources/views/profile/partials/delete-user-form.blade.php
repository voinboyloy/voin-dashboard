<section>
    <header class="panel-header" style="border-bottom: none; padding-bottom: 0; margin-bottom: 20px;">
        <h2 class="panel-title">
            {{ __('Delete Account') }}
        </h2>

        <p class="panel-subtitle">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <button
        class="btn btn-secondary"
        style="color: var(--color-error); border-color: var(--color-error);"
        onclick="document.getElementById('confirm-user-deletion-modal').style.display = 'flex'"
    >{{ __('Delete Account') }}</button>

    <div id="confirm-user-deletion-modal" class="modal">
        <div class="card" style="max-width: 500px;">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <h2 class="panel-title">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>

                <p class="panel-subtitle" style="margin-top: 12px; line-height: 1.4;">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <div class="form-group" style="margin-top: 24px;">
                    <label for="password" class="sr-only">{{ __('Password') }}</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="input"
                        placeholder="{{ __('Confirm with Password') }}"
                        required
                    />
                    @if($errors->userDeletion->get('password'))
                        <p class="tiny" style="color: var(--color-error); margin-top: 4px;">{{ $errors->userDeletion->get('password')[0] }}</p>
                    @endif
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('confirm-user-deletion-modal').style.display = 'none'">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit" class="btn btn-primary" style="background: var(--color-error); border-color: var(--color-error);">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
