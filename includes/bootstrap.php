<?php

declare(strict_types=1);

$config = require dirname(__DIR__).'/database/connection.php';
date_default_timezone_set($config['timezone']);

if (session_status() !== PHP_SESSION_ACTIVE) {
    $sessionPath = dirname(__DIR__).'/database/sessions';
    if (! is_dir($sessionPath)) {
        mkdir($sessionPath, 0700, true);
    }
    session_save_path($sessionPath);
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
    return web_root().'/'.ltrim($path, '/');
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
        'SELECT b.*, e.title AS event_title, e.event_date, e.start_time, e.venue, e.city,
                c.name AS category_name, u.name AS customer_name, u.email AS customer_email
         FROM bookings b
         JOIN events e ON e.id = b.event_id
         JOIN categories c ON c.id = e.category_id
         JOIN users u ON u.id = b.user_id
         WHERE b.id = ?'
    );
    $statement->execute([$id]);

    return $statement->fetch() ?: null;
}

function event_validation(array $input, bool $admin = false, int $sold = 0): array
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
        'price' => (float) ($input['price'] ?? -1),
        'total_tickets' => (int) ($input['total_tickets'] ?? 0),
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
    if ($data['price'] < 0 || $data['price'] > 99999999.99) $errors[] = 'Enter a valid non-negative price.';
    if ($data['total_tickets'] < max(1, $sold)) $errors[] = 'Total tickets cannot be less than tickets already sold.';
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
