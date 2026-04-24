<?php

function cv_text($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function cv_order($value, array $default): array
{
    if (is_array($value)) {
        return $value;
    }

    $parts = array_filter(array_map('trim', explode(',', (string)$value)));
    return $parts ?: $default;
}

function detail_label(string $key): string
{
    return [
        'email' => t('email'),
        'phone' => t('phone'),
        'location' => t('location'),
        'website' => t('website'),
        'linkedin' => t('linkedin'),
        'nationality' => t('nationality'),
        'rhythm' => t('internship_rhythm'),
        'driving' => t('driving_licence'),
    ][$key] ?? ucfirst($key);
}

function detail_icon(string $key): string
{
    return [
        'email' => '✉',
        'phone' => '☎',
        'location' => '🏠︎',
        'website' => '⌘',
        'linkedin' => 'in',
        'nationality' => '⚑',
        'rhythm' => '↔',
        'driving' => 'ꔮ',
    ][$key] ?? '•';
}

function render_detail(array $data, string $key): void
{
    $value = trim((string)($data[$key] ?? ''));

    if ($value === '') {
        return;
    }

    $mode = $data[$key . '_mode'] ?? ($data['detail_mode'] ?? 'icon');

    echo '<div class="detail-line detail-mode-' . cv_text($mode) . '">';

    if ($mode === 'icon') {
        echo '<span class="detail-icon" aria-hidden="true">' . cv_text(detail_icon($key)) . '</span>';
    } elseif ($mode === 'label') {
        echo '<strong class="detail-label">' . cv_text(detail_label($key)) . ':</strong>';
    }

    echo '<span class="detail-value">' . cv_text($value) . '</span>';
    echo '</div>';
}

function render_extra_details(array $data): void
{
    $text = (string)($data['personal_extra'] ?? '');
    $lines = array_filter(array_map('trim', preg_split('/\r?\n/', $text)));

    foreach ($lines as $line) {
        if ($line === '') {
            continue;
        }

        $parts = explode(':', $line, 2);

        echo '<div class="detail-line extra-detail">';

        if (count($parts) > 1) {
            echo '<strong class="detail-label">' . cv_text(trim($parts[0])) . ':</strong>';
            echo '<span class="detail-value">' . cv_text(trim($parts[1])) . '</span>';
        } else {
            echo '<span class="detail-value">' . cv_text($line) . '</span>';
        }

        echo '</div>';
    }
}

function cv_tags(string $text): void
{
    $tags = array_filter(array_map('trim', preg_split('/\r?\n/', $text)));

    foreach ($tags as $tag) {
        echo '<span>' . cv_text($tag) . '</span>';
    }
}

function cv_paragraph(string $text): string
{
    return nl2br(cv_text($text));
}

function render_section(string $section, array $data): void
{
    if ($section === 'summary' && trim((string)($data['summary'] ?? '')) !== '') {
        echo '<section>';
        echo '<h2>' . cv_text(t('profile')) . '</h2>';
        echo '<p>' . cv_paragraph($data['summary']) . '</p>';
        echo '</section>';
    }

    if ($section === 'experience' && !empty($data['experience'])) {
        echo '<section>';
        echo '<h2>' . cv_text(t('professional_experience')) . '</h2>';

        foreach ($data['experience'] as $item) {
            echo '<div class="item">';
            echo '<h3>' . cv_text($item['role'] ?? '');

            if (trim((string)($item['company'] ?? '')) !== '') {
                echo ' · ' . cv_text($item['company']);
            }

            echo '</h3>';

            $meta = trim(($item['dates'] ?? '') . ' ' . ($item['location'] ?? ''));

            if ($meta !== '') {
                echo '<small>' . cv_text($meta) . '</small>';
            }

            if (trim((string)($item['description'] ?? '')) !== '') {
                echo '<p>' . cv_paragraph($item['description']) . '</p>';
            }

            echo '</div>';
        }

        echo '</section>';
    }

    if ($section === 'education' && !empty($data['education'])) {
        echo '<section>';
        echo '<h2>' . cv_text(t('education')) . '</h2>';

        foreach ($data['education'] as $item) {
            echo '<div class="item">';
            echo '<h3>' . cv_text($item['school'] ?? '');

            if (trim((string)($item['school'] ?? '')) !== '' && trim((string)($item['degree'] ?? '')) !== '') {
                echo ' — ';
            }

            echo cv_text($item['degree'] ?? '') . '</h3>';

            if (trim((string)($item['dates'] ?? '')) !== '') {
                echo '<small>' . cv_text($item['dates']) . '</small>';
            }

            if (trim((string)($item['description'] ?? '')) !== '') {
                echo '<p>' . cv_paragraph($item['description']) . '</p>';
            }

            echo '</div>';
        }

        echo '</section>';
    }

    if ($section === 'projects' && !empty($data['projects'])) {
        echo '<section>';
        echo '<h2>' . cv_text(t('projects')) . '</h2>';

        foreach ($data['projects'] as $item) {
            echo '<div class="item">';
            echo '<h3>' . cv_text($item['name'] ?? '') . '</h3>';

            if (trim((string)($item['description'] ?? '')) !== '') {
                echo '<p>' . cv_paragraph($item['description']) . '</p>';
            }

            echo '</div>';
        }

        echo '</section>';
    }

    if ($section === 'skills' && trim((string)($data['skills'] ?? '')) !== '') {
        echo '<section>';
        echo '<h2>' . cv_text(t('skills')) . '</h2>';
        echo '<div class="tags">';
        cv_tags($data['skills']);
        echo '</div>';
        echo '</section>';
    }

    if ($section === 'languages' && trim((string)($data['languages'] ?? '')) !== '') {
        echo '<section>';
        echo '<h2>' . cv_text(t('languages')) . '</h2>';
        echo '<p>' . cv_paragraph($data['languages']) . '</p>';
        echo '</section>';
    }

    if ($section === 'qualities' && !empty($data['qualities'])) {
        echo '<section>';
        echo '<h2>' . cv_text(t('qualities')) . '</h2>';

        foreach ($data['qualities'] as $item) {
            echo '<div class="item">';
            echo '<h3>' . cv_text($item['name'] ?? '') . '</h3>';

            if (trim((string)($item['description'] ?? '')) !== '') {
                echo '<p>' . cv_paragraph($item['description']) . '</p>';
            }

            echo '</div>';
        }

        echo '</section>';
    }

    if ($section === 'custom' && !empty($data['custom_sections'])) {
        foreach ($data['custom_sections'] as $item) {
            if (trim((string)($item['title'] ?? '')) === '') {
                continue;
            }

            echo '<section>';
            echo '<h2>' . cv_text($item['title']) . '</h2>';
            echo '<p>' . cv_paragraph($item['content'] ?? '') . '</p>';
            echo '</section>';
        }
    }
}

function default_main_order(): array
{
    return ['summary', 'experience', 'projects', 'education', 'skills', 'languages', 'qualities', 'custom'];
}

function default_side_order(): array
{
    return ['details', 'skills', 'languages'];
}