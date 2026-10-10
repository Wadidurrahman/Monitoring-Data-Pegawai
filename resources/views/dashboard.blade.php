
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cek Pendataan Sensus Ekonomi ASN</title>
    @vite('resources/css/app.css')
</head>
<body>
<div class="app">
    <header class="topbar">
        <div class="shell topbar-inner">
            <div>
                <h1 class="brand-title">Cek Pendataan Sensus Ekonomi ASN</h1>
                <p class="brand-subtitle">Pengecekan status pendataan Sensus Ekonomi bagi ASN</p>
            </div>
            <div class="topbar-meta">
                <span class="online-dot"></span>
                <span>Sistem aktif</span>
                <span>•</span>
                <span>{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="shell">
            <section class="check-panel">
                <div class="panel-heading">
                    <h2>Cek Status Pendataan</h2>
                    <p>Masukkan NIK, NIP, dan pilih instansi untuk mengecek status pendataan Sensus Ekonomi ASN.</p>
                </div>

                <form id="checkForm" autocomplete="off">
                    <div class="form-group">
                        <label class="form-label" for="nik">
                            <span>NIK</span>
                            <span class="form-label-meta">16 digit</span>
                        </label>
                        <input class="input" id="nik" name="nik" type="text" inputmode="numeric" maxlength="16" pattern="[0-9]{16}" placeholder="Masukkan 16 digit NIK" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="nip">
                            <span>NIP (Nomor Induk Pegawai)</span>
                            <span class="form-label-meta">18 digit</span>
                        </label>
                        <input class="input" id="nip" name="nip" type="text" inputmode="numeric" maxlength="18" pattern="[0-9]{18}" placeholder="Masukkan 18 digit NIP" required disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="institutionSearch">
                            <span>Instansi</span>
                            <span class="form-label-meta" id="institutionModeLabel">Pilih instansi</span>
                        </label>
                        <div class="institution-picker" id="institutionPicker">
                            <div class="institution-select" id="institutionSelect">
                                <input class="input" id="institutionSearch" type="text" autocomplete="off" placeholder="Isi NIP terlebih dahulu" disabled>
                                <span class="institution-chevron">⌄</span>
                                <div class="institution-menu" id="institutionMenu"></div>
                            </div>
                            <div class="institution-other" id="institutionOther">
                                <div class="institution-other-box">
                                    <input class="input" id="institutionOtherInput" type="text" maxlength="255" autocomplete="off" placeholder="Masukkan nama instansi" disabled>
                                    <button class="institution-back" id="institutionBack" type="button">← Kembali ke daftar instansi</button>
                                </div>
                            </div>
                            <input type="hidden" id="institution" name="institution">
                        </div>
                    </div>

                    <button class="btn btn-primary btn-block" id="checkButton" type="submit" disabled>Cek NIK</button>
                </form>

                <div class="security-note">
                    <span class="lock"></span>
                    <span>NIK dilindungi dan tidak ditampilkan secara penuh.</span>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="shell footer-inner">
            <span>© {{ now()->year }} Badan Pusat Statistik Kota Probolinggo</span>
        </div>
    </footer>
</div>

<div class="modal" id="resultModal" aria-hidden="true">
    <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="resultTitle">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <span class="status-icon success" id="resultIcon">✓</span>
                <h3 class="modal-title" id="resultTitle">Hasil Pengecekan</h3>
            </div>
            <button class="modal-close" type="button" data-close-modal="resultModal" aria-label="Tutup">×</button>
        </div>
        <div class="modal-body">
            <p class="modal-message" id="resultMessage"></p>
            <div class="detail-list" id="resultDetails"></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="button" id="resultAction" data-close-modal="resultModal">Selesai</button>
        </div>
    </div>
</div>

<div class="modal" id="notFoundModal" aria-hidden="true">
    <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="notFoundTitle">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <span class="status-icon warning">!</span>
                <h3 class="modal-title" id="notFoundTitle">Lengkapi Data</h3>
            </div>
            <button class="modal-close" type="button" data-close-modal="notFoundModal" aria-label="Tutup">×</button>
        </div>
        <div class="modal-body">
            <p class="modal-message">NIK tidak ditemukan pada data master. Silakan lengkapi data yang dibutuhkan untuk melanjutkan pendataan.</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="button" id="openManualFormButton">Lengkapi Data</button>
        </div>
    </div>
</div>

<div class="modal" id="manualModal" aria-hidden="true">
    <div class="modal-dialog large" role="dialog" aria-modal="true" aria-labelledby="manualTitle">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <span class="status-icon warning">!</span>
                <h3 class="modal-title" id="manualTitle">Lengkapi Data</h3>
            </div>
            <button class="modal-close" type="button" data-close-modal="manualModal" aria-label="Tutup">×</button>
        </div>

        <form id="manualForm" autocomplete="off">
            <div class="modal-body">
                <p class="manual-intro">Lengkapi informasi berikut dengan data yang sesuai untuk melanjutkan pendataan.</p>
                <div class="error-box" id="manualError" style="display:none"></div>

                <input type="hidden" id="registration_token" name="registration_token">

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="manual_nik">NIK</label>
                        <input class="input" id="manual_nik" type="text" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="manual_institution">Instansi</label>
                        <input class="input" id="manual_institution" type="text" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap</label>
                        <input class="input" id="name" name="name" type="text" maxlength="255" placeholder="Masukkan nama lengkap" autocomplete="name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="manual_nip">
                            <span>NIP (Nomor Induk Pegawai)</span>
                            <span class="form-label-meta">18 digit</span>
                        </label>
                        <input class="input" id="manual_nip" name="nip" type="text" inputmode="numeric" maxlength="18" pattern="[0-9]{18}" readonly required>
                    </div>

                    <div class="form-group span-2">
                        <label class="form-label" for="manual_email">
                            <span>Alamat Gmail</span>
                            <span class="form-label-meta">Wajib</span>
                        </label>
                        <input class="input" id="manual_email" name="email" type="email" maxlength="254" placeholder="nama@gmail.com" autocomplete="email" required disabled>
                        <p class="manual-intro">Gunakan Gmail aktif agar informasi pendataan dapat dikirim ke alamat yang tepat.</p>
                    </div>

                    <div class="form-group span-2">
                        <label class="email-consent" for="email_consent" style="display:flex;align-items:flex-start;gap:10px;font-size:13px;line-height:1.6;color:#475569;cursor:pointer">
                            <input id="email_consent" name="email_consent" type="checkbox" value="1" style="margin-top:4px;flex-shrink:0;accent-color:#2563eb">
                            <span>Saya bersedia menerima pemberitahuan dan pengingat terkait pendataan melalui email.</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="provinceSearch">Provinsi</label>
                        <div class="region-picker" id="provincePicker">
                            <div class="region-select">
                                <input class="input region-search" id="provinceSearch" type="text" autocomplete="off" placeholder="Isi Gmail terlebih dahulu" disabled>
                                <span class="region-chevron">⌄</span>
                                <div class="region-menu" id="provinceMenu"></div>
                            </div>
                            <input type="hidden" id="province_name" name="province_name">
                            <input type="hidden" id="province_code">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="regencySearch">Kabupaten / Kota</label>
                        <div class="region-picker" id="regencyPicker">
                            <div class="region-select">
                                <input class="input region-search" id="regencySearch" type="text" autocomplete="off" placeholder="Pilih provinsi dahulu" disabled>
                                <span class="region-chevron">⌄</span>
                                <div class="region-menu" id="regencyMenu"></div>
                            </div>
                            <input type="hidden" id="regency_name" name="regency_name">
                            <input type="hidden" id="regency_code">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="districtSearch">Kecamatan</label>
                        <div class="region-picker" id="districtPicker">
                            <div class="region-select">
                                <input class="input region-search" id="districtSearch" type="text" autocomplete="off" placeholder="Pilih kabupaten / kota dahulu" disabled>
                                <span class="region-chevron">⌄</span>
                                <div class="region-menu" id="districtMenu"></div>
                            </div>
                            <input type="hidden" id="district_name" name="district_name">
                            <input type="hidden" id="district_code">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="villageSearch">Kelurahan / Desa</label>
                        <div class="region-picker" id="villagePicker">
                            <div class="region-select">
                                <input class="input region-search" id="villageSearch" type="text" autocomplete="off" placeholder="Pilih kecamatan dahulu" disabled>
                                <span class="region-chevron">⌄</span>
                                <div class="region-menu" id="villageMenu"></div>
                            </div>
                            <input type="hidden" id="village_name" name="village_name">
                            <input type="hidden" id="village_code">
                        </div>
                    </div>

                    <div class="form-group span-2">
                        <div class="address-grid">
                            <div class="form-group">
                                <label class="form-label" for="rw">RW</label>
                                <input class="input" id="rw" name="rw" type="text" inputmode="numeric" maxlength="3" autocomplete="off" placeholder="Contoh: 001" disabled required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="rt">RT</label>
                                <input class="input" id="rt" name="rt" type="text" inputmode="numeric" maxlength="3" autocomplete="off" placeholder="Contoh: 001" disabled required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-close-modal="manualModal">Batal</button>
                <button class="btn btn-primary" type="submit" id="saveButton" disabled>Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
const $ = id => document.getElementById(id);
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const rawInstitutions = @json($institutions ?? []);
const institutions = Array.isArray(rawInstitutions) ? rawInstitutions : Object.values(rawInstitutions);
const checkForm = $('checkForm');
const manualForm = $('manualForm');
const checkButton = $('checkButton');
const saveButton = $('saveButton');
const institutionSearch = $('institutionSearch');
const institutionInput = $('institution');
const institutionOtherInput = $('institutionOtherInput');
const institutionMenu = $('institutionMenu');
let institutionOtherMode = false;
let institutionResults = [];
let institutionActiveIndex = -1;
let checking = false;
let saving = false;

const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
}[character]));

const normalizeText = value => String(value ?? '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/\s+/g, ' ')
    .trim()
    .toLowerCase();

const openModal = id => {
    const modal = $(id);
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
};

const closeModal = id => {
    const modal = $(id);
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
};

const setButtonLoading = (button, loading, normal, busy) => {
    button.disabled = loading;
    button.innerHTML = loading ? `<span class="spinner"></span> ${busy}` : normal;
};

const detailRow = (label, value) => `
    <div class="detail-row">
        <div class="detail-label">${escapeHtml(label)}</div>
        <div class="detail-value">${escapeHtml(value ?? '-')}</div>
    </div>
`;

const showResultModal = ({ type = 'success', title, message, details = '', focusAfterClose = '' }) => {
    $('resultIcon').className = `status-icon ${type}`;
    $('resultIcon').textContent = type === 'success' ? '✓' : '!';
    $('resultTitle').textContent = title;
    $('resultMessage').textContent = message;
    $('resultDetails').innerHTML = details;
    $('resultDetails').style.display = details ? 'block' : 'none';
    $('resultAction').dataset.focus = focusAfterClose;
    openModal('resultModal');
};

const readJson = async response => {
    try {
        return await response.json();
    } catch {
        return { message: 'Respons server tidak dapat dibaca.' };
    }
};

const getInstitutionResults = value => {
    const query = normalizeText(value);
    const words = query.split(' ').filter(Boolean);

    const relevance = item => {
        const name = normalizeText(item.name);
        const alias = normalizeText(item.alias);

        if (!query) return 4;
        if (name === query || alias === query) return 0;
        if (name.startsWith(query) || alias.startsWith(query)) return 1;
        if (name.split(' ').some(word => word.startsWith(query))) return 2;

        return 3;
    };

    return institutions
        .filter(item => words.every(word =>
            normalizeText(`${item.name ?? ''} ${item.alias ?? ''}`).includes(word)
        ))
        .sort((a, b) => relevance(a) - relevance(b) ||
            String(a.name).localeCompare(String(b.name), 'id')
        );
};

const closeInstitutionMenu = () => {
    institutionMenu.classList.remove('open', 'drop-up', 'drop-down');
};

const positionInstitutionMenu = () => {
    if (!institutionMenu.classList.contains('open')) return;

    const rect = institutionSearch.getBoundingClientRect();
    const viewport = window.visualViewport;
    const viewportHeight = viewport ? viewport.height + viewport.offsetTop : window.innerHeight;
    const bottom = viewportHeight - rect.bottom - 8;
    const top = rect.top - (viewport ? viewport.offsetTop : 0) - 8;
    const openUp = bottom < 155 && top > bottom;

    institutionMenu.classList.remove('drop-up', 'drop-down');
    institutionMenu.classList.add(openUp ? 'drop-up' : 'drop-down');

    const list = institutionMenu.querySelector('.institution-list');

    if (list) {
        list.style.maxHeight = `${Math.max(72, Math.min(160, (openUp ? top : bottom) - 35))}px`;
    }
};

const renderInstitutionMenu = () => {
    if (institutionSearch.disabled) return;

    institutionResults = getInstitutionResults(institutionSearch.value);
    institutionActiveIndex = -1;

    institutionMenu.innerHTML = `
        <div class="institution-list${institutionResults.length ? '' : ' is-empty'}">
            ${institutionResults.map((item, index) => `
                <button class="institution-option" type="button" data-institution-index="${index}">
                    <span class="institution-option-name">${escapeHtml(item.name)}</span>
                    ${item.alias ? `<span class="institution-option-alias">${escapeHtml(item.alias)}</span>` : ''}
                </button>
            `).join('') || '<div class="institution-empty">Tidak ada instansi yang cocok.</div>'}
        </div>
        <div class="institution-other-option" role="button" tabindex="0" data-institution-other="true">
            <span class="institution-other-icon">+</span>
            <span>Instansi Lainnya</span>
        </div>
    `;

    institutionMenu.classList.add('open');
    requestAnimationFrame(positionInstitutionMenu);
};

const selectInstitution = item => {
    institutionSearch.value = item.name;
    institutionInput.value = item.name;
    institutionSearch.setCustomValidity('');
    closeInstitutionMenu();
    updateCheckSequence();
};

const activateInstitutionOther = () => {
    institutionOtherMode = true;
    institutionInput.value = '';
    institutionSearch.value = '';
    institutionOtherInput.value = '';

    closeInstitutionMenu();

    $('institutionSelect').classList.add('hidden');
    $('institutionOther').classList.add('open');
    $('institutionModeLabel').textContent = 'Instansi lainnya';

    updateCheckSequence();
    setTimeout(() => institutionOtherInput.focus(), 60);
};

const resetInstitutionPicker = () => {
    institutionOtherMode = false;
    institutionInput.value = '';
    institutionSearch.value = '';
    institutionOtherInput.value = '';
    institutionSearch.setCustomValidity('');
    institutionOtherInput.setCustomValidity('');

    $('institutionOther').classList.remove('open');
    $('institutionSelect').classList.remove('hidden');
    $('institutionModeLabel').textContent = 'Pilih instansi';

    closeInstitutionMenu();
};

const updateCheckSequence = () => {
    const nik = $('nik').value.replace(/\D/g, '').slice(0, 16);
    const nip = $('nip').value.replace(/\D/g, '').slice(0, 18);
    const nikValid = nik.length === 16;
    const nipValid = nikValid && nip.length === 18;

    if ($('nip').disabled !== !nikValid) {
        $('nip').disabled = !nikValid;
    }

    if (!nikValid) {
        $('nip').value = '';
    }

    if (!nipValid && (institutionInput.value || institutionSearch.value || institutionOtherMode)) {
        resetInstitutionPicker();
    }

    institutionSearch.disabled = !nipValid || institutionOtherMode;
    institutionOtherInput.disabled = !nipValid || !institutionOtherMode;
    $('institutionPicker').style.opacity = nipValid ? '1' : '.55';

    const institutionValid = institutionOtherMode
        ? institutionOtherInput.value.trim().length > 0
        : institutions.some(item => normalizeText(item.name) === normalizeText(institutionInput.value));

    checkButton.disabled = checking || !nipValid || !institutionValid;
};

institutionSearch.addEventListener('focus', renderInstitutionMenu);
institutionSearch.addEventListener('click', renderInstitutionMenu);

institutionSearch.addEventListener('input', () => {
    institutionInput.value = '';
    institutionSearch.setCustomValidity('');
    renderInstitutionMenu();
    updateCheckSequence();
});

institutionSearch.addEventListener('keydown', event => {
    const options = [...institutionMenu.querySelectorAll('.institution-option')];

    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();

        if (!institutionMenu.classList.contains('open')) {
            renderInstitutionMenu();
            return;
        }

        if (!options.length) return;

        institutionActiveIndex = event.key === 'ArrowDown'
            ? (institutionActiveIndex + 1) % options.length
            : (institutionActiveIndex + options.length - 1) % options.length;

        options.forEach((option, index) =>
            option.classList.toggle('active', index === institutionActiveIndex)
        );

        options[institutionActiveIndex]?.scrollIntoView({ block: 'nearest' });
    }

    if (event.key === 'Enter' && institutionActiveIndex >= 0) {
        event.preventDefault();

        if (institutionResults[institutionActiveIndex]) {
            selectInstitution(institutionResults[institutionActiveIndex]);
        }
    }

    if (event.key === 'Escape') closeInstitutionMenu();
});

institutionMenu.addEventListener('pointerdown', event => {
    const option = event.target.closest('[data-institution-index]');
    const other = event.target.closest('[data-institution-other]');

    if (!option && !other) return;

    event.preventDefault();

    if (option) {
        const item = institutionResults[Number(option.dataset.institutionIndex)];
        if (item) selectInstitution(item);
    } else {
        activateInstitutionOther();
    }
});

institutionMenu.addEventListener('keydown', event => {
    if (
        event.target.closest('[data-institution-other]') &&
        (event.key === 'Enter' || event.key === ' ')
    ) {
        event.preventDefault();
        activateInstitutionOther();
    }
});

institutionOtherInput.addEventListener('input', () => {
    institutionOtherInput.setCustomValidity('');
    institutionInput.value = institutionOtherInput.value.replace(/\s+/g, ' ').trimStart();
    updateCheckSequence();
});

$('institutionBack').addEventListener('pointerdown', event => {
    event.preventDefault();
    resetInstitutionPicker();
    updateCheckSequence();
    institutionSearch.focus();
});

const positionRegionMenu = (search, menu) => {
    if (!menu.classList.contains('open')) return;

    const rect = search.getBoundingClientRect();
    const viewport = window.visualViewport;
    const viewportHeight = viewport ? viewport.height + viewport.offsetTop : window.innerHeight;
    const bottom = viewportHeight - rect.bottom - 8;
    const top = rect.top - (viewport ? viewport.offsetTop : 0) - 8;
    const openUp = bottom < 125 && top > bottom;

    menu.classList.remove('drop-up', 'drop-down');
    menu.classList.add(openUp ? 'drop-up' : 'drop-down');

    const list = menu.querySelector('.region-list');

    if (list) {
        list.style.maxHeight = `${Math.max(72, Math.min(160, (openUp ? top : bottom) - 10))}px`;
    }
};

const fetchRegionJson = async url => {
    const response = await fetch(url, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin'
    });

    const data = await readJson(response);

    if (!response.ok) throw new Error(data.message || 'Data wilayah gagal dimuat.');

    return Array.isArray(data) ? data : [];
};

const createRegionPicker = config => {
    const search = $(config.searchId);
    const menu = $(config.menuId);
    const value = $(config.valueId);
    const code = $(config.codeId);

    let items = [];
    let results = [];
    let activeIndex = -1;
    let loading = false;

    const close = () => menu.classList.remove('open', 'drop-up', 'drop-down');

    const reset = () => {
        search.value = '';
        value.value = '';
        code.value = '';
        search.setCustomValidity('');
        close();
    };

    const setItems = list => {
        items = Array.isArray(list) ? list : [];
    };

    const setDisabled = (disabled, placeholder) => {
        if (disabled && !search.disabled) reset();
        search.disabled = disabled;
        if (placeholder) search.placeholder = placeholder;
    };

    const filtered = () => {
        const query = normalizeText(search.value);

        return items
            .filter(item => !query || query.split(' ').every(word =>
                normalizeText(`${item.name ?? ''} ${item.alias ?? ''}`).includes(word)
            ))
            .sort((a, b) => String(a.name).localeCompare(String(b.name), 'id'))
            .slice(0, 30);
    };

    const choose = item => {
        search.value = String(item.name ?? '');
        value.value = String(item.name ?? '');
        code.value = String(item.id ?? item.code ?? '');

        search.setCustomValidity('');
        close();
        config.onSelect?.();
        updateManualSequence();
    };

    const render = () => {
        results = filtered();
        activeIndex = -1;

        menu.innerHTML = results.length
            ? `<div class="region-list">
                ${results.map((item, index) => `
                    <div class="region-option" role="button" tabindex="-1" data-region-index="${index}">
                        <span class="region-option-name">${escapeHtml(item.name)}</span>
                    </div>
                `).join('')}
            </div>`
            : `<div class="region-empty">${escapeHtml(config.emptyText || 'Data tidak ditemukan.')}</div>`;

        menu.classList.add('open');
        requestAnimationFrame(() => positionRegionMenu(search, menu));
    };

    const open = async () => {
        if (search.disabled || loading) return;

        try {
            if (config.onOpen) {
                loading = true;
                menu.innerHTML = '<div class="region-loading"><span class="spinner"></span> Memuat data...</div>';
                menu.classList.add('open');
                await config.onOpen();
                loading = false;
            }

            render();
        } catch (error) {
            loading = false;
            menu.innerHTML = `<div class="region-empty">${escapeHtml(error.message)}</div>`;
            menu.classList.add('open');
        }
    };

    search.addEventListener('focus', open);
    search.addEventListener('click', open);

    search.addEventListener('input', () => {
        value.value = '';
        code.value = '';
        search.setCustomValidity('');
        if (items.length) render();
        else open();
        updateManualSequence();
    });

    search.addEventListener('keydown', event => {
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();

            if (!menu.classList.contains('open')) {
                open();
                return;
            }

            if (!results.length) return;

            activeIndex = event.key === 'ArrowDown'
                ? (activeIndex + 1) % results.length
                : (activeIndex + results.length - 1) % results.length;

            menu.querySelectorAll('.region-option').forEach((option, index) =>
                option.classList.toggle('active', index === activeIndex)
            );
        }

        if (event.key === 'Enter' && activeIndex >= 0 && results[activeIndex]) {
            event.preventDefault();
            choose(results[activeIndex]);
        }

        if (event.key === 'Escape') close();
    });

    menu.addEventListener('pointerdown', event => {
        const option = event.target.closest('[data-region-index]');
        if (!option) return;

        event.preventDefault();

        const item = results[Number(option.dataset.regionIndex)];
        if (item) choose(item);
    });

    document.addEventListener('pointerdown', event => {
        if (event.target !== search && !menu.contains(event.target)) close();
    });

    return {
        reset,
        setItems,
        setDisabled,
        close,
        getValue: () => value.value,
        getCode: () => code.value,
        getSearch: () => search,
        commitExact: () => {
            const item = items.find(item =>
                normalizeText(item.name) === normalizeText(search.value)
            );

            if (!item) return false;
            choose(item);
            return true;
        }
    };
};

let provinceLoaded = false;
let regenciesParent = '';
let districtsParent = '';
let villagesParent = '';

const provincePicker = createRegionPicker({
    searchId: 'provinceSearch',
    menuId: 'provinceMenu',
    valueId: 'province_name',
    codeId: 'province_code',
    emptyText: 'Provinsi tidak ditemukan.',
    onOpen: async () => {
        if (provinceLoaded) return;
        const data = await fetchRegionJson('/wilayah/provinsi');
        provincePicker.setItems(data);
        provinceLoaded = true;
    },
    onSelect: () => {
        regenciesParent = '';
        districtsParent = '';
        villagesParent = '';
        regencyPicker.setItems([]);
        districtPicker.setItems([]);
        villagePicker.setItems([]);
        regencyPicker.reset();
        districtPicker.reset();
        villagePicker.reset();
        $('rw').value = '';
        $('rt').value = '';
    }
});

const regencyPicker = createRegionPicker({
    searchId: 'regencySearch',
    menuId: 'regencyMenu',
    valueId: 'regency_name',
    codeId: 'regency_code',
    emptyText: 'Kabupaten / kota tidak ditemukan.',
    onOpen: async () => {
        const code = provincePicker.getCode();
        if (!code || regenciesParent === code) return;

        const data = await fetchRegionJson(`/wilayah/kabupaten-kota?province=${encodeURIComponent(code)}`);
        regencyPicker.setItems(data);
        regenciesParent = code;
    },
    onSelect: () => {
        districtsParent = '';
        villagesParent = '';
        districtPicker.setItems([]);
        villagePicker.setItems([]);
        districtPicker.reset();
        villagePicker.reset();
        $('rw').value = '';
        $('rt').value = '';
    }
});

const districtPicker = createRegionPicker({
    searchId: 'districtSearch',
    menuId: 'districtMenu',
    valueId: 'district_name',
    codeId: 'district_code',
    emptyText: 'Kecamatan tidak ditemukan.',
    onOpen: async () => {
        const code = regencyPicker.getCode();
        if (!code || districtsParent === code) return;

        const data = await fetchRegionJson(`/wilayah/kecamatan?regency=${encodeURIComponent(code)}`);
        districtPicker.setItems(data);
        districtsParent = code;
    },
    onSelect: () => {
        villagesParent = '';
        villagePicker.setItems([]);
        villagePicker.reset();
        $('rw').value = '';
        $('rt').value = '';
    }
});

const villagePicker = createRegionPicker({
    searchId: 'villageSearch',
    menuId: 'villageMenu',
    valueId: 'village_name',
    codeId: 'village_code',
    emptyText: 'Kelurahan / desa tidak ditemukan.',
    onOpen: async () => {
        const code = districtPicker.getCode();
        if (!code || villagesParent === code) return;

        const data = await fetchRegionJson(`/wilayah/kelurahan?district=${encodeURIComponent(code)}`);
        villagePicker.setItems(data);
        villagesParent = code;
    }
});

const resetRegionForm = () => {
    regenciesParent = '';
    districtsParent = '';
    villagesParent = '';

    provincePicker.reset();
    regencyPicker.reset();
    districtPicker.reset();
    villagePicker.reset();

    regencyPicker.setItems([]);
    districtPicker.setItems([]);
    villagePicker.setItems([]);

    $('rw').value = '';
    $('rt').value = '';

    updateManualSequence();
};

const updateManualSequence = () => {
    const nameValid = $('name').value.trim().length > 0;

    $('manual_email').disabled = !nameValid;

    if (!nameValid) {
        $('manual_email').value = '';
        $('email_consent').checked = false;
    }

    const email = $('manual_email').value.trim().toLowerCase();
    const emailValid = nameValid && /^[^\s@]+@gmail\.com$/.test(email);

    $('email_consent').disabled = !emailValid;

    provincePicker.setDisabled(!emailValid, emailValid ? 'Cari atau pilih provinsi' : 'Isi Gmail terlebih dahulu');

    const provinceValid = emailValid && Boolean(provincePicker.getValue());
    regencyPicker.setDisabled(!provinceValid, provinceValid ? 'Cari atau pilih kabupaten / kota' : 'Pilih provinsi dahulu');

    const regencyValid = provinceValid && Boolean(regencyPicker.getValue());
    districtPicker.setDisabled(!regencyValid, regencyValid ? 'Cari atau pilih kecamatan' : 'Pilih kabupaten / kota dahulu');

    const districtValid = regencyValid && Boolean(districtPicker.getValue());
    villagePicker.setDisabled(!districtValid, districtValid ? 'Cari atau pilih kelurahan / desa' : 'Pilih kecamatan dahulu');

    const villageValid = districtValid && Boolean(villagePicker.getValue());

    $('rw').disabled = !villageValid;

    if (!villageValid) $('rw').value = '';

    const rwValid = villageValid && /^[0-9]{1,3}$/.test($('rw').value.trim());

    $('rt').disabled = !rwValid;

    if (!rwValid) $('rt').value = '';

    const rtValid = rwValid && /^[0-9]{1,3}$/.test($('rt').value.trim());

    saveButton.disabled = saving || !(
        $('registration_token').value &&
        /^[0-9]{18}$/.test($('manual_nip').value) &&
        nameValid &&
        emailValid &&
        provinceValid &&
        regencyValid &&
        districtValid &&
        villageValid &&
        rtValid
    );
};

const validateRegionForm = () => {
    const required = [
        [provincePicker, 'Silakan pilih provinsi dari daftar.'],
        [regencyPicker, 'Silakan pilih kabupaten / kota dari daftar.'],
        [districtPicker, 'Silakan pilih kecamatan dari daftar.'],
        [villagePicker, 'Silakan pilih kelurahan / desa dari daftar.']
    ];

    for (const [picker, message] of required) {
        if (!picker.getValue() && !picker.commitExact()) {
            picker.getSearch().setCustomValidity(message);
            picker.getSearch().reportValidity();
            picker.getSearch().focus();
            return false;
        }
    }

    return true;
};

$('nik').addEventListener('input', event => {
    event.target.value = event.target.value.replace(/\D/g, '').slice(0, 16);
    event.target.setCustomValidity('');
    updateCheckSequence();
});

$('nip').addEventListener('input', event => {
    event.target.value = event.target.value.replace(/\D/g, '').slice(0, 18);
    event.target.setCustomValidity('');
    updateCheckSequence();
});

$('name').addEventListener('input', updateManualSequence);

$('manual_email').addEventListener('input', event => {
    event.target.setCustomValidity('');
    updateManualSequence();
});

for (const id of ['rw', 'rt']) {
    $(id).addEventListener('input', event => {
        event.target.value = event.target.value.replace(/\D/g, '').slice(0, 3);
        updateManualSequence();
    });

    $(id).addEventListener('blur', event => {
        const value = event.target.value.replace(/\D/g, '').slice(0, 3);
        event.target.value = value ? value.padStart(3, '0') : '';
        updateManualSequence();
    });
}

$('email_consent').addEventListener('change', updateManualSequence);

document.addEventListener('pointerdown', event => {
    if (!$('institutionPicker').contains(event.target)) closeInstitutionMenu();
});

document.querySelectorAll('[data-close-modal]').forEach(button => {
    button.addEventListener('click', () => {
        closeModal(button.dataset.closeModal);

        if (button.dataset.focus) {
            setTimeout(() => $(button.dataset.focus)?.focus(), 70);
        }
    });
});

document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('pointerdown', event => {
        if (event.target === modal) closeModal(modal.id);
    });
});

document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;

    closeInstitutionMenu();
    document.querySelectorAll('.region-menu.open').forEach(menu =>
        menu.classList.remove('open', 'drop-up', 'drop-down')
    );

    document.querySelectorAll('.modal.open').forEach(modal => closeModal(modal.id));
});

window.addEventListener('resize', positionInstitutionMenu);

$('openManualFormButton').addEventListener('click', () => {
    closeModal('notFoundModal');

    $('manualError').style.display = 'none';
    $('manualError').textContent = '';
    $('name').value = '';
    $('manual_email').value = '';
    $('email_consent').checked = false;

    resetRegionForm();
    updateManualSequence();

    openModal('manualModal');
    setTimeout(() => $('name').focus(), 90);
});

checkForm.addEventListener('submit', async event => {
    event.preventDefault();
    if (checking) return;

    updateCheckSequence();

    const nik = $('nik').value.replace(/\D/g, '');
    const nip = $('nip').value.replace(/\D/g, '');

    if (!/^[0-9]{16}$/.test(nik)) {
        $('nik').setCustomValidity('NIK harus terdiri dari 16 digit.');
        $('nik').reportValidity();
        return;
    }

    if (!/^[0-9]{18}$/.test(nip)) {
        $('nip').setCustomValidity('NIP harus terdiri dari 18 digit.');
        $('nip').reportValidity();
        return;
    }

    if (institutionOtherMode) {
        institutionInput.value = institutionOtherInput.value.replace(/\s+/g, ' ').trim();
    } else {
        const item = institutions.find(item =>
            normalizeText(item.name) === normalizeText(institutionSearch.value) ||
            normalizeText(item.alias) === normalizeText(institutionSearch.value)
        );

        if (!item) {
            institutionSearch.setCustomValidity('Pilih instansi dari daftar atau gunakan Instansi Lainnya.');
            institutionSearch.reportValidity();
            return;
        }

        institutionInput.value = item.name;
    }

    if (!institutionInput.value.trim()) return;

    checking = true;
    setButtonLoading(checkButton, true, 'Cek NIK', 'Memeriksa');

    try {
        const response = await fetch('/cek-unit', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                nik,
                nip,
                institution: institutionInput.value.trim()
            })
        });

        const result = await readJson(response);

        if (!response.ok) {
            const message = result.errors
                ? Object.values(result.errors).flat().join(' ')
                : result.message || 'Pengecekan gagal.';

            showResultModal({
                type: 'error',
                title: 'Pengecekan Gagal',
                message
            });

            return;
        }

        if (result.result === 'found') {
            const sudah = result.data?.status === 'Sudah Didata';

            showResultModal({
                type: sudah ? 'success' : 'warning',
                title: sudah ? 'Data Sudah Didata' : 'Data Belum Didata',
                message: result.message,
                details:
                    detailRow('NIK', result.data?.masked_nik) +
                    detailRow('Nama', result.data?.name) +
                    detailRow('Status', result.data?.status)
            });

            return;
        }

        if (result.result === 'manual_found') {
            showResultModal({
                type: 'success',
                title: 'Data Sudah Dikirim',
                message: result.message,
                details:
                    detailRow('NIK', result.data?.masked_nik) +
                    detailRow('Instansi', result.data?.institution)
            });

            return;
        }

        if (result.result === 'not_found') {
            $('registration_token').value = result.data.registration_token;
            $('manual_nik').value = result.data.masked_nik;
            $('manual_institution').value = result.data.institution;
            $('manual_nip').value = nip;
            openModal('notFoundModal');
            return;
        }

        showResultModal({
            type: 'error',
            title: 'Pengecekan Gagal',
            message: 'Respons server tidak dikenali.'
        });
    } catch {
        showResultModal({
            type: 'error',
            title: 'Koneksi Bermasalah',
            message: 'Tidak dapat menghubungi server. Silakan coba kembali.'
        });
    } finally {
        checking = false;
        checkButton.innerHTML = 'Cek NIK';
        updateCheckSequence();
    }
});

manualForm.addEventListener('submit', async event => {
    event.preventDefault();
    if (saving) return;

    $('manualError').style.display = 'none';
    $('manualError').textContent = '';

    const email = $('manual_email');
    email.value = email.value.trim().toLowerCase();

    if (!/^[^\s@]+@gmail\.com$/.test(email.value)) {
        email.setCustomValidity('Gunakan Gmail dengan domain @gmail.com.');
        email.reportValidity();
        email.focus();
        return;
    }

    if (!validateRegionForm()) return;

    updateManualSequence();

    if (saveButton.disabled) return;

    saving = true;
    setButtonLoading(saveButton, true, 'Simpan Data', 'Menyimpan');

    try {
        const payload = Object.fromEntries(new FormData(manualForm).entries());

        const response = await fetch('/pendaftaran-manual', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(payload)
        });

        const result = await readJson(response);

        if (!response.ok) {
            $('manualError').textContent = result.errors
                ? Object.values(result.errors).flat().join(' ')
                : result.message || 'Data gagal disimpan.';

            $('manualError').style.display = 'block';
            return;
        }

        closeModal('manualModal');

        checkForm.reset();
        manualForm.reset();

        resetInstitutionPicker();
        resetRegionForm();
        updateCheckSequence();

        showResultModal({
            type: 'success',
            title: 'Data Berhasil Disimpan',
            message: result.message || 'Data Anda berhasil disimpan.',
            details:
                detailRow('Nama', result.data?.name) +
                detailRow('Instansi', result.data?.institution),
            focusAfterClose: 'nik'
        });
    } catch {
        $('manualError').textContent = 'Terjadi kesalahan saat menyimpan data. Silakan coba kembali.';
        $('manualError').style.display = 'block';
    } finally {
        saving = false;
        saveButton.innerHTML = 'Simpan Data';
        updateManualSequence();
    }
});

updateCheckSequence();
updateManualSequence();
</script>
</body>
</html>
