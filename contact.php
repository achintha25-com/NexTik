<?php

declare(strict_types=1);

require __DIR__.'/includes/bootstrap.php';

$errors = [];

if (is_post()) {
    verify_csrf();

    $name = post_string('name');
    $email = post_string('email');
    $topic = post_string('topic') ?: 'General enquiry';
    $message = post_string('message');

    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = 'Enter your name.';
    }

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if (mb_strlen($topic) > 100) {
        $errors[] = 'Choose a valid enquiry topic.';
    }

    if ($message === '' || mb_strlen($message) > 2000) {
        $errors[] = 'Enter a message of up to 2,000 characters.';
    }

    if ($errors === []) {
        $statement = db()->prepare(
            'INSERT INTO contact_messages (name, email, topic, message)
             VALUES (?, ?, ?, ?)'
        );
        $statement->execute([$name, $email, $topic, $message]);
        flash('success', 'Thank you. Your message has been sent to the NexTik support team.');
        redirect_to('contact');
    }
}

$title = 'Contact NexTik';
$user = current_user();
$flashMessages = consume_flash();

require __DIR__.'/includes/header.php';
?>

<section class="about-hero contact-hero">
    <div class="about-hero-backdrop" aria-hidden="true"></div>
    <div class="container about-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> CONTACT NEXTIK</span>
            <h1>Let's talk about <span>your next event.</span></h1>
        </div>
    </div>
</section>

<section class="section compact-top">
    <div class="container detail-grid contact-layout">
        <div class="form-card">
            <p class="eyebrow">SEND A MESSAGE</p>
            <h2>Tell us how we can help.</h2>
            <p class="muted">Share as much detail as you can so our support team can guide you quickly.</p>

            <?php if ($errors): ?>
                <div class="alert alert-error">
                    <strong>Please correct the following:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(app_url('contact')) ?>">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="name">Name</label>
                    <input class="form-control" id="name" name="name" value="<?= e($_POST['name'] ?? '') ?>" maxlength="100" required>
                </div>

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input class="form-control" id="email" name="email" type="email" value="<?= e($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="topic">What do you need help with?</label>
                    <select class="form-control" id="topic" name="topic">
                        <option value="General enquiry">General enquiry</option>
                        <option value="Booking support">Booking support</option>
                        <option value="Event information">Event information</option>
                        <option value="Organizer support">Organizer support</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea class="form-control" id="message" name="message" maxlength="2000" required><?= e($_POST['message'] ?? '') ?></textarea>
                </div>

                <button class="btn btn-primary" type="submit">Send message</button>
            </form>
        </div>

        <aside class="detail-card contact-info-card">
            <p class="eyebrow">CONTACT INFORMATION</p>
            <h2>Reach NexTik directly.</h2>

            <div class="contact-method-list">
                <div>
                    <small>Email support</small>
                    <a href="mailto:support@nextik.lk">support@nextik.lk</a>
                    <p>For booking, account, and general enquiries.</p>
                </div>
                <div>
                    <small>Phone support</small>
                    <a href="tel:+94112456789">+94 11 245 6789</a>
                    <p>Monday to Friday, 9:00 AM to 6:00 PM.</p>
                </div>
                <div>
                    <small>Office</small>
                    <strong>Colombo 03, Sri Lanka</strong>
                    <p>Meetings are available by appointment.</p>
                </div>
            </div>

            <div class="contact-note">
                <h3>Booking support</h3>
                <p>For a faster response, include your booking reference, the event name, and the email address used for the reservation.</p>
            </div>
        </aside>
    </div>
</section>

<section class="section contact-map-section">
    <div class="container contact-map-card">
        <div class="contact-map-copy">
            <p class="eyebrow">OUR LOCATION</p>
            <h2>Find the NexTik team in Colombo.</h2>
            <p>Our support office is based in Colombo 03, Sri Lanka. Meetings are available by appointment.</p>
            <a class="text-link" href="https://www.google.com/maps/search/?api=1&query=Colombo+03%2C+Sri+Lanka" target="_blank" rel="noopener">Open in Google Maps <span>&rarr;</span></a>
        </div>
        <div class="contact-map-frame">
            <iframe
                src="https://www.google.com/maps?q=Colombo%2003%2C%20Sri%20Lanka&output=embed"
                title="NexTik location in Colombo 03"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<?php require __DIR__.'/includes/footer.php'; ?>
