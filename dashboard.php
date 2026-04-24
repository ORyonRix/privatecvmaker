<?php
require 'config/database.php';
require 'config/helpers.php';

require_login();

$stmt = $pdo->prepare('SELECT * FROM cvs WHERE user_id = ? ORDER BY updated_at DESC');
$stmt->execute([current_user_id()]);
$cvs = $stmt->fetchAll();
?>
<!doctype html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <title><?= e(t('dashboard')) ?> · CV Forge</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>

<body>
<header class="app-header">
    <h1>CV Forge</h1>

    <nav>
        <?= e(t('hello')) ?>, <?= e($_SESSION['name']) ?> |
        <a href="logout.php"><?= e(t('logout')) ?></a>
        <?= language_switcher() ?>
    </nav>
</header>

<main class="wrap">
    <div class="dashboard-top">
        <div>
            <span class="eyebrow"><?= e(t('dashboard')) ?></span>
            <h2><?= e(t('your_cvs')) ?></h2>
        </div>

        <a class="button" href="cv_edit.php"><?= e(t('new_cv')) ?></a>
    </div>

    <div class="grid">
        <?php foreach ($cvs as $cv): ?>
            <?php
            $data = decode_cv($cv['data_json']);
            $name = trim($data['full_name'] ?? '') ?: $cv['title'];
            $headline = trim($data['headline'] ?? '') ?: t('no_headline');
            $template = $cv['template'] ?? 'modern';
            ?>

            <section class="card cv-card">
                <a class="cv-thumb-real" href="cv_edit.php?id=<?= (int)$cv['id'] ?>">
                    <iframe
                        src="cv_view.php?id=<?= (int)$cv['id'] ?>&embed=1"
                        loading="lazy"
                        scrolling="no"
                    ></iframe>
                </a>

                <h2><?= e($cv['title']) ?></h2>
                <p><?= e(t('design')) ?>: <?= e(template_label($template)) ?></p>
                <p><?= e(t('updated')) ?>: <?= e($cv['updated_at']) ?></p>

                <div class="card-actions">
                    <a class="action-btn edit" href="cv_edit.php?id=<?= (int)$cv['id'] ?>"><?= e(t('edit')) ?></a>
                    <a class="action-btn view" href="cv_view.php?id=<?= (int)$cv['id'] ?>" target="_blank"><?= e(t('view_print')) ?></a>
                    <a class="action-btn delete" href="delete_cv.php?id=<?= (int)$cv['id'] ?>" onclick="return confirm('<?= e(t('delete_confirm')) ?>')"><?= e(t('delete')) ?></a>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</main>
</body>
</html>
