<section>
    <header class="panel-header" style="border-bottom: none; padding-bottom: 0; margin-bottom: 20px;">
        <h2 class="panel-title">
            <?php echo e(__('Profile Information')); ?>

        </h2>

        <p class="panel-subtitle">
            <?php echo e(__("Update your account's profile information and email address.")); ?>

        </p>
    </header>

    <form id="send-verification" method="post" action="<?php echo e(route('verification.send')); ?>">
        <?php echo csrf_field(); ?>
    </form>

    <form method="post" action="<?php echo e(route('profile.update')); ?>" class="mt-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('patch'); ?>

        <div class="form-group">
            <label for="name"><?php echo e(__('Name')); ?></label>
            <input id="name" name="name" type="text" class="input" value="<?php echo e(old('name', $user->name)); ?>" required autofocus autocomplete="name" />
            <?php if($errors->get('name')): ?>
                <p class="tiny" style="color: var(--color-error); margin-top: 4px;"><?php echo e($errors->get('name')[0]); ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email"><?php echo e(__('Email')); ?></label>
            <input id="email" name="email" type="email" class="input" value="<?php echo e(old('email', $user->email)); ?>" required autocomplete="username" />
            <?php if($errors->get('email')): ?>
                <p class="tiny" style="color: var(--color-error); margin-top: 4px;"><?php echo e($errors->get('email')[0]); ?></p>
            <?php endif; ?>

            <?php if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()): ?>
                <div style="margin-top: 12px;">
                    <p class="tiny" style="color: var(--text-secondary);">
                        <?php echo e(__('Your email address is unverified.')); ?>


                        <button form="send-verification" class="btn-ghost" style="padding: 0; font-size: inherit; text-decoration: underline;">
                            <?php echo e(__('Click here to re-send the verification email.')); ?>

                        </button>
                    </p>

                    <?php if(session('status') === 'verification-link-sent'): ?>
                        <p class="tiny" style="color: var(--accent-teal); font-weight: 600; margin-top: 4px;">
                            <?php echo e(__('A new verification link has been sent to your email address.')); ?>

                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: flex; align-items: center; gap: 16px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary"><?php echo e(__('Save Changes')); ?></button>

            <?php if(session('status') === 'profile-updated'): ?>
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="tiny"
                    style="color: var(--text-secondary);"
                ><?php echo e(__('Saved.')); ?></p>
            <?php endif; ?>
        </div>
    </form>
</section>
<?php /**PATH /app/resources/views/profile/partials/update-profile-information-form.blade.php ENDPATH**/ ?>