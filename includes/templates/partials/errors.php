<?php if (! empty($errors)): ?>
    <div class="alert alert-error" role="alert">
        <strong>Please correct the following:</strong>
        <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>
