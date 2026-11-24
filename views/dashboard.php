<div class="dashboard">
    <div class="page-header">
        <h1>Hi, <b><?= e($username) ?></b>. Welcome to our site.</h1>
    </div>
    <p>
        <a href="change-password" class="btn btn-warning">Change Your Password</a>
        <form action="logout" method="post" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <button type="submit" class="btn btn-danger">Sign Out of Your Account</button>
        </form>
    </p>
</div>
