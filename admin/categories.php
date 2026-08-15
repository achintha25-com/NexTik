<?php

declare(strict_types=1);

require dirname(__DIR__).'/includes/bootstrap.php';

require_role('admin');

$errors = [];

if (is_post()) {
    verify_csrf();
    $action = post_string('action');

    if ($action === 'create') {
        $name = post_string('name');

        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = 'Enter a category name of up to 100 characters.';
        }

        $check = db()->prepare('SELECT COUNT(*) FROM categories WHERE name = ?');
        $check->execute([$name]);
        if ($check->fetchColumn()) {
            $errors[] = 'That category already exists.';
        }

        if ($errors === []) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-'));
            db()->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)')->execute([$name, $slug]);
            flash('success', 'Category created successfully.');
            redirect_to('admin-categories');
        }
    }

    if ($action === 'delete') {
        $categoryId = (int) ($_POST['id'] ?? 0);
        $check = db()->prepare('SELECT COUNT(*) FROM events WHERE category_id = ?');
        $check->execute([$categoryId]);

        if ($check->fetchColumn()) {
            $errors[] = 'A category with events cannot be deleted.';
        } else {
            db()->prepare('DELETE FROM categories WHERE id = ?')->execute([$categoryId]);
            flash('success', 'Category deleted successfully.');
            redirect_to('admin-categories');
        }
    }
}

$categories = db()->query(
    'SELECT c.*, COUNT(e.id) AS event_count
     FROM categories c
     LEFT JOIN events e ON e.category_id = c.id
     GROUP BY c.id
     ORDER BY c.name'
)->fetchAll();

$title = 'Categories';
$user = current_user();
$flashMessages = consume_flash();

require dirname(__DIR__).'/includes/header.php';
?>

<section class="page-header">
    <div class="container narrow-wide">
        <p class="eyebrow">ADMINISTRATION</p>
        <h1>Categories</h1>
        <p>Keep event discovery organized.</p>
        <div class="page-toolbar">
            <a class="btn btn-secondary" href="<?= e(app_url('admin-dashboard')) ?>">Dashboard</a>
            <a class="btn btn-secondary" href="<?= e(app_url('admin-events')) ?>">Events</a>
        </div>
    </div>
</section>

<section class="section compact-top">
    <div class="container narrow-wide">
        <div class="form-card">
            <h2>Add category</h2>
            <?php if ($errors): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(app_url('admin-categories')) ?>" class="inline-create">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="create">
                <input class="form-control" name="name" placeholder="Category name" maxlength="100" required>
                <button class="btn btn-primary" type="submit">Add</button>
            </form>
        </div>

        <div class="table-card">
            <table>
                <thead><tr><th>Category</th><th>Slug</th><th>Events</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><strong><?= e($category['name']) ?></strong></td>
                            <td class="muted"><?= e($category['slug']) ?></td>
                            <td><?= (int) $category['event_count'] ?></td>
                            <td>
                                <?php if (! (int) $category['event_count']): ?>
                                    <form method="post" action="<?= e(app_url('admin-categories')) ?>" data-confirm="Delete this category?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                        <button class="icon-btn icon-btn-danger" type="submit" title="Delete category" aria-label="Delete <?= e($category['name']) ?>">
                                            <?= icon('delete') ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require dirname(__DIR__).'/includes/footer.php'; ?>
