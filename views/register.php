<div class="wrapper">
    <h2>Sign Up</h2>
    <p>Please fill this form to create an account.</p>

    <form action="register" method="post" novalidate>
        <div class="form-group <?= !empty($errors['username']) ? 'has-error' : '' ?>">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= e($username ?? '') ?>" autocomplete="username" required>
            <?php if (!empty($errors['username'])): ?>
                <span class="help-block"><?= e($errors['username']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group <?= !empty($errors['password']) ? 'has-error' : '' ?>">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" autocomplete="new-password" required>
            <?php if (!empty($errors['password'])): ?>
                <span class="help-block"><?= e($errors['password']) ?></span>
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
            <input type="reset" class="btn btn-default" value="Reset">
        </div>

        <p>Already have an account? <a href="login">Login here</a>.</p>
    </form>
</div>
