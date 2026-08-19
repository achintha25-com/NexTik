<?php

require __DIR__ . '/includes/bootstrap.php';

if (current_user())
    redirect_to('dashboard');
$errors = [];
if (is_post()) {
    verify_csrf();
    $name = post_string('name');
    $email = strtolower(post_string('email'));
    $phone = post_string('phone');
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');
    if ($name === '' || mb_strlen($name) > 255)
        $errors[] = 'Enter a valid name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255)
        $errors[] = 'Enter a valid email address.';
    if ($phone !== '' && mb_strlen($phone) > 20)
        $errors[] = 'Enter a valid phone number.';
    if (strlen($password) < 8)
        $errors[] = 'The password must contain at least 8 characters.';
    if ($password !== $confirmation)
        $errors[] = 'The password confirmation does not match.';
    $check = db()->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetchColumn())
        $errors[] = 'That email address is already registered.';
    if ($errors === []) {
        $statement = db()->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
        $statement->execute([$name, $email, $phone ?: null, password_hash($password, PASSWORD_DEFAULT)]);
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) db()->lastInsertId();
        flash('success', 'Your customer account was created successfully.');
        redirect_to('home');
    }
}
$title = 'Create customer account';
$bodyClass = 'register-page';
$user = current_user();
$flashMessages = consume_flash();
require __DIR__ . '/includes/header.php';
?>

<section class="about-hero register-hero">
    <div class="about-hero-backdrop" aria-hidden="true"></div>
    <div class="container about-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> JOIN NEXTIK</span>
            <h1>Start planning <span>something memorable.</span></h1>
        </div>
    </div>
</section>

<section class="login-content register-content">
    <div class="container login-content-grid">
        <div class="login-form-column">
            <section class="auth-wrapper">
                <div class="auth-card">
                    <p class="auth-role-label">CUSTOMER ACCOUNT</p>
                    <h1>Create your account</h1>
                    <p>Join NexTik and reserve your next experience.</p>
                    <?php if ($errors): ?>
                        <div class="alert alert-error"><strong>Please correct the following:</strong>
                            <ul><?php foreach ($errors as $error): ?>
                                    <li><?= e($error) ?></li><?php endforeach; ?>
                            </ul>
                        </div><?php endif; ?>
                    <form method="post" action="<?= e(app_url('register')) ?>">
                        <?= csrf_field() ?>
                        <div class="form-group"><label for="name">Full name</label><input class="form-control" id="name"
                                name="name" value="<?= e($_POST['name'] ?? '') ?>" required maxlength="255"></div>
                        <div class="form-group"><label for="email">Email address</label><input class="form-control"
                                id="email" name="email" type="email" value="<?= e($_POST['email'] ?? '') ?>" required>
                        </div>
                        <div class="form-group"><label for="phone">Phone number</label><input class="form-control"
                                id="phone" name="phone" value="<?= e($_POST['phone'] ?? '') ?>" maxlength="20"></div>
                        <div class="form-group"><label for="password">Password</label>
                            <div class="password-field"><input class="form-control" id="password" name="password"
                                    type="password" minlength="8" required><button type="button" class="password-toggle"
                                    data-password-toggle="password">Show</button></div>
                        </div>
                        <div class="form-group"><label for="password_confirmation">Confirm password</label><input
                                class="form-control" id="password_confirmation" name="password_confirmation"
                                type="password" minlength="8" required></div>
                        <button class="btn btn-primary auth-submit" type="submit">Create account</button>
                    </form>
                    <p class="auth-footer-text">Already registered? <a
                            href="<?= e(app_url('login', ['role' => 'customer'])) ?>">Sign in</a></p>
                </div>
            </section>
        </div>
        <div class="login-visual register-visual">
            <img src="<?= e(asset_url('images/register.png')) ?>" alt="Guests arriving at a NexTik event">
            <div class="login-visual-caption">
                <span>MAKE ROOM FOR SOMETHING NEW</span>
                <strong>your next plan.</strong>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>