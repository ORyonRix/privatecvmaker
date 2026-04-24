'use strict';

let dirty = false;
let nextHref = null;

const I18N = window.CV_I18N || {};
function tr(key, fallback) {
    return I18N[key] || fallback || key;
}

const DETAIL_LABELS = {
    email: tr('email', 'Email'),
    phone: tr('phone', 'Phone'),
    location: tr('location', 'Location'),
    website: tr('website', 'Website'),
    linkedin: tr('linkedin', 'LinkedIn'),
    nationality: tr('nationality', 'Nationality'),
    rhythm: tr('internship_rhythm', 'Rhythm'),
    driving: tr('driving_licence', 'Driving licence'),
};

const DETAIL_ICONS = {
    email: '✉',
    phone: '☎',
    location: '🏠︎',
    website: '⌘',
    linkedin: 'in',
    nationality: '⚑',
    rhythm: '↔',
    driving: 'ꔮ',
};

const TEMPLATE_LABELS = {
    modern: tr('template_label_modern', 'Modern compact'),
    classic: tr('template_label_classic', 'Sidebar fresh'),
    classic_dark: tr('template_label_classic_dark', 'Sidebar graphite'),
    classic_blue: tr('template_label_classic_blue', 'Sidebar slate'),
    classic_orange: tr('template_label_classic_orange', 'Sidebar sand'),
    classic_compact: tr('template_label_classic_compact', 'Sidebar dense'),
    classic_clean: tr('template_label_classic_clean', 'Sidebar clean'),
    classic_line: tr('template_label_classic_line', 'Sidebar line'),
    academic_sidebar: tr('template_label_academic_sidebar', 'Academic sidebar'),
};

const PREVIEW_CLASSES = {
    classic: 'sidebar-fresh',
    classic_dark: 'sidebar-graphite',
    classic_blue: 'sidebar-slate',
    classic_orange: 'sidebar-sand',
    classic_compact: 'sidebar-compact',
    classic_clean: 'sidebar-clean',
    classic_line: 'sidebar-line',
    academic_sidebar: 'sidebar-academic',
};

const DETAIL_ORDER = [
    'email',
    'phone',
    'linkedin',
    'location',
    'nationality',
    'rhythm',
    'driving',
    'website',
];

function escapeHtml(value) {
    return String(value || '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));
}

function getValue(name) {
    return document.querySelector(`[name="${name}"]`)?.value || '';
}

function textLines(value) {
    return escapeHtml(value).replace(/\r?\n/g, '<br>');
}

function splitList(value) {
    return String(value || '').split(',').map(x => x.trim()).filter(Boolean);
}

function splitTags(value) {
    return String(value || '').split(/\r?\n/).map(x => x.trim()).filter(Boolean);
}

function templateLabel(template) {
    return TEMPLATE_LABELS[template] || tr('template_label_modern', 'Modern compact');
}

function previewClass(template) {
    return PREVIEW_CLASSES[template] || '';
}

function detailMode(key) {
    return getValue(`${key}_mode`) || 'icon';
}

function htmlToElement(html) {
    const template = document.createElement('template');
    template.innerHTML = html.trim();
    return template.content.firstElementChild;
}

function chunkWords(text, size = 24) {
    const words = String(text || '').trim().split(/([ \t]+|\r?\n)/).filter(Boolean);
    const chunks = [];

    for (let i = 0; i < words.length; i += size) {
        chunks.push(words.slice(i, i + size).join('').trim());
    }

    return chunks;
}

function collectEntries(type, fields) {
    return [...document.querySelectorAll(`#${type}-list .entry`)]
        .map(entry => {
            const row = {};

            fields.forEach(field => {
                row[field] = entry.querySelector(`[name*="[${field}]"]`)?.value || '';
            });

            row.description = entry.querySelector('[name*="[description]"]')?.value || '';
            row.content = entry.querySelector('[name*="[content]"]')?.value || '';

            return row;
        })
        .filter(row => Object.values(row).some(value => String(value).trim() !== ''));
}

function renderDetail(key, data) {
    const value = data[key];

    if (!value) {
        return '';
    }

    const mode = detailMode(key);
    let left = '';

    if (mode === 'icon') {
        left = `<span class="detail-icon">${escapeHtml(DETAIL_ICONS[key] || '•')}</span>`;
    }

    if (mode === 'label') {
        left = `<b>${escapeHtml(DETAIL_LABELS[key] || key)}:</b>`;
    }

    return `
        <div class="detail-line detail-mode-${escapeHtml(mode)}">
            ${left}
            <span>${escapeHtml(value)}</span>
        </div>
    `;
}

function renderExtraDetails(data) {
    return String(data.personal_extra || '')
        .split(/\r?\n/)
        .map(line => line.trim())
        .filter(Boolean)
        .map(line => {
            const index = line.indexOf(':');

            if (index > 0) {
                return `
                    <div class="detail-line extra-detail">
                        <b>${escapeHtml(line.slice(0, index).trim())}:</b>
                        <span>${escapeHtml(line.slice(index + 1).trim())}</span>
                    </div>
                `;
            }

            return `
                <div class="detail-line extra-detail">
                    <span>${escapeHtml(line)}</span>
                </div>
            `;
        })
        .join('');
}

function renderDetails(data) {
    return DETAIL_ORDER.map(key => renderDetail(key, data)).join('') + renderExtraDetails(data);
}

function getPreviewData() {
    return {
        full_name: getValue('full_name') || tr('your_name', 'Your Name'),
        headline: getValue('headline') || tr('your_headline', 'Your headline'),
        email: getValue('email'),
        phone: getValue('phone'),
        location: getValue('location'),
        website: getValue('website'),
        linkedin: getValue('linkedin'),
        nationality: getValue('nationality'),
        rhythm: getValue('rhythm'),
        driving: getValue('driving'),
        personal_extra: getValue('personal_extra'),
        summary: getValue('summary') || tr('your_profile_summary', 'Your profile summary will appear here.'),
        skills: getValue('skills'),
        languages: getValue('languages'),
    };
}

function getPreviewCollections() {
    return {
        experience: collectEntries('experience', ['role', 'company', 'dates', 'location']),
        education: collectEntries('education', ['degree', 'school', 'dates']),
        projects: collectEntries('projects', ['name']),
        qualities: collectEntries('qualities', ['name']),
        customSections: collectEntries('custom_sections', ['title']),
    };
}

function sectionBlocks(section, data, collections) {
    const blocks = [];

    if (section === 'summary' && data.summary) {
        blocks.push(`
            <section class="flow-title">
                <h2>${escapeHtml(tr('profile', 'Profile'))}</h2>
            </section>
        `);

        chunkWords(data.summary, 24).forEach(chunk => {
            blocks.push(`
                <span class="flow-chunk">${textLines(chunk)}</span>
            `);
        });
    }

    if (section === 'experience' && collections.experience.length) {
        collections.experience.forEach((item, index) => {
            blocks.push(`
                <section>
                    ${index === 0 ? `<h2>${escapeHtml(tr('professional_experience', 'Professional experience'))}</h2>` : ''}
                    <div class="item">
                        <h3>${escapeHtml(item.role)}${item.company ? ` · ${escapeHtml(item.company)}` : ''}</h3>
                        ${(item.dates || item.location) ? `<small>${escapeHtml(`${item.dates} ${item.location}`.trim())}</small>` : ''}
                        ${item.description ? `<p>${textLines(item.description)}</p>` : ''}
                    </div>
                </section>
            `);
        });
    }

    if (section === 'education' && collections.education.length) {
        collections.education.forEach((item, index) => {
            blocks.push(`
                <section>
                    ${index === 0 ? `<h2>${escapeHtml(tr('education', 'Education'))}</h2>` : ''}
                    <div class="item">
                        <h3>${escapeHtml(item.school)}${item.school && item.degree ? ' — ' : ''}${escapeHtml(item.degree)}</h3>
                        ${item.dates ? `<small>${escapeHtml(item.dates)}</small>` : ''}
                        ${item.description ? `<p>${textLines(item.description)}</p>` : ''}
                    </div>
                </section>
            `);
        });
    }

    if (section === 'projects' && collections.projects.length) {
        collections.projects.forEach((item, index) => {
            blocks.push(`
                <section>
                    ${index === 0 ? `<h2>${escapeHtml(tr('projects', 'Projects'))}</h2>` : ''}
                    <div class="item">
                        <h3>${escapeHtml(item.name)}</h3>
                        ${item.description ? `<p>${textLines(item.description)}</p>` : ''}
                    </div>
                </section>
            `);
        });
    }

    if (section === 'skills' && data.skills) {
        blocks.push(`
            <section>
                <h2>${escapeHtml(tr('skills', 'Skills'))}</h2>
                <div class="p-tags">
                    ${splitTags(data.skills).map(tag => `<span>${escapeHtml(tag)}</span>`).join('')}
                </div>
            </section>
        `);
    }

    if (section === 'languages' && data.languages) {
        blocks.push(`
            <section>
                <h2>${escapeHtml(tr('languages', 'Languages'))}</h2>
                <p>${textLines(data.languages)}</p>
            </section>
        `);
    }

    if (section === 'qualities' && collections.qualities.length) {
        collections.qualities.forEach((item, index) => {
            blocks.push(`
                <section>
                    ${index === 0 ? `<h2>${escapeHtml(tr('qualities', 'Qualities'))}</h2>` : ''}
                    <div class="item">
                        <h3>${escapeHtml(item.name)}</h3>
                        ${item.description ? `<p>${textLines(item.description)}</p>` : ''}
                    </div>
                </section>
            `);
        });
    }

    if (section === 'custom' && collections.customSections.length) {
        collections.customSections.forEach(item => {
            if (!item.title) {
                return;
            }

            chunkWords(item.content, 24).forEach((chunk, index) => {
                blocks.push(`
                    <section>
                        ${index === 0 ? `<h2>${escapeHtml(item.title)}</h2>` : ''}
                        <p>${textLines(chunk)}</p>
                    </section>
                `);
            });
        });
    }

    return blocks;
}

function createModernPage(data, photo, withHeader = false) {
    const page = htmlToElement(`<article class="cv cv-page modern"></article>`);

    if (withHeader) {
        page.innerHTML = `
            <header class="modern-header">
                <img class="cv-photo" src="${escapeHtml(photo)}" alt="">
                <div class="modern-header-content">
                    <h1>${escapeHtml(data.full_name)}</h1>
                    <p>${escapeHtml(data.headline)}</p>
                    <div class="modern-details">${renderDetails(data)}</div>
                </div>
            </header>
        `;
    }

    return page;
}

function sidebarHtml(data, photo, sideOrder, collections, namePosition) {
    const sideContent = sideOrder.map(section => {
        if (section === 'details') {
            return `
                <section>
                    <h2>${escapeHtml(tr('personal_information', 'Personal information'))}</h2>
                    ${renderDetails(data)}
                </section>
            `;
        }

        return sectionBlocks(section, data, collections).join('');
    }).join('');

    const sideName = namePosition === 'side'
        ? `<h1>${escapeHtml(data.full_name)}</h1><p class="side-headline">${escapeHtml(data.headline)}</p>`
        : '';

    return `
        <aside class="side">
            <img class="cv-photo" src="${escapeHtml(photo)}" alt="">
            ${sideName}
            ${sideContent}
        </aside>
    `;
}

function createSidebarPage(template, data, photo, sideOrder, collections, withHeader = false) {
    const namePosition = getValue('name_position') || 'main';
    const variant = previewClass(template);

    const page = htmlToElement(`
        <article class="cv cv-page classic ${variant} ${withHeader ? '' : 'continuation-page'}">
            ${withHeader ? sidebarHtml(data, photo, sideOrder, collections, namePosition) : '<aside class="side continuation-side"></aside>'}
            <main class="content"></main>
        </article>
    `);

    if (withHeader && namePosition !== 'side') {
        page.querySelector('.content').innerHTML = `
            <header class="classic-header">
                <h1>${escapeHtml(data.full_name)}</h1>
                <p>${escapeHtml(data.headline)}</p>
            </header>
        `;
    }

    return page;
}

function wrapPreviewPage(page) {
    const wrapper = document.createElement('div');
    wrapper.className = 'preview-a4-wrap';
    wrapper.appendChild(page);
    return wrapper;
}

function appendWithPagination(container, createPage, getContentArea, blocks) {
    let page = createPage(true);
    container.appendChild(wrapPreviewPage(page));

    let area = getContentArea(page);

    blocks.forEach(blockHtml => {
        const node = htmlToElement(blockHtml);
        area.appendChild(node);

        if (area.scrollHeight > area.clientHeight) {
            node.remove();

            page = createPage(false);
            container.appendChild(wrapPreviewPage(page));
            area = getContentArea(page);
            area.appendChild(node);
        }
    });
}

function updatePreview() {
    const preview = document.getElementById('cv-preview');

    if (!preview) {
        return;
    }

    const template = getValue('template') || 'modern';
    const label = document.getElementById('preview-template-label');

    if (label) {
        label.textContent = templateLabel(template);
    }

    const photo = preview.dataset.photo || 'assets/img-placeholder.png';
    const data = getPreviewData();
    const collections = getPreviewCollections();

    const mainOrder = splitList(
        getValue('main_order') || 'summary,experience,projects,education,skills,languages,qualities,custom'
    );

    const sideOrder = splitList(
        getValue('side_order') || 'details,skills,languages'
    );

    const mainBlocks = mainOrder
        .filter(section => template === 'modern' || !sideOrder.includes(section))
        .flatMap(section => sectionBlocks(section, data, collections));

    preview.className = 'preview-page preview-pages';
    preview.innerHTML = '';

    if (template === 'modern') {
        appendWithPagination(
            preview,
            withHeader => createModernPage(data, photo, withHeader),
            page => page,
            mainBlocks
        );

        return;
    }

    appendWithPagination(
        preview,
        withHeader => createSidebarPage(template, data, photo, sideOrder, collections, withHeader),
        page => page.querySelector('.content'),
        mainBlocks
    );
}

function showInlineError(entry, message) {
    entry.classList.add('entry-warning');

    let error = entry.querySelector('.inline-error');

    if (!error) {
        error = document.createElement('p');
        error.className = 'inline-error';
        entry.prepend(error);
    }

    error.textContent = message;

    setTimeout(() => {
        entry.classList.remove('entry-warning');
    }, 1200);
}

function canAddEntry(type) {
    const lastEntry = document.querySelector(`#${type}-list .entry:last-child`);

    if (!lastEntry) {
        return true;
    }

    const getField = field => (
        lastEntry.querySelector(`[name*="[${field}]"]`)?.value || ''
    ).trim();

    const rules = {
        experience: {
            valid: () => getField('role') || getField('company'),
            message: tr('complete_role_company', 'Complete Role or Company first.'),
        },
        education: {
            valid: () => getField('degree') || getField('school'),
            message: tr('complete_degree_school', 'Complete Degree or School first.'),
        },
        projects: {
            valid: () => getField('name'),
            message: tr('complete_project_name', 'Complete Project name first.'),
        },
        qualities: {
            valid: () => getField('name'),
            message: tr('complete_quality_title', 'Complete Quality title first.'),
        },
        custom_sections: {
            valid: () => getField('title'),
            message: tr('complete_section_title', 'Complete Section title first.'),
        },
    };

    const rule = rules[type];

    if (!rule || rule.valid()) {
        return true;
    }

    showInlineError(lastEntry, rule.message);
    return false;
}

function entryTemplate(type, index) {
    const removeButton = `
        <button type="button" class="remove-entry" onclick="removeEntry(this)">
            ${escapeHtml(tr('remove', 'Remove'))}
        </button>
    `;

    const templates = {
        experience: `
            ${removeButton}
            <div class="entry-grid">
                <input name="experience[${index}][role]" placeholder="${escapeHtml(tr('role', 'Role'))}">
                <input name="experience[${index}][company]" placeholder="${escapeHtml(tr('company', 'Company'))}">
                <input name="experience[${index}][dates]" placeholder="${escapeHtml(tr('dates', 'Dates'))}">
                <input name="experience[${index}][location]" placeholder="${escapeHtml(tr('location', 'Location'))}">
            </div>
            <textarea name="experience[${index}][description]" placeholder="${escapeHtml(tr('bullets_description', 'Bullets or description'))}"></textarea>
        `,
        education: `
            ${removeButton}
            <div class="entry-grid">
                <input name="education[${index}][degree]" placeholder="${escapeHtml(tr('degree', 'Degree'))}">
                <input name="education[${index}][school]" placeholder="${escapeHtml(tr('school', 'School'))}">
                <input name="education[${index}][dates]" placeholder="${escapeHtml(tr('dates', 'Dates'))}">
            </div>
            <textarea name="education[${index}][description]" placeholder="${escapeHtml(tr('details', 'Details'))}"></textarea>
        `,
        projects: `
            ${removeButton}
            <div class="entry-grid">
                <input name="projects[${index}][name]" placeholder="${escapeHtml(tr('project_name', 'Project name'))}">
            </div>
            <textarea name="projects[${index}][description]" placeholder="${escapeHtml(tr('description', 'Description'))}"></textarea>
        `,
        qualities: `
            ${removeButton}
            <div class="entry-grid">
                <input name="qualities[${index}][name]" placeholder="${escapeHtml(tr('quality_title', 'Quality title'))}">
            </div>
            <textarea name="qualities[${index}][description]" placeholder="${escapeHtml(tr('description', 'Description'))}"></textarea>
        `,
        custom_sections: `
            ${removeButton}
            <div class="entry-grid">
                <input name="custom_sections[${index}][title]" placeholder="${escapeHtml(tr('section_title', 'Section title'))}">
            </div>
            <textarea name="custom_sections[${index}][content]" placeholder="${escapeHtml(tr('content', 'Content'))}"></textarea>
        `,
    };

    return templates[type] || '';
}

function addEntry(type) {
    if (!canAddEntry(type)) {
        return;
    }

    const list = document.getElementById(`${type}-list`);

    if (!list) {
        return;
    }

    const entry = document.createElement('div');
    entry.className = 'entry';
    entry.innerHTML = entryTemplate(type, list.children.length);

    list.appendChild(entry);

    bindPreviewEvents();
    dirty = true;
    updatePreview();
}

function removeEntry(button) {
    const entry = button.closest('.entry');
    const list = entry?.parentElement;

    if (!entry || !list) {
        return;
    }

    if (list.children.length === 1) {
        entry.querySelectorAll('input, textarea').forEach(field => {
            field.value = '';
        });
    } else {
        entry.remove();
    }

    dirty = true;
    bindPreviewEvents();
    updatePreview();
}

function bindPreviewEvents() {
    const form = document.getElementById('cv-form');

    if (!form) {
        return;
    }

    form.querySelectorAll('input, textarea, select').forEach(field => {
        field.oninput = () => {
            dirty = true;
            updatePreview();
        };

        field.onchange = () => {
            dirty = true;
            updatePreview();
        };
    });
}

function bindPhotoPreview() {
    const photoInput = document.getElementById('photo-input');
    const preview = document.getElementById('cv-preview');

    if (!photoInput || !preview) {
        return;
    }

    photoInput.addEventListener('change', () => {
        const file = photoInput.files?.[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.onload = event => {
            preview.dataset.photo = event.target.result;
            dirty = true;
            updatePreview();
        };

        reader.readAsDataURL(file);
    });
}

function bindLeaveModal() {
    const form = document.getElementById('cv-form');
    const modal = document.getElementById('leave-modal');

    document.querySelectorAll('a:not([target])').forEach(link => {
        link.addEventListener('click', event => {
            if (!dirty || link.dataset.safeLeave !== undefined) {
                return;
            }

            event.preventDefault();
            nextHref = link.href;
            modal?.classList.add('show');
        });
    });

    document.getElementById('modal-save')?.addEventListener('click', () => {
        form?.requestSubmit();
    });

    document.getElementById('modal-leave')?.addEventListener('click', () => {
        dirty = false;
        window.location.href = nextHref || 'dashboard.php';
    });

    document.getElementById('modal-cancel')?.addEventListener('click', () => {
        modal?.classList.remove('show');
    });

    window.addEventListener('beforeunload', event => {
        if (!dirty) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('cv-form');

    bindPreviewEvents();
    bindPhotoPreview();
    bindLeaveModal();
    updatePreview();

    form?.addEventListener('submit', () => {
        dirty = false;
    });
});