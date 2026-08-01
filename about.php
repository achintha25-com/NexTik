<?php

declare(strict_types=1);

require __DIR__.'/includes/bootstrap.php';

$title = 'About NexTik';
$bodyClass = 'about-page';
$user = current_user();
$flashMessages = consume_flash();

require __DIR__.'/includes/header.php';
?>

<section class="about-hero">
    <div class="about-hero-backdrop" aria-hidden="true"></div>
    <div class="container about-hero-layout">
        <div class="original-copy">
            <span class="original-kicker"><i></i> THE NEXTIK EXPERIENCE</span>
            <h1>Make every plan <span>worth remembering.</span></h1>
        </div>
    </div>
</section>

<section class="section about-section">
    <div class="container">
        <div class="section-heading about-section-heading">
            <div>
                <p class="eyebrow">WHAT NEXTIK DOES</p>
                <h2 class="section-title">A clearer way to move from discovery to attendance.</h2>
            </div>
            <p>Useful tools for every step of the event journey.</p>
        </div>

        <div class="about-feature-grid">
            <article class="about-feature-card">
                <span class="about-feature-number">01</span>
                <h3>Discover with confidence</h3>
                <p>Browse upcoming events by category, city, date, and search term, then see the details that matter before booking.</p>
            </article>
            <article class="about-feature-card">
                <span class="about-feature-number">02</span>
                <h3>Reserve without confusion</h3>
                <p>Choose your tickets, review the total, and receive a clear booking reference that is easy to keep and find later.</p>
            </article>
            <article class="about-feature-card">
                <span class="about-feature-number">03</span>
                <h3>Manage events smoothly</h3>
                <p>Organizers can publish events and monitor reservations while administrators keep the catalogue accurate and dependable.</p>
            </article>
        </div>
    </div>
</section>

<section class="section about-section about-journey-section">
    <div class="container about-journey-grid">
        <div class="about-journey-intro">
            <p class="eyebrow">HOW IT WORKS</p>
            <h2>From a quick search to a confirmed plan.</h2>
            <p>
                NexTik keeps the process focused so customers can spend less time searching through
                scattered information and more time looking forward to the experience.
            </p>
        </div>

        <ol class="about-steps">
            <li>
                <span>01</span>
                <div><h3>Find your event</h3><p>Search the catalogue and open a complete event overview.</p></div>
            </li>
            <li>
                <span>02</span>
                <div><h3>Choose your tickets</h3><p>Check the date, venue, price, and available ticket quantity.</p></div>
            </li>
            <li>
                <span>03</span>
                <div><h3>Keep your confirmation</h3><p>Access your booking reference and reservation history from your account.</p></div>
            </li>
        </ol>
    </div>
</section>

<section class="section about-section">
    <div class="container">
        <div class="detail-card about-commitment-card">
            <div>
                <p class="eyebrow">OUR COMMITMENT</p>
                <h2>Built for clarity, reliability, and local experiences.</h2>
            </div>
            <div class="about-commitment-actions">
                <p>Find your next experience or contact NexTik to learn more.</p>
                <div class="actions-row">
                    <a class="btn btn-primary" href="<?= e(app_url()) ?>">Browse events</a>
                    <a class="btn btn-outline" href="<?= e(app_url('contact')) ?>">Contact us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__.'/includes/footer.php'; ?>
