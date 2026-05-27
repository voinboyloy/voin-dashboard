<section>
    <header class="panel-header" style="border-bottom: none; padding-bottom: 0; margin-bottom: 20px;">
        <h2 class="panel-title">
            <?php echo e(__('Delete Account')); ?>

        </h2>

        <p class="panel-subtitle">
            <?php echo e(__('Once your account is deleted, all of its resources and data will be permanently deleted.')); ?>

        </p>
    </header>

    <button
        class="btn btn-secondary"
        style="color: var(--color-error); border-color: var(--color-error);"
        onclick="document.getElementById('confirm-user-deletion-modal').style.display = 'flex'"
    ><?php echo e(__('Delete Account')); ?></button>

    <div id="confirm-user-deletion-modal" class="modal">
        <div class="card" style="max-width: 500px;">
            <form method="post" action="<?php echo e(route('profile.destroy')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('delete'); ?>

                <h2 class="panel-title">
                    <?php echo e(__('Are you sure you want to delete your account?')); ?>

                </h2>

                <p class="panel-subtitle" style="margin-top: 12px; line-height: 1.4;">
                    <?php echo e(__('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.')); ?>

                </p>

                <div class="form-group" style="margin-top: 24px;">
                    <label for="password" class="sr-only"><?php echo e(__('Password')); ?></label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="input"
                        placeholder="<?php echo e(__('Confirm with Password')); ?>"
                        required
                    />
                    <?php if($errors->userDeletion->get('password')): ?>
                        <p class="tiny" style="color: var(--color-error); margin-top: 4px;"><?php echo e($errors->userDeletion->get('password')[0]); ?></p>
                    <?php endif; ?>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('confirm-user-deletion-modal').style.display = 'none'">
                        <?php echo e(__('Cancel')); ?>

                    </button>

                    <button type="submit" class="btn btn-primary" style="background: var(--color-error); border-color: var(--color-error);">
                        <?php echo e(__('Delete Account')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php /**PATH /app/resources/views/profile/partials/delete-user-form.blade.php ENDPATH**/ ?>