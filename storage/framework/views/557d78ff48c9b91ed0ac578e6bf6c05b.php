<section>
    <header class="panel-header" style="border-bottom: none; padding-bottom: 0; margin-bottom: 20px;">
        <h2 class="panel-title">
            <?php echo e(__('Update Password')); ?>

        </h2>

        <p class="panel-subtitle">
            <?php echo e(__('Ensure your account is using a long, random password to stay secure.')); ?>

        </p>
    </header>

    <form method="post" action="<?php echo e(route('password.update')); ?>" class="mt-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('put'); ?>

        <div class="form-group">
            <label for="update_password_current_password"><?php echo e(__('Current Password')); ?></label>
            <input id="update_password_current_password" name="current_password" type="password" class="input" autocomplete="current-password" />
            <?php if($errors->updatePassword->get('current_password')): ?>
                <p class="tiny" style="color: var(--color-error); margin-top: 4px;"><?php echo e($errors->updatePassword->get('current_password')[0]); ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="update_password_password"><?php echo e(__('New Password')); ?></label>
            <input id="update_password_password" name="password" type="password" class="input" autocomplete="new-password" />
            <?php if($errors->updatePassword->get('password')): ?>
                <p class="tiny" style="color: var(--color-error); margin-top: 4px;"><?php echo e($errors->updatePassword->get('password')[0]); ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation"><?php echo e(__('Confirm Password')); ?></label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="input" autocomplete="new-password" />
            <?php if($errors->updatePassword->get('password_confirmation')): ?>
                <p class="tiny" style="color: var(--color-error); margin-top: 4px;"><?php echo e($errors->updatePassword->get('password_confirmation')[0]); ?></p>
            <?php endif; ?>
        </div>

        <div style="display: flex; align-items: center; gap: 16px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary"><?php echo e(__('Update Password')); ?></button>

            <?php if(session('status') === 'password-updated'): ?>
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
<?php /**PATH /app/resources/views/profile/partials/update-password-form.blade.php ENDPATH**/ ?>