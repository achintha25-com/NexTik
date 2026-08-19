<?php

require __DIR__ . '/includes/bootstrap.php';

$user = require_login();
$errors = [];
if (is_post()) {
    verify_csrf();
    $action = post_string('action');
    if ($action === 'details') {
        $name = post_string('name');
        $email = strtolower(post_string('email'));
        $phone = post_string('phone');
        if ($name === '' || mb_strlen($name) > 255)
            $errors[] = 'Enter a valid name.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = 'Enter a valid email address.';
        $check = db()->prepare('SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?');
        $check->execute([$email, $user['id']]);
        if ($check->fetchColumn())
            $errors[] = 'That email address is already registered.';
        if ($errors === []) {
            db()->prepare('UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?')->execute([$name, $email, $phone ?: null, $user['id']]);
            flash('success', 'Profile updated successfully.');
            redirect_to('profile');
        }
    } elseif ($action === 'password') {
        $statement = db()->prepare('SELECT password FROM users WHERE id = ?');
        $statement->execute([$user['id']]);
        $currentHash = (string) $statement->fetchColumn();
        $password = (string) ($_POST['password'] ?? '');
        if (!password_verify((string) ($_POST['current_password'] ?? ''), $currentHash))
            $errors[] = 'The current password is incorrect.';
        if (strlen($password) < 8)
            $errors[] = 'The new password must contain at least 8 characters.';
        if ($password !== (string) ($_POST['password_confirmation'] ?? ''))
            $errors[] = 'The password confirmation does not match.';
        if ($errors === []) {
            db()->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
            flash('success', 'Password updated successfully.');
            redirect_to('profile');
        }
    } elseif ($action === 'delete' && $user['role'] === 'customer') {
        $statement = db()->prepare('SELECT password FROM users WHERE id = ?');
        $statement->execute([$user['id']]);
        if (!password_verify((string) ($_POST['password'] ?? ''), (string) $statement->fetchColumn())) {
            $errors[] = 'The password is incorrect.';
        } else {
            db()->prepare('DELETE FROM users WHERE id = ?')->execute([$user['id']]);
            $_SESSION = [];
            session_regenerate_id(true);
            flash('success', 'Your account was deleted.');
            redirect_to('home');
        }
    }
}
$statement = db()->prepare('SELECT id, name, email, phone, role FROM users WHERE id = ?');
$statement->execute([$user['id']]);
render('profile', [
    'title' => 'Profile',
    'bodyClass' => 'profile-page' . ($user['role'] === 'admin' ? ' admin-workspace' : ($user['role'] === 'organizer' ? ' organizer-workspace' : '')),
    'profile' => $statement->fetch(),
    'errors' => $errors,
]);
