<div class="wrapper text-center">
    <h2>Error <?= e($statusCode ?? 500) ?></h2>
    <p><?= e($message ?? 'Something went wrong.') ?></p>
    <p><a href="/" class="btn btn-primary">Back to Home</a></p>
</div>
