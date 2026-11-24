<div class="wrapper">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>

    <?php if (!empty($flash)): ?>
        <div class="alert alert-success" role="status"><?= e($flash) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors['form'])): ?>
        <div class="alert alert-danger" role="alert"><?= e($errors['form']) ?></div>
    <?php endif; ?>

    <form action="login" method="post" novalidate>
        <div class="form-group <?= !empty($errors['username']) ? 'has-error' : '' ?>">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= e($username ?? '') ?>" autocomplete="username" required>
            <?php if (!empty($errors['username'])): ?>
                <span class="help-block"><?= e($errors['username']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group <?= !empty($errors['password']) ? 'has-error' : '' ?>">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
            <?php if (!empty($errors['password'])): ?>
                <span class="help-block"><?= e($errors['password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <input type="submit" class="btn btn-primary" value="Login">
        </div>

        <p>Don't have an account? <a href="register">Sign up now</a>.</p>
    </form>
</div>
