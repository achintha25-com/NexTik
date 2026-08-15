<?php

require __DIR__.'/includes/bootstrap.php';

if (current_user()) redirect_to('dashboard');
$role = query_string('role') ?: 'customer';
if (! valid_role($role)) $role = 'customer';
$errors = [];
if (is_post()) {
    verify_csrf();
    $email = strtolower(post_string('email'));
    $password = (string) ($_POST['password'] ?? '');
    $statement = db()->prepare('SELECT id, password, role FROM users WHERE email = ? LIMIT 1');
    $statement->execute([$email]);
    $account = $statement->fetch();
    if (! $account || ! password_verify($password, $account['password']) || $account['role'] !== $role) {
        $errors[] = 'Those details do not match a '.$role.' account.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $account['id'];
        $destination = $_SESSION['intended_url'] ?? null;
        unset($_SESSION['intended_url']);
        if (is_string($destination) && str_starts_with($destination, '/')) { header('Location: '.$destination); exit; }
        redirect_to('dashboard');
    }
}

$copy = match ($role) {
    'organizer' => [
        'kicker' => 'Organizer access',
        'title' => 'Open your events workspace.',
        'lead' => 'Publish nights, manage ticket types, and track who is walking in.',
        'submit' => 'Sign in as organizer',
        'visual_kicker' => 'FOR ORGANIZERS',
        'visual_title' => 'Run the night.',
        'points' => ['Create and publish events', 'Set VIP, standing, and more', 'See bookings as they land'],
    ],
    'admin' => [
        'kicker' => 'Admin access',
        'title' => 'Keep NexTik on track.',
        'lead' => 'Manage events, categories, and the support inbox from one place.',
        'submit' => 'Sign in as admin',
        'visual_kicker' => 'FOR ADMINISTRATORS',
        'visual_title' => 'Steer the platform.',
        'points' => ['Oversee every listing', 'Reply to customer messages', 'Watch bookings and revenue'],
    ],
    default => [
        'kicker' => 'Customer login',
        'title' => 'Welcome back.',
        'lead' => 'Sign in to book the next night and keep every pass in one place.',
        'submit' => 'Sign in',
        'visual_kicker' => 'YOUR NEXT NIGHT',
        'visual_title' => 'is one sign-in away.',
        'points' => ['Instant digital tickets', 'Bookings saved in your account', 'Ready at the door'],
    ],
};

$title = ucfirst($role).' login';
$bodyClass = 'login-page';
$user = current_user();
$flashMessages = consume_flash();
$emailValue = (string) ($_POST['email'] ?? '');
require __DIR__.'/includes/header.php';
?>

<section class="login-stage">
    <div class="container login-stage-grid">
        <div class="login-panel">
            <p class="catalog-eyebrow"><?= e($copy['kicker']) ?></p>
            <h1><?= e($copy['title']) ?></h1>
            <p class="login-lead"><?= e($copy['lead']) ?></p>

            <nav class="login-role-switcher" aria-label="Account type">
                <a class="<?= $role === 'customer' ? 'is-active' : '' ?>" href="<?= e(app_url('login', ['role' => 'customer'])) ?>" <?= $role === 'customer' ? 'aria-current="page"' : '' ?>>Customer</a>
                <a class="<?= $role === 'organizer' ? 'is-active' : '' ?>" href="<?= e(app_url('login', ['role' => 'organizer'])) ?>" <?= $role === 'organizer' ? 'aria-current="page"' : '' ?>>Organizer</a>
                <a class="<?= $role === 'admin' ? 'is-active' : '' ?>" href="<?= e(app_url('login', ['role' => 'admin'])) ?>" <?= $role === 'admin' ? 'aria-current="page"' : '' ?>>Admin</a>
            </nav>

            <?php if ($errors): ?>
                <div class="alert alert-error" role="alert">
                    <strong>Could not sign you in</strong>
                    <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form class="login-form" method="post" action="<?= e(app_url('login', ['role' => $role])) ?>" novalidate>
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input class="form-control<?= $errors ? ' is-invalid' : '' ?>" id="email" name="email" type="email" value="<?= e($emailValue) ?>" required autocomplete="username" inputmode="email" placeholder="you@email.com" <?= $emailValue === '' ? 'autofocus' : '' ?>>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-field">
                        <input class="form-control<?= $errors ? ' is-invalid' : '' ?>" id="password" name="password" type="password" required autocomplete="current-password" placeholder="Enter your password" <?= $emailValue !== '' ? 'autofocus' : '' ?>>
                        <button type="button" class="password-toggle" data-password-toggle="password" aria-controls="password" aria-pressed="false">Show</button>
                    </div>
                </div>
                <button class="btn btn-primary auth-submit" type="submit"><?= e($copy['submit']) ?></button>
            </form>

            <?php if ($role === 'customer'): ?>
                <p class="auth-footer-text">New to NexTik? <a href="<?= e(app_url('register')) ?>">Create a free account</a></p>
            <?php else: ?>
                <p class="auth-footer-text">Organizer and admin accounts are issued by NexTik.</p>
            <?php endif; ?>
        </div>

        <aside class="login-visual" aria-hidden="true">
            <img src="<?= e(asset_url('images/login.png')) ?>" alt="">
            <div class="login-visual-caption">
                <span><?= e($copy['visual_kicker']) ?></span>
                <strong><?= e($copy['visual_title']) ?></strong>
                <ul class="login-visual-points">
                    <?php foreach ($copy['points'] as $point): ?>
                        <li><?= e($point) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>
</section>
<?php require __DIR__.'/includes/footer.php'; ?>
