<?php

require dirname(__DIR__).'/includes/bootstrap.php';

require_role('admin');
$id = (int) ($_GET['id'] ?? 0);
$event = $id ? event_by_id($id) : null;
if ($id && ! $event) {
    http_response_code(404);
    render('error', ['title' => 'Event not found', 'message' => 'The requested event could not be found.']);
    exit;
}
$ticketOptions = $id ? ticket_options_for_event($id) : [];
$errors = [];
if (is_post()) {
    verify_csrf();
    [$image, $imageErrors] = process_event_image($event['image'] ?? null);
    [$data, $errors] = event_validation($_POST, true);
    [$options, $optionErrors] = ticket_options_validation(parse_ticket_options_from_post($_POST), $id);
    $errors = array_merge($imageErrors, $errors, $optionErrors);
    if ($errors === []) {
        $eventId = save_event($id, $data, $image, true);
        save_ticket_options($eventId, $options);
        flash('success', 'Event saved successfully.');
        redirect_to('admin-events');
    }
    $event = array_merge($event ?? [], $data, ['image' => $image]);
    $ticketOptions = parse_ticket_options_from_post($_POST);
}
$categories = db()->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$organizers = db()->query("SELECT id, name FROM users WHERE role = 'organizer' ORDER BY name")->fetchAll();
$title = $event ? 'Edit event' : 'Create event';
$bodyClass = 'admin-page admin-workspace';
$user = current_user();
$flashMessages = consume_flash();
require dirname(__DIR__).'/includes/header.php';
?>
<section class="plain-page-head"><div class="container plain-page-head-inner"><div><span class="plain-page-label">Administration</span><h1><?= e($title) ?></h1><p>Complete the event details below. Required fields are marked.</p></div></div></section>
<section class="section compact-top"><div class="container narrow-wide"><div class="form-card">
<?php if($errors): ?><div class="alert alert-error"><strong>Please correct the following:</strong><ul><?php foreach($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" action="<?= e(app_url('admin-event-form', $event && isset($event['id']) ? ['id'=>$event['id']] : [])) ?>"><?= csrf_field() ?>
<div class="form-group"><label for="organizer_id">Organizer *</label><select class="form-control" id="organizer_id" name="organizer_id" required><option value="">Select organizer</option><?php foreach($organizers as $organizer): ?><option value="<?= $organizer['id'] ?>" <?= (int)($event['organizer_id'] ?? 0)===(int)$organizer['id']?'selected':'' ?>><?= e($organizer['name']) ?></option><?php endforeach; ?></select></div>
<div class="form-row"><div class="form-group"><label for="title">Title *</label><input class="form-control" id="title" name="title" value="<?= e($event['title'] ?? '') ?>" maxlength="255" required></div><div class="form-group"><label for="category_id">Category *</label><select class="form-control" id="category_id" name="category_id" required><option value="">Select category</option><?php foreach($categories as $item): ?><option value="<?= $item['id'] ?>" <?= (int)($event['category_id'] ?? 0)===(int)$item['id']?'selected':'' ?>><?= e($item['name']) ?></option><?php endforeach; ?></select></div></div>
<div class="form-group"><label for="description">Description *</label><textarea class="form-control" id="description" name="description" maxlength="5000" required><?= e($event['description'] ?? '') ?></textarea></div>
<div class="form-group"><label for="image">Event poster</label><input class="form-control" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"><small class="form-hint">Upload a JPG, PNG, or WEBP poster up to 5 MB. Leave empty to keep the current poster.</small></div>
<div class="form-row"><div class="form-group"><label for="venue">Venue *</label><input class="form-control" id="venue" name="venue" value="<?= e($event['venue'] ?? '') ?>" required></div><div class="form-group"><label for="city">City *</label><input class="form-control" id="city" name="city" value="<?= e($event['city'] ?? 'Colombo') ?>" required></div></div>
<div class="form-row three"><div class="form-group"><label for="event_date">Date *</label><input class="form-control" id="event_date" name="event_date" type="date" min="<?= date('Y-m-d') ?>" value="<?= e($event['event_date'] ?? '') ?>" required></div><div class="form-group"><label for="start_time">Start *</label><input class="form-control" id="start_time" name="start_time" type="time" value="<?= e(substr((string)($event['start_time'] ?? ''),0,5)) ?>" required></div><div class="form-group"><label for="end_time">End</label><input class="form-control" id="end_time" name="end_time" type="time" value="<?= e(substr((string)($event['end_time'] ?? ''),0,5)) ?>"></div></div>
<?php require dirname(__DIR__).'/includes/templates/partials/ticket-options-form.php'; ?>
<div class="form-row"><div class="form-group"><label for="status">Status *</label><select class="form-control" id="status" name="status"><?php foreach(['draft','published','postponed','cancelled'] as $status): ?><option value="<?= $status ?>" <?= ($event['status'] ?? 'draft')===$status?'selected':'' ?>><?= ucfirst($status) ?></option><?php endforeach; ?></select></div><div class="form-group checkbox-block"><label class="checkbox-label"><input type="checkbox" name="is_featured" value="1" <?= !empty($event['is_featured'])?'checked':'' ?>> Feature this event</label></div></div>
<div class="actions-row"><button class="btn btn-primary" type="submit">Save event</button><a class="btn btn-secondary" href="<?= e(app_url('admin-events')) ?>">Cancel</a></div></form>
</div></div></section>
<?php require dirname(__DIR__).'/includes/footer.php'; ?>
