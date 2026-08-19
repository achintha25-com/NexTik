<?php

declare(strict_types=1);

require dirname(__DIR__).'/includes/bootstrap.php';

$user = require_role('admin');

if (is_post()) {
    verify_csrf();

    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
    $reply = post_string('reply_text');
    $messageQuery = db()->prepare('SELECT id, name, email, topic FROM contact_messages WHERE id = ?');
    $messageQuery->execute([$id]);
    $message = $messageQuery->fetch();

    if (! $message) {
        flash('error', 'That contact message could not be found.');
    } elseif ($reply === '' || mb_strlen($reply) > 5000) {
        flash('error', 'Enter a reply of up to 5,000 characters.');
    } else {
        db()->prepare(
            "UPDATE contact_messages
             SET reply_text = ?, status = 'replied', replied_by = ?, replied_at = NOW()
             WHERE id = ?"
        )->execute([$reply, $user['id'], $id]);

        flash('success', 'Reply saved. Use the email button on the message to send it to the customer.');
    }

    redirect_to('admin-messages');
}

$messages = db()->query(
    "SELECT m.*, u.name AS replied_by_name
     FROM contact_messages m
     LEFT JOIN users u ON u.id = m.replied_by
     ORDER BY (m.status = 'open') DESC, m.created_at DESC"
)->fetchAll();

$title = 'Contact messages';
$bodyClass = 'admin-messages-page admin-workspace';
$flashMessages = consume_flash();

require dirname(__DIR__).'/includes/header.php';
?>

<section class="plain-page-head">
    <div class="container plain-page-head-inner">
      <div>
        <span class="plain-page-label">Support inbox</span>
        <h1>Contact messages</h1>
        <p>Read customer enquiries and save replies.</p>
      </div>
    </div>
</section>

<section class="section compact-top">
    <div class="container">
        <div class="admin-messages-heading">
            <div>
                <p class="eyebrow">MESSAGES</p>
                <h2>Customer enquiries</h2>
            </div>
            <span class="messages-count"><?= count($messages) ?> <?= count($messages) === 1 ? 'message' : 'messages' ?></span>
        </div>

        <?php if (! $messages): ?>
            <div class="empty-state-card"><h3>Your inbox is clear.</h3><p>New messages from the Contact page will appear here.</p></div>
        <?php else: ?>
            <div class="message-list">
                <?php foreach ($messages as $message): ?>
                    <article class="message-card status-<?= e($message['status']) ?>">
                        <div class="message-card-heading">
                            <div>
                                <span class="message-topic"><?= e($message['topic']) ?></span>
                                <h3><?= e($message['name']) ?></h3>
                                <p class="message-meta"><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a> · <?= e(date('M j, Y · g:i A', strtotime($message['created_at']))) ?></p>
                            </div>
                            <span class="status-badge status-<?= e($message['status']) ?>"><?= e(ucfirst($message['status'])) ?></span>
                        </div>

                        <div class="message-body"><?= nl2br(e($message['message'])) ?></div>

                        <?php if (! empty($message['reply_text'])): ?>
                            <div class="message-reply">
                                <small>Reply saved<?= $message['replied_by_name'] ? ' by '.e($message['replied_by_name']) : '' ?><?= $message['replied_at'] ? ' · '.e(date('M j, Y · g:i A', strtotime($message['replied_at']))) : '' ?></small>
                                <p><?= nl2br(e($message['reply_text'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <form class="message-reply-form" method="post" action="<?= e(app_url('admin-messages')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e($message['id']) ?>">
                            <label for="reply-<?= e($message['id']) ?>"><?= ! empty($message['reply_text']) ? 'Update your reply' : 'Write a reply' ?></label>
                            <textarea id="reply-<?= e($message['id']) ?>" name="reply_text" maxlength="5000" required><?= e($message['reply_text'] ?? '') ?></textarea>
                            <div class="message-actions">
                                <button class="btn btn-primary btn-sm" type="submit">Save reply</button>
                                <a class="btn btn-secondary btn-sm" href="mailto:<?= e($message['email']) ?>?subject=<?= rawurlencode('Re: '.$message['topic'].' - NexTik') ?>&body=<?= rawurlencode($message['reply_text'] ?? '') ?>">Email customer</a>
                            </div>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require dirname(__DIR__).'/includes/footer.php'; ?>
