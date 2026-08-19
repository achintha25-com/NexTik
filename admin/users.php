<?php
declare(strict_types=1);
require dirname(__DIR__).'/includes/bootstrap.php';
$user = require_role('admin');
if (is_post()) {
    verify_csrf();
    $action = post_string('action');
    $accountId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: 0;

    if ($action === 'save') {
        flash('success', 'Account record saved.');
        redirect_to('admin-users');
    }

    if ($action === 'delete') {
        if ($accountId === (int)$user['id']) flash('error','You cannot delete your own account.');
        else {
            $check = db()->prepare('SELECT (SELECT COUNT(*) FROM events WHERE organizer_id = ?) + (SELECT COUNT(*) FROM bookings WHERE user_id = ?)');
            $check->execute([$accountId,$accountId]);
            if ($check->fetchColumn()) flash('error','Accounts connected to events or bookings cannot be deleted.');
            else { db()->prepare('DELETE FROM users WHERE id = ?')->execute([$accountId]); flash('success','Account deleted.'); }
        }
        redirect_to('admin-users');
    }
}

$roleFilter = query_string('role');
$where = in_array($roleFilter,['customer','organizer','admin'],true) ? 'WHERE u.role = ?' : '';
$statement = db()->prepare("SELECT u.*,COUNT(DISTINCT e.id) event_count,COUNT(DISTINCT b.id) booking_count FROM users u LEFT JOIN events e ON e.organizer_id=u.id LEFT JOIN bookings b ON b.user_id=u.id $where GROUP BY u.id ORDER BY u.created_at DESC");
$statement->execute($where ? [$roleFilter] : []);
$accounts = $statement->fetchAll();
$pageNames = ['customer'=>'Customers','organizer'=>'Organizers','admin'=>'Administrators'];
$accountPageTitle = $pageNames[$roleFilter] ?? 'All accounts';
$title = $accountPageTitle;
$bodyClass = 'admin-page admin-workspace';
$flashMessages = consume_flash();
require dirname(__DIR__).'/includes/header.php';
?>
<section class="plain-page-head"><div class="container plain-page-head-inner"><div><span class="plain-page-label">Account management</span><h1><?= e($accountPageTitle) ?></h1><p>Review account activity and manage existing records.</p></div></div></section>
<section class="admin-page-content">
<div class="admin-bookings-panel"><div class="admin-table-wrap"><table><thead><tr><th>Account</th><th>Role</th><th>Activity</th><th>Joined</th><th>Actions</th></tr></thead><tbody><?php foreach($accounts as $account): ?><tr><td><strong><?= e($account['name']) ?></strong><small class="table-subtitle"><?= e($account['email']) ?><?= $account['phone']?' · '.e($account['phone']):'' ?></small></td><td><span class="status-badge"><?= e(ucfirst($account['role'])) ?></span></td><td><?= (int)$account['event_count'] ?> events · <?= (int)$account['booking_count'] ?> bookings</td><td><?= e(date('M j, Y',strtotime($account['created_at']))) ?></td><td><div class="table-actions"><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)$account['id'] ?>"><button class="btn btn-secondary btn-sm" type="submit">Save</button></form><form method="post" data-confirm="Delete this account?"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$account['id'] ?>"><button class="icon-btn icon-btn-danger" type="submit" aria-label="Delete account" <?= (int)$account['id']===(int)$user['id']?'disabled title="You cannot delete your own account"':'' ?>><?= icon('delete') ?></button></form></div></td></tr><?php endforeach; ?></tbody></table></div></div>
</section>
<?php require dirname(__DIR__).'/includes/footer.php'; ?>
