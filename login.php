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
        $errors[] = 'The credentials do not match a '.$role.' account.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $account['id'];
        $destination = $_SESSION['intended_url'] ?? null;
        unset($_SESSION['intended_url']);
        if (is_string($destination) && str_starts_with($destination, '/')) { header('Location: '.$destination); exit; }
        redirect_to('dashboard');
    }
}
$title = ucfirst($role).' login';
$bodyClass = 'login-page';
$user = current_user();
$flashMessages = consume_flash();
require __DIR__.'/includes/header.php';
?>

<section class="about-hero login-hero">
    <div class="about-hero-backdrop" aria-hidden="true"></div>
    <div class="container about-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> NEX TIK LOGIN</span>
            <h1>Welcome back <span>to NexTik.</span></h1>
        </div>
    </div>
</section>

<section class="login-content">
    <div class="container login-content-grid">
        <div class="login-form-column">
            <section class="auth-wrapper"><div class="auth-card">
    <p class="auth-role-label"><?= e(strtoupper($role)) ?> ACCOUNT</p><h1>Sign in to continue</h1><p>Choose your workspace and enter your account details.</p>
    <div class="login-role-switcher" aria-label="Choose account type">
        <a class="<?= $role === 'customer' ? 'active' : '' ?>" href="<?= e(app_url('login', ['role' => 'customer'])) ?>">Customer</a>
        <a class="<?= $role === 'organizer' ? 'active' : '' ?>" href="<?= e(app_url('login', ['role' => 'organizer'])) ?>">Organizer</a>
        <a class="<?= $role === 'admin' ? 'active' : '' ?>" href="<?= e(app_url('login', ['role' => 'admin'])) ?>">Administrator</a>
    </div>
    <?php if ($errors): ?><div class="alert alert-error"><strong>Please correct the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" action="<?= e(app_url('login', ['role' => $role])) ?>">
        <?= csrf_field() ?>
        <div class="form-group"><label for="email">Email address</label><input class="form-control" id="email" name="email" type="email" value="<?= e($_POST['email'] ?? '') ?>" required autocomplete="email"></div>
        <div class="form-group"><label for="password">Password</label><div class="password-field"><input class="form-control" id="password" name="password" type="password" required autocomplete="current-password"><button type="button" class="password-toggle" data-password-toggle="password">Show</button></div></div>
        <button class="btn btn-primary auth-submit" type="submit">Sign in</button>
    </form>
<?php if ($role === 'customer'): ?><p class="auth-footer-text">New to NexTik? <a href="<?= e(app_url('register')) ?>">Create an account</a></p><?php endif; ?>
    <?php if ($role !== 'customer'): ?><p class="auth-footer-text">Organizer and administrator accounts are issued by the system administrator.</p><?php endif; ?>
            </div></section>
        </div>
        <div class="login-visual">
            <img src="<?= e(asset_url('images/login.png')) ?>" alt="A selection of NexTik event experiences">
            <div class="login-visual-caption">
                <span>YOUR NEXT EXPERIENCE</span>
                <strong>starts here.</strong>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__.'/includes/footer.php'; ?>
