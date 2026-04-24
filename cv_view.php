<?php
require 'config/database.php';
require 'config/helpers.php';

require_login();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM cvs WHERE id = ? AND user_id = ?');
$stmt->execute([$id, current_user_id()]);
$cv = $stmt->fetch();

if (!$cv) {
    die(t('cv_not_found'));
}

$data = decode_cv($cv['data_json']);

$payload = [
    'cv' => [
        'id' => (int)$cv['id'],
        'title' => $cv['title'],
        'template' => $cv['template'] ?? 'modern',
    ],
    'data' => $data,
    'photo' => $cv['photo_path'] ?: 'assets/img-placeholder.png',
    'i18n' => js_translations(),
];
?>
<!doctype html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <title><?= e($cv['title']) ?></title>
    <link rel="stylesheet" href="assets/css/cv.css">
</head>

<body>
<?php if (($_GET['embed'] ?? '') !== '1'): ?>
    <div class="actions">
        <a href="dashboard.php"><?= e(t('back')) ?></a>
        <button onclick="window.print()"><?= e(t('print_save_pdf')) ?></button>
        <?= language_switcher() ?>
    </div>
<?php endif; ?>

<main id="cv-view" class="cv-pages"></main>

<script>
    window.CV_VIEW_DATA = <?= json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script>
    window.APP_LANG = "<?= $_SESSION['lang'] ?? 'en' ?>";
</script>
<script src="assets/js/cv_view.js"></script>
</body>
</html>
