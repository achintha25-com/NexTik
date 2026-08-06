<?php

declare(strict_types=1);

require dirname(__DIR__).'/includes/bootstrap.php';

$passwordHash = '$2y$10$Iq/irBATt1eABeyNSk56beZvQhpZQNc2hGSGSmj/tL2D76qrCm0qS';

$organizers = [
    [
        'name' => 'Kandy Live Events',
        'email' => 'kandy.live@nextik.lk',
        'phone' => '0774000001',
        'event' => [
            'category_id' => 2,
            'title' => 'Hill Country Live Sessions',
            'description' => 'An open-air concert evening in the heart of Kandy with local bands, food stalls, and lakeside views.',
            'venue' => 'Kandy Lake View Arena',
            'city' => 'Kandy',
            'days_ahead' => 18,
            'start_time' => '18:30:00',
            'end_time' => '23:00:00',
            'is_featured' => 1,
            'image_url' => 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&w=1200&q=80',
            'options' => [
                ['name' => 'VIP', 'price' => 8500, 'tickets' => 80],
                ['name' => 'Standing', 'price' => 4500, 'tickets' => 320],
            ],
        ],
    ],
    [
        'name' => 'Galle Festival Group',
        'email' => 'galle.fest@nextik.lk',
        'phone' => '0774000002',
        'event' => [
            'category_id' => 5,
            'title' => 'Fort Sunset Food Fest',
            'description' => 'A family-friendly evening of street food, live acoustic sets, and craft vendors inside the Galle Fort.',
            'venue' => 'Galle Fort Green',
            'city' => 'Galle',
            'days_ahead' => 24,
            'start_time' => '16:00:00',
            'end_time' => '22:00:00',
            'is_featured' => 0,
            'image_url' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=1200&q=80',
            'options' => [
                ['name' => 'Family Pass', 'price' => 5000, 'tickets' => 150],
                ['name' => 'General Admission', 'price' => 2500, 'tickets' => 350],
            ],
        ],
    ],
    [
        'name' => 'Negombo Nightlife',
        'email' => 'negombo.nights@nextik.lk',
        'phone' => '0774000003',
        'event' => [
            'category_id' => 3,
            'title' => 'Coastal Beats By The Bay',
            'description' => 'Dance until midnight with international DJs, light shows, and a beachside bar at Negombo lagoon.',
            'venue' => 'Lagoon Deck Negombo',
            'city' => 'Negombo',
            'days_ahead' => 32,
            'start_time' => '20:00:00',
            'end_time' => '23:59:00',
            'is_featured' => 1,
            'image_url' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?auto=format&fit=crop&w=1200&q=80',
            'options' => [
                ['name' => 'VIP Lounge', 'price' => 9500, 'tickets' => 60],
                ['name' => 'Standing', 'price' => 5500, 'tickets' => 440],
            ],
        ],
    ],
    [
        'name' => 'Jaffna Cultural Circle',
        'email' => 'jaffna.culture@nextik.lk',
        'phone' => '0774000004',
        'event' => [
            'category_id' => 6,
            'title' => 'Northern Lights Tamil Night',
            'description' => 'Celebrate Tamil music and culture with live DJs, traditional fusion performances, and local cuisine.',
            'venue' => 'Jaffna Cultural Centre',
            'city' => 'Jaffna',
            'days_ahead' => 27,
            'start_time' => '19:00:00',
            'end_time' => '23:30:00',
            'is_featured' => 0,
            'image_url' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1200&q=80',
            'options' => [
                ['name' => 'Premium', 'price' => 6000, 'tickets' => 100],
                ['name' => 'General', 'price' => 3500, 'tickets' => 400],
            ],
        ],
    ],
    [
        'name' => 'Ella Mountain Sounds',
        'email' => 'ella.sounds@nextik.lk',
        'phone' => '0774000005',
        'event' => [
            'category_id' => 4,
            'title' => 'Ella Ridge Trail Run',
            'description' => 'A scenic mountain trail run and wellness morning with recovery zones, music, and healthy food pop-ups.',
            'venue' => 'Ella Rock Base Camp',
            'city' => 'Ella',
            'days_ahead' => 40,
            'start_time' => '06:00:00',
            'end_time' => '12:00:00',
            'is_featured' => 0,
            'image_url' => 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?auto=format&fit=crop&w=1200&q=80',
            'options' => [
                ['name' => 'Early Bird', 'price' => 3200, 'tickets' => 120],
                ['name' => 'Standard Entry', 'price' => 4200, 'tickets' => 280],
            ],
        ],
    ],
];

function download_event_image(string $url, string $slug): ?string
{
    $directory = dirname(__DIR__).'/images/events';
    if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
        throw new RuntimeException('Could not create images/events directory.');
    }

    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'header' => "User-Agent: NexTikSeeder/1.0\r\n",
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $contents = @file_get_contents($url, false, $context);
    if ($contents === false || $contents === '') {
        return null;
    }

    $filename = 'seed-'.$slug.'.jpg';
    $path = $directory.'/'.$filename;
    if (file_put_contents($path, $contents) === false) {
        return null;
    }

    $imageInfo = @getimagesize($path);
    if (! $imageInfo) {
        @unlink($path);

        return null;
    }

    return 'events/'.$filename;
}

$pdo = db();
$createdOrganizers = 0;
$createdEvents = 0;

foreach ($organizers as $item) {
    $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $check->execute([$item['email']]);
    $organizerId = (int) ($check->fetchColumn() ?: 0);

    if (! $organizerId) {
        $insert = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'organizer')");
        $insert->execute([$item['name'], $item['email'], $item['phone'], $passwordHash]);
        $organizerId = (int) $pdo->lastInsertId();
        $createdOrganizers++;
        echo "Organizer created: {$item['name']} ({$item['email']})\n";
    } else {
        echo "Organizer exists: {$item['email']}\n";
    }

    $event = $item['event'];
    $slug = unique_slug($event['title']);

    $checkEvent = $pdo->prepare('SELECT id FROM events WHERE slug = ?');
    $checkEvent->execute([$slug]);
    if ($checkEvent->fetchColumn()) {
        echo "Event already exists: {$event['title']}\n";
        continue;
    }

    $imagePath = download_event_image($event['image_url'], $slug);
    if ($imagePath === null) {
        echo "Warning: could not download image for {$event['title']}\n";
    }

    $eventDate = date('Y-m-d', strtotime('+'.$event['days_ahead'].' days'));

    $insertEvent = $pdo->prepare(
        'INSERT INTO events (organizer_id, category_id, title, slug, description, venue, city, event_date, start_time, end_time,
         price, total_tickets, available_tickets, image, is_featured, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, ?, ?, ?)'
    );
    $insertEvent->execute([
        $organizerId,
        $event['category_id'],
        $event['title'],
        $slug,
        $event['description'],
        $event['venue'],
        $event['city'],
        $eventDate,
        $event['start_time'],
        $event['end_time'],
        $imagePath,
        $event['is_featured'],
        'published',
    ]);

    $eventId = (int) $pdo->lastInsertId();
    $options = [];

    foreach ($event['options'] as $index => $option) {
        $options[] = [
            'id' => 0,
            'name' => $option['name'],
            'price' => $option['price'],
            'total_tickets' => $option['tickets'],
            'available_tickets' => $option['tickets'],
            'sort_order' => $index,
        ];
    }

    save_ticket_options($eventId, $options);
    $createdEvents++;
    echo "Event created: {$event['title']} (organizer #{$organizerId})\n";
}

echo "\nDone. Created {$createdOrganizers} organizers and {$createdEvents} events.\n";
echo "All organizer passwords: password\n";
