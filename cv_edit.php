<?php
require 'config/database.php';
require 'config/helpers.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
$cv = null;
$data = [];
$error = '';

$allowedTemplates = [
    'modern',
    'classic',
    'classic_dark',
    'classic_blue',
    'classic_orange',
    'classic_compact',
    'classic_clean',
    'classic_line',
    'academic_sidebar',
];

$templateOptions = [
    'modern' => t('template_modern'),
    'classic' => t('template_classic'),
    'classic_dark' => t('template_classic_dark'),
    'classic_blue' => t('template_classic_blue'),
    'classic_orange' => t('template_classic_orange'),
    'classic_compact' => t('template_classic_compact'),
    'classic_clean' => t('template_classic_clean'),
    'classic_line' => t('template_classic_line'),
    'academic_sidebar' => t('template_academic_sidebar'),
];

$detailModeOptions = [
    'icon' => t('icon'),
    'label' => t('text_label'),
    'none' => t('nothing'),
];

$detailFields = [
    'email',
    'phone',
    'location',
    'website',
    'linkedin',
    'nationality',
    'rhythm',
    'driving',
];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM cvs WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, current_user_id()]);
    $cv = $stmt->fetch();

    if (!$cv) {
        die(t('cv_not_found'));
    }

    $data = decode_cv($cv['data_json']);
}

function field_value(string $key, string $default = ''): string
{
    global $data;
    return e($data[$key] ?? $default);
}

function selected_value($current, $expected): string
{
    return (string)$current === (string)$expected ? 'selected' : '';
}

function render_select_options(array $options, string $current): void
{
    foreach ($options as $value => $label) {
        echo '<option value="' . e($value) . '" ' . selected_value($current, $value) . '>' . e($label) . '</option>';
    }
}

function detail_mode_select(string $name, array $options, array $data): string
{
    $current = $data[$name] ?? 'icon';
    $html = '<select class="mini-select" name="' . e($name) . '">';

    foreach ($options as $value => $label) {
        $html .= '<option value="' . e($value) . '" ' . selected_value($current, $value) . '>' . e($label) . '</option>';
    }

    $html .= '</select>';

    return $html;
}

function clean_rows(array $rows, callable $keep): array
{
    return array_values(array_filter($rows, $keep));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $photo = $cv['photo_path'] ?? null;
        $uploadedPhoto = upload_photo($_FILES['photo'] ?? []);

        if ($uploadedPhoto) {
            $photo = $uploadedPhoto;
        }

        $payload = [];

        $basicFields = [
            'full_name',
            'headline',
            'email',
            'phone',
            'location',
            'website',
            'linkedin',
            'nationality',
            'rhythm',
            'driving',
            'summary',
            'skills',
            'languages',
            'personal_extra',
            'name_position',
            'detail_mode',
            'main_order',
            'side_order',
        ];

        foreach ($basicFields as $field) {
            $payload[$field] = trim($_POST[$field] ?? '');
        }

        foreach ($GLOBALS['detailFields'] as $field) {
            $payload[$field . '_mode'] = $_POST[$field . '_mode'] ?? 'icon';
        }

        $payload['experience'] = clean_rows(
            $_POST['experience'] ?? [],
            fn($row) => trim($row['role'] ?? '') !== '' || trim($row['company'] ?? '') !== ''
        );

        $payload['education'] = clean_rows(
            $_POST['education'] ?? [],
            fn($row) => trim($row['degree'] ?? '') !== '' || trim($row['school'] ?? '') !== ''
        );

        $payload['projects'] = clean_rows(
            $_POST['projects'] ?? [],
            fn($row) => trim($row['name'] ?? '') !== ''
        );

        $payload['qualities'] = clean_rows(
            $_POST['qualities'] ?? [],
            fn($row) =>
                trim($row['name'] ?? '') !== '' ||
                trim($row['description'] ?? '') !== ''
        );

        $payload['custom_sections'] = clean_rows(
            $_POST['custom_sections'] ?? [],
            fn($row) => trim($row['title'] ?? '') !== ''
        );

        $title = trim($_POST['title'] ?? 'Untitled CV');
        $postedTemplate = $_POST['template'] ?? ($cv['template'] ?? 'modern');
        $template = in_array($postedTemplate, $allowedTemplates, true) ? $postedTemplate : 'modern';

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

        if ($id) {
            $stmt = $pdo->prepare(
                'UPDATE cvs 
                 SET title = ?, template = ?, photo_path = ?, data_json = ? 
                 WHERE id = ? AND user_id = ?'
            );

            $stmt->execute([
                $title,
                $template,
                $photo,
                $json,
                $id,
                current_user_id(),
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO cvs (user_id, title, template, photo_path, data_json) 
                 VALUES (?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                current_user_id(),
                $title,
                $template,
                $photo,
                $json,
            ]);

            $id = (int)$pdo->lastInsertId();
        }

        redirect('cv_view.php?id=' . $id);
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$experience = $data['experience'] ?? [
    ['role' => '', 'company' => '', 'dates' => '', 'location' => '', 'description' => ''],
];

$education = $data['education'] ?? [
    ['degree' => '', 'school' => '', 'dates' => '', 'description' => ''],
];

$projects = $data['projects'] ?? [
    ['name' => '', 'description' => ''],
];

$qualities = $data['qualities'] ?? [
    ['name' => '', 'description' => ''],
];

$customSections = $data['custom_sections'] ?? [
    ['title' => '', 'content' => ''],
];

$currentTemplate = $cv['template'] ?? 'modern';
$previewPhoto = $cv['photo_path'] ?? 'assets/img-placeholder.png';

function entry_section(string $type, string $title, array $rows, array $fields, string $textareaPlaceholder): string
{
    ob_start();
    ?>
    <section class="form-panel">
        <div class="section-head">
            <div>
                <span class="panel-kicker"><?= e(t('section')) ?></span>
                <h3><?= e($title) ?></h3>
            </div>

            <button type="button" class="ghost" onclick="addEntry('<?= e($type) ?>')">
                <?= e(t('add')) ?>
            </button>
        </div>

        <div id="<?= e($type) ?>-list">
            <?php foreach ($rows as $index => $row): ?>
                <div class="entry">
                    <button type="button" class="remove-entry" onclick="removeEntry(this)">
                        <?= e(t('remove')) ?>
                    </button>

                    <div class="entry-grid">
                        <?php foreach ($fields as $key => $placeholder): ?>
                            <input
                                name="<?= e($type) ?>[<?= $index ?>][<?= e($key) ?>]"
                                placeholder="<?= e($placeholder) ?>"
                                value="<?= e($row[$key] ?? '') ?>"
                            >
                        <?php endforeach; ?>
                    </div>

                    <?php $textareaName = $type === 'custom_sections' ? 'content' : 'description'; ?>

                    <textarea
                        name="<?= e($type) ?>[<?= $index ?>][<?= e($textareaName) ?>]"
                        placeholder="<?= e($textareaPlaceholder) ?>"
                    ><?= e($row[$textareaName] ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php

    return ob_get_clean();
}
?>
<!doctype html>
<html lang="<?= e(current_lang()) ?>">
<head>
    <meta charset="utf-8">
    <title><?= e($id ? t('edit_cv') : t('new_cv_title')) ?> · CV Forge</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="assets/css/app.css">
    <script>
        window.CV_I18N = <?= json_encode(js_translations(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <script>
        window.APP_LANG = "<?= $_SESSION['lang'] ?? 'en' ?>";
    </script>
    <script defer src="assets/js/app.js"></script>
</head>

<body class="site-midnight">
<header class="app-header">
    <div>
        <span class="eyebrow">CV Forge</span>
        <h1><?= e($id ? t('edit_your_cv') : t('create_new_cv')) ?></h1>
    </div>

    <nav>
        <a class="nav-link" data-safe-leave href="dashboard.php"><?= e(t('dashboard')) ?></a>
        <?= language_switcher() ?>
    </nav>
</header>

<main class="builder-wrap">
    <form method="post" enctype="multipart/form-data" class="builder-form" id="cv-form">
        <?php if ($error): ?>
            <p class="error"><?= e($error) ?></p>
        <?php endif; ?>

        <section class="form-panel hero-panel">
            <div>
                <span class="eyebrow"><?= e(t('live_cv_builder')) ?></span>
                <h2><?= e(t('build_clean_cv')) ?></h2>
                <p><?= e(t('builder_description')) ?></p>
            </div>

            <button type="submit" class="save-top"><?= e(t('save_cv')) ?></button>
        </section>

        <section class="form-panel">
            <div class="section-title">
                <span class="panel-kicker"><?= e(t('design')) ?></span>
                <h3><?= e(t('style')) ?></h3>
            </div>

            <div class="two">
                <label>
                    <?= e(t('cv_title')) ?>
                    <input
                        name="title"
                        value="<?= e($cv['title'] ?? '') ?>"
                        placeholder="<?= e(t('cv_title_placeholder')) ?>"
                        required
                    >
                </label>

                <label>
                    <?= e(t('design')) ?>
                    <select name="template" id="template-select">
                        <?php render_select_options($templateOptions, $currentTemplate); ?>
                    </select>
                </label>
            </div>

            <div class="two">
                <label>
                    <?= e(t('name_headline_position')) ?>
                    <select name="name_position">
                        <option value="main" <?= selected_value($data['name_position'] ?? 'main', 'main') ?>>
                            <?= e(t('main_content')) ?>
                        </option>
                        <option value="side" <?= selected_value($data['name_position'] ?? '', 'side') ?>>
                            <?= e(t('sidebar')) ?>
                        </option>
                    </select>
                </label>

                <label class="file-box">
                    <?= e(t('photo')) ?>
                    <input
                        type="file"
                        name="photo"
                        id="photo-input"
                        accept="image/png,image/jpeg,image/webp"
                    >
                    <span><?= e(t('photo_help')) ?></span>
                </label>
            </div>
        </section>

        <section class="form-panel">
            <div class="section-title">
                <span class="panel-kicker"><?= e(t('identity')) ?></span>
                <h3><?= e(t('personal_details')) ?></h3>
            </div>

            <div class="two">
                <label><?= e(t('full_name')) ?><input name="full_name" value="<?= field_value('full_name') ?>" required></label>
                <label><?= e(t('headline')) ?><input name="headline" value="<?= field_value('headline') ?>" placeholder="<?= e(t('headline_placeholder')) ?>"></label>
            </div>

            <div class="two">
                <label><?= e(t('email')) ?><input name="email" value="<?= field_value('email') ?>"><?= detail_mode_select('email_mode', $detailModeOptions, $data) ?></label>
                <label><?= e(t('phone')) ?><input name="phone" value="<?= field_value('phone') ?>"><?= detail_mode_select('phone_mode', $detailModeOptions, $data) ?></label>
            </div>

            <div class="two">
                <label><?= e(t('location')) ?><input name="location" value="<?= field_value('location') ?>"><?= detail_mode_select('location_mode', $detailModeOptions, $data) ?></label>
                <label><?= e(t('website')) ?><input name="website" value="<?= field_value('website') ?>"><?= detail_mode_select('website_mode', $detailModeOptions, $data) ?></label>
            </div>

            <div class="two">
                <label><?= e(t('linkedin')) ?><input name="linkedin" value="<?= field_value('linkedin') ?>"><?= detail_mode_select('linkedin_mode', $detailModeOptions, $data) ?></label>
                <label><?= e(t('nationality')) ?><input name="nationality" value="<?= field_value('nationality') ?>"><?= detail_mode_select('nationality_mode', $detailModeOptions, $data) ?></label>
            </div>

            <div class="two">
                <label><?= e(t('internship_rhythm')) ?><input name="rhythm" placeholder="<?= e(t('rhythm_placeholder')) ?>" value="<?= field_value('rhythm') ?>"><?= detail_mode_select('rhythm_mode', $detailModeOptions, $data) ?></label>
                <label><?= e(t('driving_licence')) ?><input name="driving" placeholder="<?= e(t('driving_placeholder')) ?>" value="<?= field_value('driving') ?>"><?= detail_mode_select('driving_mode', $detailModeOptions, $data) ?></label>
            </div>

            <label>
                <?= e(t('extra_personal_details')) ?>
                <textarea name="personal_extra" placeholder="<?= e(t('extra_personal_placeholder')) ?>"><?= field_value('personal_extra') ?></textarea>
            </label>

            <label>
                <?= e(t('profile_summary')) ?>
                <textarea name="summary" placeholder="<?= e(t('summary_placeholder')) ?>"><?= field_value('summary') ?></textarea>
            </label>
        </section>

        <section class="form-panel">
            <div class="section-title">
                <span class="panel-kicker"><?= e(t('capabilities')) ?></span>
                <h3><?= e(t('skills_languages')) ?></h3>
            </div>

            <label><?= e(t('skills')) ?><textarea name="skills" placeholder="<?= e(t('skills_placeholder')) ?>"><?= field_value('skills') ?></textarea></label>
            <label><?= e(t('languages')) ?><textarea name="languages" placeholder="<?= e(t('languages_placeholder')) ?>"><?= field_value('languages') ?></textarea></label>
        </section>

        <section class="form-panel">
            <div class="section-title">
                <span class="panel-kicker"><?= e(t('layout')) ?></span>
                <h3><?= e(t('placement_order')) ?></h3>
            </div>

            <p class="muted"><?= e(t('placement_help')) ?></p>

            <label>
                <?= e(t('main_order')) ?>
                <input name="main_order" value="<?= field_value('main_order', 'summary,experience,projects,education,skills,languages,qualities,custom') ?>">
            </label>

            <label>
                <?= e(t('sidebar_order')) ?>
                <input name="side_order" value="<?= field_value('side_order', 'details,skills,languages') ?>">
            </label>

            <p class="muted"><?= e(t('available_keys')) ?></p>
        </section>

        <?= entry_section('experience', t('experience'), $experience, ['role' => t('role'), 'company' => t('company'), 'dates' => t('dates'), 'location' => t('location')], t('bullets_description')) ?>
        <?= entry_section('education', t('education'), $education, ['degree' => t('degree'), 'school' => t('school'), 'dates' => t('dates')], t('details')) ?>
        <?= entry_section('projects', t('projects'), $projects, ['name' => t('project_name')], t('description')) ?>
        <?= entry_section('qualities', t('qualities'), $qualities, ['name' => t('quality_title')], t('description')) ?>
        <?= entry_section('custom_sections', t('additional_sections'), $customSections, ['title' => t('section_title')], t('content')) ?>

        <button type="submit" class="save-bottom"><?= e(t('save_cv')) ?></button>
    </form>

    <aside class="preview-sticky">
        <div class="preview-toolbar">
            <strong><?= e(t('live_preview')) ?></strong>
            <span id="preview-template-label"><?= e(t('modern')) ?></span>
        </div>

        <div class="preview-page" id="cv-preview" data-photo="<?= e($previewPhoto) ?>"></div>
    </aside>
</main>

<div class="leave-modal" id="leave-modal">
    <div>
        <h3><?= e(t('unsaved_changes')) ?></h3>
        <p><?= e(t('save_before_leaving')) ?></p>

        <div class="modal-actions">
            <button type="button" id="modal-save"><?= e(t('save')) ?></button>
            <button type="button" id="modal-leave" class="ghost"><?= e(t('leave_without_saving')) ?></button>
            <button type="button" id="modal-cancel" class="ghost"><?= e(t('stay')) ?></button>
        </div>
    </div>
</div>
</body>
</html>
