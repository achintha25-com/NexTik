<?php
$nameParts = preg_split('/\s+/', trim((string) $profile['name'])) ?: [];
$initials = '';

foreach (array_slice($nameParts, 0, 2) as $part) {
    $initials .= mb_strtoupper(mb_substr($part, 0, 1));
}

$initials = $initials !== '' ? $initials : 'NT';

$dashboardRoute = match ($profile['role']) {
    'admin' => 'admin-dashboard',
    'organizer' => 'organizer-dashboard',
    default => 'customer-dashboard',
};
?>

<section class="about-hero profile-hero">
    <div class="about-hero-backdrop" aria-hidden="true"></div>
    <div class="container about-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> ACCOUNT SETTINGS</span>
            <h1>Your NexTik profile, <span>all in one place.</span></h1>
        </div>
    </div>
</section>

<section class="profile-workspace">
    <div class="container profile-workspace-grid">
        <aside class="profile-summary-card">
            <div class="profile-avatar" aria-hidden="true"><?= e($initials) ?></div>
            <h2><?= e($profile['name']) ?></h2>
            <span class="profile-role-badge"><?= e(ucfirst($profile['role'])) ?> account</span>

            <div class="profile-summary-list">
                <div>
                    <small>Email address</small>
                    <strong><?= e($profile['email']) ?></strong>
                </div>
                <div>
                    <small>Phone number</small>
                    <strong><?= e($profile['phone'] ?: 'Not provided') ?></strong>
                </div>
                <div>
                    <small>Account status</small>
                    <strong class="profile-status"><i></i> Active</strong>
                </div>
            </div>

            <div class="profile-quick-links">
                <a class="btn btn-primary full-width" href="<?= e(app_url($dashboardRoute)) ?>">Open dashboard</a>
                <?php if ($profile['role'] === 'customer'): ?>
                    <a class="btn btn-secondary full-width" href="<?= e(app_url('bookings')) ?>">View my bookings</a>
                <?php endif; ?>
            </div>
        </aside>

        <div class="profile-settings">
            <?php require __DIR__.'/partials/errors.php'; ?>

            <section class="form-card profile-settings-card">
                <div class="profile-card-heading">
                    <span>01</span>
                    <div>
                        <h2>Personal information</h2>
                        <p>Keep your contact details accurate and up to date.</p>
                    </div>
                </div>

                <form method="post" action="<?= e(app_url('profile')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="details">

                    <div class="profile-form-grid">
                        <div class="form-group">
                            <label for="name">Full name</label>
                            <input class="form-control" id="name" name="name" value="<?= e($profile['name']) ?>" maxlength="255" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input class="form-control" id="email" name="email" type="email" value="<?= e($profile['email']) ?>" maxlength="255" required>
                        </div>
                        <div class="form-group profile-form-wide">
                            <label for="phone">Phone number</label>
                            <input class="form-control" id="phone" name="phone" value="<?= e($profile['phone'] ?? '') ?>" maxlength="20" placeholder="Add a contact number">
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit">Save profile</button>
                </form>
            </section>

            <section class="form-card profile-settings-card">
                <div class="profile-card-heading">
                    <span>02</span>
                    <div>
                        <h2>Account security</h2>
                        <p>Use a strong password that you do not use elsewhere.</p>
                    </div>
                </div>

                <form method="post" action="<?= e(app_url('profile')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="password">

                    <div class="form-group">
                        <label for="current_password">Current password</label>
                        <div class="password-field">
                            <input class="form-control" id="current_password" name="current_password" type="password" required autocomplete="current-password">
                            <button type="button" class="password-toggle" data-password-toggle="current_password">Show</button>
                        </div>
                    </div>
                    <div class="profile-form-grid">
                        <div class="form-group">
                            <label for="new_password">New password</label>
                            <div class="password-field">
                                <input class="form-control" id="new_password" name="password" type="password" minlength="8" required autocomplete="new-password">
                                <button type="button" class="password-toggle" data-password-toggle="new_password">Show</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm new password</label>
                            <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password">
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit">Update password</button>
                </form>
            </section>

            <?php if ($profile['role'] === 'customer'): ?>
                <section class="form-card profile-settings-card profile-danger-zone">
                    <div class="profile-card-heading">
                        <span>!</span>
                        <div>
                            <h2>Delete account</h2>
                            <p>This permanently removes your account and booking history.</p>
                        </div>
                    </div>

                    <form method="post" action="<?= e(app_url('profile')) ?>" data-confirm="Delete your account permanently?">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <div class="form-group">
                            <label for="delete_password">Confirm with your password</label>
                            <input class="form-control" id="delete_password" name="password" type="password" required>
                        </div>
                        <button class="btn btn-danger" type="submit">Delete account</button>
                    </form>
                </section>
            <?php endif; ?>
        </div>
    </div>
</section>
