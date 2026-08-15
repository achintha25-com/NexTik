<?php

declare(strict_types=1);

$config = require dirname(__DIR__).'/database/connection.php';
date_default_timezone_set($config['timezone']);

if (session_status() !== PHP_SESSION_ACTIVE) {
    $sessionPath = dirname(__DIR__).'/database/sessions';
    if (! is_dir($sessionPath)) {
        mkdir($sessionPath, 0777, true);
    }
    // Apache on XAMPP runs as daemon; 0700 dirs owned by the Mac user cannot store sessions.
    @chmod($sessionPath, 0777);
    if (is_dir($sessionPath) && is_writable($sessionPath)) {
        session_save_path($sessionPath);
    }
    session_name($config['session_name']);
    session_set_cookie_params([
        'httponly' => true,
        'secure' => ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function db(): PDO
{
    static $pdo;
    global $config;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $database = $config['database'];
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $database['host'],
        $database['port'],
        $database['name'],
        $database['charset']
    );

    try {
        $pdo = new PDO($dsn, $database['user'], $database['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        http_response_code(500);
$message = 'Database connection failed. Import database/nextik.sql and check database/connection.php.';
        if (PHP_SAPI === 'cli') {
            throw new RuntimeException($message, 0, $exception);
        }
        exit('<!doctype html><title>NexTik setup</title><h1>NexTik is not configured</h1><p>'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</p>');
    }

    return $pdo;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function icon(string $name): string
{
    $icons = [
        'view' => '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 12s3.6-7 9.5-7 9.5 7 9.5 7-3.6 7-9.5 7-9.5-7-9.5-7Z"/><circle cx="12" cy="12" r="3"/></svg>',
        'edit' => '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17v3Z"/><path d="m13.5 6.5 3 3"/></svg>',
        'ticket' => '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4V8Z"/><path d="M12 7.5v9"/></svg>',
        'delete' => '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7h14"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>',
    ];

    return $icons[$name] ?? '';
}

function web_root(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $directory = str_replace('\\', '/', dirname($script));
    if (in_array(basename($directory), ['admin', 'customer', 'organizer'], true)) {
        $directory = str_replace('\\', '/', dirname($directory));
    }

    return rtrim($directory, '/.');
}

function app_url(string $page = 'home', array $parameters = []): string
{
    $routes = [
        'home' => 'index.php',
        'dashboard' => 'dashboard.php',
        'search' => 'search.php',
        'event' => 'events.php',
        'accounts' => 'login.php',
        'about' => 'about.php',
        'contact' => 'contact.php',
        'login' => 'login.php',
        'register' => 'register.php',
        'logout' => 'logout.php',
        'profile' => 'profile.php',
        'bookings' => 'customer/bookings.php',
        'customer-dashboard' => 'customer/dashboard.php',
        'booking' => 'customer/booking.php',
        'book' => 'customer/booking.php',
        'admin-dashboard' => 'admin/dashboard.php',
        'admin-events' => 'admin/events.php',
        'admin-event-form' => 'admin/event_form.php',
        'admin-event-delete' => 'admin/events.php',
        'admin-categories' => 'admin/categories.php',
        'admin-messages' => 'admin/messages.php',
        'organizer-dashboard' => 'organizer/dashboard.php',
        'organizer-events' => 'organizer/events.php',
        'organizer-event-form' => 'organizer/event_form.php',
        'organizer-event-bookings' => 'organizer/bookings.php',
    ];
    $path = $routes[$page] ?? 'index.php';
    $query = http_build_query(['page' => $page] + $parameters);

    return web_root().'/'.$path.'?'.$query;
}

function asset_url(string $path): string
{
    $relative = ltrim($path, '/');
    $full = dirname(__DIR__).'/'.$relative;
    $version = is_file($full) ? (string) filemtime($full) : (string) time();

    return web_root().'/'.$relative.'?v='.$version;
}

function redirect_to(string $page, array $parameters = []): never
{
    header('Location: '.app_url($page, $parameters));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][$type][] = $message;
}

function consume_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $messages;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'.e(csrf_token()).'">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (! is_string($token) || ! hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Your session expired. Go back, refresh the page, and try again.');
    }
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function current_user(): ?array
{
    static $loaded = false;
    static $user;

    if ($loaded) {
        return $user;
    }

    $loaded = true;
    $id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
    if (! $id) {
        return null;
    }

    $statement = db()->prepare('SELECT id, name, email, phone, role FROM users WHERE id = ?');
    $statement->execute([$id]);
    $user = $statement->fetch() ?: null;

    if ($user === null) {
        unset($_SESSION['user_id']);
    }

    return $user;
}

function require_login(): array
{
    $user = current_user();
    if ($user === null) {
        $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? app_url();
        flash('error', 'Please sign in to continue.');
        redirect_to('login', ['role' => 'customer']);
    }

    return $user;
}

function require_role(string ...$roles): array
{
    $user = require_login();
    if (! in_array($user['role'], $roles, true)) {
        http_response_code(403);
        render('error', ['title' => 'Access denied', 'message' => 'You do not have permission to access this page.']);
        exit;
    }

    return $user;
}

function post_string(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

/**
 * Validate and store an optional event poster uploaded by an organizer/admin.
 * The database stores a path relative to the images directory.
 */
function process_event_image(?string $currentImage = null): array
{
    if (! isset($_FILES['image']) || ! is_array($_FILES['image'])) {
        return [$currentImage, []];
    }

    $file = $_FILES['image'];
    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error === UPLOAD_ERR_NO_FILE) {
        return [$currentImage, []];
    }
    if ($error !== UPLOAD_ERR_OK) {
        return [$currentImage, ['The event poster could not be uploaded. Try again.']];
    }

    $temporaryPath = (string) ($file['tmp_name'] ?? '');
    $size = (int) ($file['size'] ?? 0);
    if ($size < 1 || $size > 5 * 1024 * 1024) {
        return [$currentImage, ['Choose an event poster smaller than 5 MB.']];
    }

    $imageInfo = @getimagesize($temporaryPath);
    $allowedTypes = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];
    $imageType = (int) ($imageInfo[2] ?? 0);
    if (! $imageInfo || ! isset($allowedTypes[$imageType])) {
        return [$currentImage, ['Upload a JPG, PNG, or WEBP event poster.']];
    }

    $directory = dirname(__DIR__).'/images/events';
    if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
        return [$currentImage, ['The event poster folder could not be created.']];
    }

    $filename = 'event-'.bin2hex(random_bytes(12)).'.'.$allowedTypes[$imageType];
    if (! move_uploaded_file($temporaryPath, $directory.'/'.$filename)) {
        return [$currentImage, ['The event poster could not be saved. Try again.']];
    }

    return ['events/'.$filename, []];
}

function query_string(string $key): string
{
    return trim((string) ($_GET[$key] ?? ''));
}

function valid_role(string $role): bool
{
    return in_array($role, ['customer', 'organizer', 'admin'], true);
}

function event_by_id(int $id, bool $publishedOnly = false): ?array
{
    $sql = 'SELECT e.*, c.name AS category_name, c.slug AS category_slug, u.name AS organizer_name
            FROM events e
            JOIN categories c ON c.id = e.category_id
            JOIN users u ON u.id = e.organizer_id
            WHERE e.id = ?';
    if ($publishedOnly) {
        $sql .= " AND e.status = 'published'";
    }
    $statement = db()->prepare($sql);
    $statement->execute([$id]);

    return $statement->fetch() ?: null;
}

function booking_by_id(int $id): ?array
{
    $statement = db()->prepare(
        'SELECT b.*, e.title AS event_title, e.event_date, e.start_time, e.end_time, e.venue, e.city, e.image,
                c.name AS category_name, c.slug AS category_slug,
                u.name AS customer_name, u.email AS customer_email
         FROM bookings b
         JOIN events e ON e.id = b.event_id
         JOIN categories c ON c.id = e.category_id
         JOIN users u ON u.id = b.user_id
         WHERE b.id = ?'
    );
    $statement->execute([$id]);

    return $statement->fetch() ?: null;
}

function event_poster(array $event): string
{
    if (! empty($event['image'])) {
        return 'images/'.ltrim((string) $event['image'], '/');
    }

    return match ((string) ($event['category_slug'] ?? '')) {
        'concerts' => 'images/concert-card.png',
        'edm' => 'images/edm-card.png',
        default => 'images/hero-nextik.png',
    };
}

function event_countdown(string $date): string
{
    $days = (int) round((strtotime($date) - strtotime(date('Y-m-d'))) / 86400);

    return match (true) {
        $days === 0 => 'Today',
        $days === 1 => 'Tomorrow',
        $days === -1 => 'Yesterday',
        $days > 1 => 'In '.$days.' days',
        default => abs($days).' days ago',
    };
}

function customer_bookings(int $userId): array
{
    $statement = db()->prepare(
        'SELECT b.*, e.title AS event_title, e.event_date, e.start_time, e.venue, e.city, e.image,
                c.name AS category_name, c.slug AS category_slug
         FROM bookings b
         JOIN events e ON e.id = b.event_id
         JOIN categories c ON c.id = e.category_id
         WHERE b.user_id = ?
         ORDER BY e.event_date ASC, e.start_time ASC, b.created_at DESC'
    );
    $statement->execute([$userId]);

    return $statement->fetchAll();
}

function payment_reference(): string
{
    do {
        $reference = 'NT-PAY-'.strtoupper(bin2hex(random_bytes(4)));
        $check = db()->prepare('SELECT COUNT(*) FROM bookings WHERE payment_reference = ?');
        $check->execute([$reference]);
    } while ($check->fetchColumn());

    return $reference;
}

function payment_validation(array $input): array
{
    $cardNumber = preg_replace('/\D+/', '', (string) ($input['card_number'] ?? ''));
    $cardName = trim((string) ($input['card_name'] ?? ''));
    $expiry = trim((string) ($input['card_expiry'] ?? ''));
    $cvv = preg_replace('/\D+/', '', (string) ($input['card_cvv'] ?? ''));
    $errors = [];

    if ($cardName === '' || mb_strlen($cardName) > 100) {
        $errors[] = 'Enter the name on the card.';
    }

    if (! preg_match('/^\d{16}$/', $cardNumber)) {
        $errors[] = 'Enter a valid 16-digit card number.';
    }

    if (! preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiry, $matches)) {
        $errors[] = 'Enter expiry as MM/YY.';
    } else {
        $month = (int) $matches[1];
        $year = 2000 + (int) $matches[2];
        $expires = strtotime(sprintf('%04d-%02d-01 +1 month -1 day', $year, $month));
        if ($expires < strtotime('today')) {
            $errors[] = 'This card has expired.';
        }
    }

    if (! preg_match('/^\d{3,4}$/', $cvv)) {
        $errors[] = 'Enter a valid CVV.';
    }

    return [$errors, [
        'card_name' => $cardName,
        'card_last4' => substr($cardNumber, -4),
    ]];
}

function set_pending_booking(array $data): void
{
    $_SESSION['pending_booking'] = $data + ['expires_at' => time() + 900];
}

function pending_booking(): ?array
{
    $pending = $_SESSION['pending_booking'] ?? null;
    if (! is_array($pending)) {
        return null;
    }

    if ((int) ($pending['expires_at'] ?? 0) < time()) {
        unset($_SESSION['pending_booking']);

        return null;
    }

    return $pending;
}

function clear_pending_booking(): void
{
    unset($_SESSION['pending_booking']);
}

function complete_booking(int $userId, int $eventId, int $ticketOptionId, int $quantity, string $paymentReference): int
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $statement = $pdo->prepare('SELECT id, event_date, status FROM events WHERE id = ? FOR UPDATE');
        $statement->execute([$eventId]);
        $lockedEvent = $statement->fetch();
        if (! $lockedEvent || $lockedEvent['status'] !== 'published' || $lockedEvent['event_date'] < date('Y-m-d')) {
            throw new RuntimeException('This event is no longer available.');
        }

        $statement = $pdo->prepare('SELECT id, name, price, available_tickets FROM ticket_options WHERE id = ? AND event_id = ? FOR UPDATE');
        $statement->execute([$ticketOptionId, $eventId]);
        $option = $statement->fetch();
        if (! $option) {
            throw new RuntimeException('Choose a valid ticket option.');
        }
        if ((int) $option['available_tickets'] < $quantity) {
            throw new RuntimeException('Not enough tickets are available for '.$option['name'].'.');
        }

        do {
            $reference = 'NT-'.strtoupper(bin2hex(random_bytes(4)));
            $check = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE booking_reference = ?');
            $check->execute([$reference]);
        } while ($check->fetchColumn());

        $total = (float) $option['price'] * $quantity;
        $statement = $pdo->prepare(
            "INSERT INTO bookings (user_id, event_id, ticket_option_id, ticket_option_name, booking_reference, quantity, unit_price, total_amount,
             payment_method, payment_reference, payment_status, paid_at, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'card', ?, 'paid', NOW(), 'confirmed')"
        );
        $statement->execute([
            $userId,
            $eventId,
            $option['id'],
            $option['name'],
            $reference,
            $quantity,
            $option['price'],
            $total,
            $paymentReference,
        ]);
        $bookingId = (int) $pdo->lastInsertId();
        $pdo->prepare('UPDATE ticket_options SET available_tickets = available_tickets - ? WHERE id = ?')->execute([$quantity, $option['id']]);
        sync_event_ticket_totals($eventId);
        $pdo->commit();

        return $bookingId;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }
}

function event_validation(array $input, bool $admin = false): array
{
    $data = [
        'organizer_id' => (int) ($input['organizer_id'] ?? 0),
        'category_id' => (int) ($input['category_id'] ?? 0),
        'title' => trim((string) ($input['title'] ?? '')),
        'description' => trim((string) ($input['description'] ?? '')),
        'venue' => trim((string) ($input['venue'] ?? '')),
        'city' => trim((string) ($input['city'] ?? '')),
        'event_date' => trim((string) ($input['event_date'] ?? '')),
        'start_time' => trim((string) ($input['start_time'] ?? '')),
        'end_time' => trim((string) ($input['end_time'] ?? '')),
        'is_featured' => isset($input['is_featured']) ? 1 : 0,
        'status' => trim((string) ($input['status'] ?? 'draft')),
    ];
    $errors = [];

    if ($data['title'] === '' || mb_strlen($data['title']) > 255) $errors[] = 'Enter a title of up to 255 characters.';
    if ($data['description'] === '' || mb_strlen($data['description']) > 5000) $errors[] = 'Enter a description of up to 5,000 characters.';
    if ($data['venue'] === '' || mb_strlen($data['venue']) > 255) $errors[] = 'Enter a valid venue.';
    if ($data['city'] === '' || mb_strlen($data['city']) > 100) $errors[] = 'Enter a valid city.';
    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['event_date']) || $data['event_date'] < date('Y-m-d')) $errors[] = 'The event date must be today or later.';
    if (! preg_match('/^\d{2}:\d{2}$/', $data['start_time'])) $errors[] = 'Enter a valid start time.';
    if ($data['end_time'] !== '' && (! preg_match('/^\d{2}:\d{2}$/', $data['end_time']) || $data['end_time'] <= $data['start_time'])) $errors[] = 'The end time must be after the start time.';
    if (! in_array($data['status'], ['draft', 'published', 'cancelled', 'postponed'], true)) $errors[] = 'Select a valid event status.';

    $check = db()->prepare('SELECT COUNT(*) FROM categories WHERE id = ?');
    $check->execute([$data['category_id']]);
    if (! $check->fetchColumn()) $errors[] = 'Select a valid category.';

    if ($admin) {
        $check = db()->prepare("SELECT COUNT(*) FROM users WHERE id = ? AND role = 'organizer'");
        $check->execute([$data['organizer_id']]);
        if (! $check->fetchColumn()) $errors[] = 'Select a valid organizer.';
    }

    return [$data, $errors];
}

function ticket_options_for_event(int $eventId): array
{
    $statement = db()->prepare('SELECT * FROM ticket_options WHERE event_id = ? ORDER BY sort_order, id');
    $statement->execute([$eventId]);

    return $statement->fetchAll();
}

function parse_ticket_options_from_post(array $input): array
{
    $names = $input['option_name'] ?? [];
    $prices = $input['option_price'] ?? [];
    $totals = $input['option_total'] ?? [];
    $ids = $input['option_id'] ?? [];
    $count = max(count($names), count($prices), count($totals));
    $options = [];

    for ($index = 0; $index < $count; $index++) {
        $options[] = [
            'id' => (int) ($ids[$index] ?? 0),
            'name' => trim((string) ($names[$index] ?? '')),
            'price' => (float) ($prices[$index] ?? -1),
            'total_tickets' => (int) ($totals[$index] ?? 0),
        ];
    }

    return $options;
}

function ticket_options_validation(array $options, int $eventId = 0): array
{
    $errors = [];
    $valid = [];
    $existing = [];

    if ($eventId) {
        foreach (ticket_options_for_event($eventId) as $row) {
            $existing[(int) $row['id']] = $row;
        }
    }

    if ($options === []) {
        $errors[] = 'Add at least one ticket option (e.g. VIP, Standing).';

        return [$valid, $errors];
    }

    $namesSeen = [];
    foreach ($options as $index => $option) {
        $row = $index + 1;
        $name = $option['name'];

        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = "Ticket option {$row}: enter a name of up to 100 characters.";
            continue;
        }

        if (isset($namesSeen[strtolower($name)])) {
            $errors[] = "Ticket option {$row}: \"{$name}\" is already used.";
            continue;
        }

        $namesSeen[strtolower($name)] = true;

        if ($option['price'] < 0 || $option['price'] > 99999999.99) {
            $errors[] = "Ticket option {$row}: enter a valid price.";
            continue;
        }

        $sold = 0;
        if ($option['id'] && isset($existing[$option['id']])) {
            $sold = (int) $existing[$option['id']]['total_tickets'] - (int) $existing[$option['id']]['available_tickets'];
        }

        if ($option['total_tickets'] < max(1, $sold)) {
            $errors[] = "Ticket option {$row}: total tickets cannot be less than {$sold} already sold.";
            continue;
        }

        $valid[] = [
            'id' => $option['id'],
            'name' => $name,
            'price' => $option['price'],
            'total_tickets' => $option['total_tickets'],
            'available_tickets' => $option['id'] && isset($existing[$option['id']])
                ? $option['total_tickets'] - $sold
                : $option['total_tickets'],
            'sort_order' => $index,
        ];
    }

    return [$valid, $errors];
}

function save_ticket_options(int $eventId, array $options): void
{
    $existingIds = array_map(
        static fn (array $row): int => (int) $row['id'],
        ticket_options_for_event($eventId)
    );
    $keptIds = [];

    foreach ($options as $option) {
        if ($option['id']) {
            db()->prepare(
                'UPDATE ticket_options
                 SET name = ?, price = ?, total_tickets = ?, available_tickets = ?, sort_order = ?
                 WHERE id = ? AND event_id = ?'
            )->execute([
                $option['name'],
                $option['price'],
                $option['total_tickets'],
                $option['available_tickets'],
                $option['sort_order'],
                $option['id'],
                $eventId,
            ]);
            $keptIds[] = (int) $option['id'];
        } else {
            db()->prepare(
                'INSERT INTO ticket_options (event_id, name, price, total_tickets, available_tickets, sort_order)
                 VALUES (?, ?, ?, ?, ?, ?)'
            )->execute([
                $eventId,
                $option['name'],
                $option['price'],
                $option['total_tickets'],
                $option['available_tickets'],
                $option['sort_order'],
            ]);
            $keptIds[] = (int) db()->lastInsertId();
        }
    }

    foreach (array_diff($existingIds, $keptIds) as $removeId) {
        $statement = db()->prepare('SELECT total_tickets, available_tickets FROM ticket_options WHERE id = ? AND event_id = ?');
        $statement->execute([$removeId, $eventId]);
        $row = $statement->fetch();

        if ($row && (int) $row['total_tickets'] === (int) $row['available_tickets']) {
            db()->prepare('DELETE FROM ticket_options WHERE id = ? AND event_id = ?')->execute([$removeId, $eventId]);
        }
    }

    sync_event_ticket_totals($eventId);
}

function sync_event_ticket_totals(int $eventId): void
{
    $statement = db()->prepare(
        'SELECT COALESCE(MIN(price), 0) AS min_price,
                COALESCE(SUM(total_tickets), 0) AS total_tickets,
                COALESCE(SUM(available_tickets), 0) AS available_tickets
         FROM ticket_options
         WHERE event_id = ?'
    );
    $statement->execute([$eventId]);
    $totals = $statement->fetch() ?: ['min_price' => 0, 'total_tickets' => 0, 'available_tickets' => 0];

    db()->prepare('UPDATE events SET price = ?, total_tickets = ?, available_tickets = ? WHERE id = ?')->execute([
        $totals['min_price'],
        $totals['total_tickets'],
        $totals['available_tickets'],
        $eventId,
    ]);
}

function save_event(int $eventId, array $data, ?string $image, bool $admin, int $organizerId = 0): int
{
    if ($eventId) {
        if ($admin) {
            db()->prepare(
                'UPDATE events SET organizer_id = ?, category_id = ?, title = ?, slug = ?, description = ?, venue = ?, city = ?,
                 event_date = ?, start_time = ?, end_time = ?, image = ?, is_featured = ?, status = ? WHERE id = ?'
            )->execute([
                $data['organizer_id'],
                $data['category_id'],
                $data['title'],
                unique_slug($data['title'], $eventId),
                $data['description'],
                $data['venue'],
                $data['city'],
                $data['event_date'],
                $data['start_time'],
                $data['end_time'] ?: null,
                $image,
                $data['is_featured'],
                $data['status'],
                $eventId,
            ]);
        } else {
            db()->prepare(
                'UPDATE events SET category_id = ?, title = ?, slug = ?, description = ?, venue = ?, city = ?,
                 event_date = ?, start_time = ?, end_time = ?, image = ?, is_featured = ?, status = ? WHERE id = ? AND organizer_id = ?'
            )->execute([
                $data['category_id'],
                $data['title'],
                unique_slug($data['title'], $eventId),
                $data['description'],
                $data['venue'],
                $data['city'],
                $data['event_date'],
                $data['start_time'],
                $data['end_time'] ?: null,
                $image,
                $data['is_featured'],
                $data['status'],
                $eventId,
                $organizerId,
            ]);
        }

        return $eventId;
    }

    db()->prepare(
        'INSERT INTO events (organizer_id, category_id, title, slug, description, venue, city, event_date, start_time, end_time,
         price, total_tickets, available_tickets, image, is_featured, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, ?, ?, ?)'
    )->execute([
        $admin ? $data['organizer_id'] : $organizerId,
        $data['category_id'],
        $data['title'],
        unique_slug($data['title']),
        $data['description'],
        $data['venue'],
        $data['city'],
        $data['event_date'],
        $data['start_time'],
        $data['end_time'] ?: null,
        $image,
        $data['is_featured'],
        $data['status'],
    ]);

    return (int) db()->lastInsertId();
}

function unique_slug(string $title, ?int $ignoreId = null): string
{
    $base = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $title), '-')) ?: 'event';
    $slug = $base;
    $counter = 1;
    do {
        $sql = 'SELECT COUNT(*) FROM events WHERE slug = ?'.($ignoreId ? ' AND id <> ?' : '');
        $statement = db()->prepare($sql);
        $statement->execute($ignoreId ? [$slug, $ignoreId] : [$slug]);
        $exists = (int) $statement->fetchColumn() > 0;
        if ($exists) $slug = $base.'-'.$counter++;
    } while ($exists);

    return $slug;
}

function render(string $view, array $data = []): void
{
    global $config;
    extract($data, EXTR_SKIP);
    $flashMessages = consume_flash();
    $user = current_user();
    $title = $title ?? $config['app_name'];
    ob_start();
    require __DIR__.'/templates/'.$view.'.php';
    $content = ob_get_clean();
    require __DIR__.'/header.php';
    echo $content;
    require __DIR__.'/footer.php';
}
