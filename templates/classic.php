<?php
require __DIR__ . '/_render.php';

$template = $cv['template'] ?? 'classic';

$variant = match ($template) {
    'classic_dark' => 'sidebar-graphite',
    'classic_blue' => 'sidebar-slate',
    'classic_orange' => 'sidebar-sand',
    'classic_compact' => 'sidebar-compact',
    'classic_clean' => 'sidebar-clean',
    'classic_line' => 'sidebar-line',
    'academic_sidebar' => 'sidebar-academic',
    default => 'sidebar-fresh',
};

$side = cv_order($data['side_order'] ?? '', default_side_order());
$main = cv_order($data['main_order'] ?? '', default_main_order());
$name_pos = $data['name_position'] ?? 'main';
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

    <article class="cv classic <?= cv_text($variant) ?>">
        <aside class="side">
            <img class="cv-photo" src="<?= cv_text($photo) ?>" alt="Profile photo">

            <?php if ($name_pos === 'side'): ?>
                <h1><?= cv_text($data['full_name'] ?? '') ?></h1>
                <p class="side-headline"><?= cv_text($data['headline'] ?? '') ?></p>
            <?php endif; ?>

            <?php
            foreach ($side as $section) {
                if ($section === 'details') {
                    echo '<section class="personal-section">';
                    echo '<h2>' . cv_text(t('personal_information')) . '</h2>';

                    foreach (['email', 'phone', 'linkedin', 'location', 'nationality', 'rhythm', 'driving', 'website'] as $key) {
                        render_detail($data, $key);
                    }

                    render_extra_details($data);
                    echo '</section>';
                } else {
                    render_section($section, $data);
                }
            }
            ?>
        </aside>

        <main class="content">
            <?php if ($name_pos !== 'side'): ?>
                <header class="classic-header">
                    <h1><?= cv_text($data['full_name'] ?? '') ?></h1>
                    <p><?= cv_text($data['headline'] ?? '') ?></p>
                </header>
            <?php endif; ?>

            <?php
            foreach ($main as $section) {
                if (!in_array($section, $side, true)) {
                    render_section($section, $data);
                }
            }
            ?>
        </main>
    </article>
</body>
</html>