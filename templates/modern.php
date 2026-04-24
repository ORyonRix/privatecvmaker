<?php
require __DIR__ . '/_render.php';

$order = cv_order($data['main_order'] ?? '', default_main_order());
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= cv_text($cv['title'] ?? 'CV') ?></title>
    <link rel="stylesheet" href="assets/css/cv.css">
</head>
<body>
    <?php if (empty($GLOBALS['cv_embed'])): ?>
        <div class="actions">
            <a href="dashboard.php">← <?= cv_text(t('back')) ?></a>
            <button onclick="window.print()"><?= cv_text(t('print_save_pdf')) ?></button>
        </div>
    <?php endif; ?>

    <article class="cv modern">
        <header class="modern-header">
            <img class="cv-photo" src="<?= cv_text($photo) ?>" alt="Profile photo">

            <div class="modern-header-content">
                <h1><?= cv_text($data['full_name'] ?? '') ?></h1>
                <p><?= cv_text($data['headline'] ?? '') ?></p>

                <div class="modern-details">
                    <?php
                    foreach (['phone', 'email', 'linkedin', 'location', 'nationality', 'rhythm', 'driving', 'website'] as $key) {
                        render_detail($data, $key);
                    }
                    render_extra_details($data);
                    ?>
                </div>
            </div>
        </header>

        <?php foreach ($order as $section): ?>
            <?php render_section($section, $data); ?>
        <?php endforeach; ?>
    </article>
</body>
</html>