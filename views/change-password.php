<?php
if (PHP_SAPI !== 'cli' && realpath((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === realpath(__FILE__)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('403 Forbidden');
}
?><div class="wrapper">
    <h2>Change Password</h2>
    <p>Please fill out this form to change your password.</p>

    <form action="change-password" method="post" novalidate>
        <div class="form-group <?= !empty($errors['new_password']) ? 'has-error' : '' ?>">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" autocomplete="new-password" required>
            <?php if (!empty($errors['new_password'])): ?>
                <span class="help-block"><?= e($errors['new_password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group <?= !empty($errors['confirm_password']) ? 'has-error' : '' ?>">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" autocomplete="new-password" required>
            <?php if (!empty($errors['confirm_password'])): ?>
                <span class="help-block"><?= e($errors['confirm_password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a class="btn btn-link" href="./">Cancel</a>
        </div>
    </form>
</div>
