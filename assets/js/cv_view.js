'use strict';

const payload = window.CV_VIEW_DATA;
const cv = payload.cv;
const data = payload.data || {};
const photo = payload.photo || 'assets/img-placeholder.png';
const I18N = payload.i18n || {};
function tr(key, fallback) {
    return I18N[key] || fallback || key;
}

const detailOrder = ['email', 'phone', 'linkedin', 'location', 'nationality', 'rhythm', 'driving', 'website'];

const icons = {
    email: '✉',
    phone: '☎',
    location: '🏠︎',
    website: '⌘',
    linkedin: 'in',
    nationality: '⚑',
    rhythm: '↔',
    driving: 'ꔮ',
};

const labels = {
    email: tr('email', 'Email'),
    phone: tr('phone', 'Phone'),
    location: tr('location', 'Location'),
    website: tr('website', 'Website'),
    linkedin: tr('linkedin', 'LinkedIn'),
    nationality: tr('nationality', 'Nationality'),
    rhythm: tr('internship_rhythm', 'Rhythm'),
    driving: tr('driving_licence', 'Driving licence'),
};

const variants = {
    classic: 'sidebar-fresh',
    classic_dark: 'sidebar-graphite',
    classic_blue: 'sidebar-slate',
    classic_orange: 'sidebar-sand',
    classic_compact: 'sidebar-compact',
    classic_clean: 'sidebar-clean',
    classic_line: 'sidebar-line',
    academic_sidebar: 'sidebar-academic',
};

function esc(value) {
    return String(value || '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));
}

function lines(value) {
    return esc(value).replace(/\r?\n/g, '<br>');
}

function order(value, fallback) {
    return String(value || fallback)
        .split(',')
        .map(item => item.trim())
        .filter(Boolean);
}

function tags(value) {
    return String(value || '')
        .split(/\r?\n/)
        .map(item => item.trim())
        .filter(Boolean)
        .map(item => `<span>${esc(item)}</span>`)
        .join('');
}

function chunkWords(text, size = 24) {
    const words = String(text || '').trim().split(/([ \t]+|\r?\n)/).filter(Boolean);
    const chunks = [];

    for (let i = 0; i < words.length; i += size) {
        chunks.push(words.slice(i, i + size).join('').trim());
    }

    return chunks;
}

function detail(key) {
    const value = String(data[key] || '').trim();

    if (!value) {
        return '';
    }

    const mode = data[`${key}_mode`] || data.detail_mode || 'icon';

    let left = '';

    if (mode === 'icon') {
        left = `<span class="detail-icon">${esc(icons[key] || '•')}</span>`;
    }

    if (mode === 'label') {
        left = `<strong class="detail-label">${esc(labels[key] || key)}:</strong>`;
    }

    return `<div class="detail-line detail-mode-${esc(mode)}">${left}<span class="detail-value">${esc(value)}</span></div>`;
}

function extraDetails() {
    return String(data.personal_extra || '')
        .split(/\r?\n/)
        .map(line => line.trim())
        .filter(Boolean)
        .map(line => {
            const index = line.indexOf(':');

            if (index > 0) {
                return `
                    <div class="detail-line extra-detail">
                        <strong class="detail-label">${esc(line.slice(0, index).trim())}:</strong>
                        <span class="detail-value">${esc(line.slice(index + 1).trim())}</span>
                    </div>
                `;
            }

            return `<div class="detail-line extra-detail"><span class="detail-value">${esc(line)}</span></div>`;
        })
        .join('');
}

function detailsBlock() {
    return detailOrder.map(detail).join('') + extraDetails();
}

function sectionBlocks(section) {
    const blocks = [];

    if (section === 'summary' && data.summary) {
        blocks.push(`
            <section class="flow-title">
                <h2>${esc(tr('profile', 'Profile'))}</h2>
            </section>
        `);

        chunkWords(data.summary, 24).forEach(chunk => {
            blocks.push(`
                <span class="flow-chunk">${lines(chunk)}</span>
            `);
        });
    }

    if (section === 'experience' && Array.isArray(data.experience)) {
        data.experience.forEach((item, index) => {
            blocks.push(`
                <section>
                    ${index === 0 ? `<h2>${esc(tr('professional_experience', 'Professional experience'))}</h2>` : ''}
                    <div class="item">
                        <h3>${esc(item.role)}${item.company ? ` · ${esc(item.company)}` : ''}</h3>
                        ${(item.dates || item.location) ? `<small>${esc(`${item.dates || ''} ${item.location || ''}`.trim())}</small>` : ''}
                        ${item.description ? `<p>${lines(item.description)}</p>` : ''}
                    </div>
                </section>
            `);
        });
    }

    if (section === 'projects' && Array.isArray(data.projects)) {
        data.projects.forEach((item, index) => {
            blocks.push(`
                <section>
                    ${index === 0 ? `<h2>${esc(tr('projects', 'Projects'))}</h2>` : ''}
                    <div class="item">
                        <h3>${esc(item.name)}</h3>
                        ${item.description ? `<p>${lines(item.description)}</p>` : ''}
                    </div>
                </section>
            `);
        });
    }

    if (section === 'education' && Array.isArray(data.education)) {
        data.education.forEach((item, index) => {
            blocks.push(`
                <section>
                    ${index === 0 ? `<h2>${esc(tr('education', 'Education'))}</h2>` : ''}
                    <div class="item">
                        <h3>${esc(item.school)}${item.school && item.degree ? ' — ' : ''}${esc(item.degree)}</h3>
                        ${item.dates ? `<small>${esc(item.dates)}</small>` : ''}
                        ${item.description ? `<p>${lines(item.description)}</p>` : ''}
                    </div>
                </section>
            `);
        });
    }

    if (section === 'skills' && data.skills) {
        blocks.push(`<section><h2>${esc(tr('skills', 'Skills'))}</h2><div class="tags">${tags(data.skills)}</div></section>`);
    }

    if (section === 'languages' && data.languages) {
        blocks.push(`<section><h2>${esc(tr('languages', 'Languages'))}</h2><p>${lines(data.languages)}</p></section>`);
    }

    if (section === 'qualities' && Array.isArray(data.qualities)) {
        data.qualities.forEach((item, index) => {
            blocks.push(`
                <section>
                    ${index === 0 ? `<h2>${esc(tr('qualities', 'Qualities'))}</h2>` : ''}
                    <div class="item">
                        <h3>${esc(item.name)}</h3>
                        ${item.description ? `<p>${lines(item.description)}</p>` : ''}
                    </div>
                </section>
            `);
        });
    }

    if (section === 'custom' && Array.isArray(data.custom_sections)) {
        data.custom_sections.forEach(item => {
            if (!item.title) return;

            chunkWords(item.content, 24).forEach((chunk, index) => {
                blocks.push(`
                    <section>
                        ${index === 0 ? `<h2>${esc(item.title)}</h2>` : ''}
                        <p>${lines(chunk)}</p>
                    </section>
                `);
            });
        });
    }

    return blocks;
}

function createModernPage(firstPage) {
    const page = document.createElement('article');
    page.className = 'cv cv-page modern';

    if (firstPage) {
        page.innerHTML = `
            <header class="modern-header">
                <img class="cv-photo" src="${esc(photo)}" alt="">
                <div class="modern-header-content">
                    <h1>${esc(data.full_name)}</h1>
                    <p>${esc(data.headline)}</p>
                    <div class="modern-details">${detailsBlock()}</div>
                </div>
            </header>
        `;
    }

    return page;
}

function sidebarHtml(firstPage) {
    if (!firstPage) {
        return '<aside class="side continuation-side"></aside>';
    }

    const sideOrder = order(data.side_order, 'details,skills,languages');
    const namePosition = data.name_position || 'main';

    const sideContent = sideOrder.map(section => {
        if (section === 'details') {
            return `<section><h2>${esc(tr('personal_information', 'Personal information'))}</h2>${detailsBlock()}</section>`;
        }

        return sectionBlocks(section).join('');
    }).join('');

    return `
        <aside class="side">
            <img class="cv-photo" src="${esc(photo)}" alt="">
            ${namePosition === 'side' ? `<h1>${esc(data.full_name)}</h1><p class="side-headline">${esc(data.headline)}</p>` : ''}
            ${sideContent}
        </aside>
    `;
}

function createClassicPage(firstPage) {
    const template = cv.template || 'classic';
    const variant = variants[template] || 'sidebar-fresh';
    const namePosition = data.name_position || 'main';

    const page = document.createElement('article');
    page.className = `cv cv-page classic ${variant} ${firstPage ? '' : 'continuation-page'}`;

    page.innerHTML = `
        ${sidebarHtml(firstPage)}
        <main class="content">
            ${firstPage && namePosition !== 'side' ? `
                <header class="classic-header">
                    <h1>${esc(data.full_name)}</h1>
                    <p>${esc(data.headline)}</p>
                </header>
            ` : ''}
        </main>
    `;

    return page;
}

function paginate() {
    const root = document.getElementById('cv-view');
    const template = cv.template || 'modern';

    const mainOrder = order(data.main_order, 'summary,experience,projects,education,skills,languages,qualities,custom');
    const sideOrder = order(data.side_order, 'details,skills,languages');

    const blocks = mainOrder
        .filter(section => template === 'modern' || !sideOrder.includes(section))
        .flatMap(sectionBlocks);

    root.innerHTML = '';

    let page = template === 'modern' ? createModernPage(true) : createClassicPage(true);
    root.appendChild(page);

    let area = template === 'modern' ? page : page.querySelector('.content');

    blocks.forEach(blockHtml => {
        const wrapper = document.createElement('template');
        wrapper.innerHTML = blockHtml.trim();
        const block = wrapper.content.firstElementChild;

        area.appendChild(block);

        if (area.scrollHeight > area.clientHeight) {
            block.remove();

            page = template === 'modern' ? createModernPage(false) : createClassicPage(false);
            root.appendChild(page);

            area = template === 'modern' ? page : page.querySelector('.content');
            area.appendChild(block);
        }
    });
}

paginate();