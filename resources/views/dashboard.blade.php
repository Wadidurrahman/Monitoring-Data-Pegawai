
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
                        <input class="input" id="nip" name="nip" type="text" inputmode="numeric" maxlength="18" pattern="[0-9]{18}" placeholder="Masukkan 18 digit NIP" disabled required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="institutionSearch">
                            <span>Instansi</span>
                            <span class="form-label-meta" id="institutionModeLabel">Pilih instansi</span>
                        </label>

                        <div class="institution-picker" id="institutionPicker">
                            <div class="institution-select" id="institutionSelect">
                                <input class="input" id="institutionSearch" type="text" autocomplete="off" placeholder="Cari atau pilih instansi" disabled>
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
                <h3 class="modal-title" id="resultTitle">Data Ditemukan</h3>
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
                <p class="manual-intro">Lengkapi informasi berikut secara berurutan untuk melanjutkan pendataan.</p>
                <div class="error-box" id="manualError"></div>
                <input type="hidden" id="registration_token" name="registration_token">

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="manual_nik">
                            <span>NIK</span>
                        </label>
                        <input class="input" id="manual_nik" type="text" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="manual_institution">
                            <span>Instansi</span>
                        </label>
                        <input class="input" id="manual_institution" type="text" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="name">
                            <span>Nama Lengkap</span>
                        </label>
                        <input class="input" id="name" name="name" type="text" maxlength="255" placeholder="Masukkan nama lengkap" autocomplete="name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="manual_nip">
                            <span>NIP (Nomor Induk Pegawai)</span>
                            <span class="form-label-meta">18 digit</span>
                        </label>
                        <input class="input" id="manual_nip" name="nip" type="text" inputmode="numeric" maxlength="18" pattern="[0-9]{18}" placeholder="18 digit NIP" readonly required>
                    </div>

                    <div class="form-group span-2">
                        <label class="form-label" for="manual_email">
                            <span>Alamat Gmail</span>
                            <span class="form-label-meta">Wajib</span>
                        </label>
                        <input class="input" id="manual_email" name="email" type="email" maxlength="254" placeholder="nama@gmail.com" autocomplete="email" disabled required>
                        <p class="manual-intro">Gunakan Gmail aktif agar informasi pendataan dapat dikirim ke alamat yang tepat.</p>
                    </div>

                    <div class="form-group span-2">
                        <label class="email-consent" for="email_consent" style="display:flex;align-items:flex-start;gap:10px;font-size:13px;line-height:1.6;color:#475569;cursor:pointer">
                            <input id="email_consent" name="email_consent" type="checkbox" value="1" style="margin-top:4px;flex-shrink:0;accent-color:#2563eb" disabled>
                            <span>Saya bersedia menerima pemberitahuan dan pengingat terkait pendataan melalui email.</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="provinceSearch">
                            <span>Provinsi</span>
                        </label>
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
                        <label class="form-label" for="regencySearch">
                            <span>Kabupaten / Kota</span>
                        </label>
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
                        <label class="form-label" for="districtSearch">
                            <span>Kecamatan</span>
                        </label>
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
                        <label class="form-label" for="villageSearch">
                            <span>Kelurahan / Desa</span>
                        </label>
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
                                <label class="form-label" for="rw">
                                    <span>RW</span>
                                </label>
                                <input class="input" id="rw" name="rw" type="text" inputmode="numeric" maxlength="3" autocomplete="off" placeholder="Contoh: 001" disabled>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="rt">
                                    <span>RT</span>
                                </label>
                                <input class="input" id="rt" name="rt" type="text" inputmode="numeric" maxlength="3" autocomplete="off" placeholder="Isi RW terlebih dahulu" disabled>
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
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const institutions = @json($institutions ?? []);
const el = id => document.getElementById(id);
const checkForm = el('checkForm');
const manualForm = el('manualForm');
const checkButton = el('checkButton');
const saveButton = el('saveButton');
const manualError = el('manualError');
const institutionPicker = el('institutionPicker');
const institutionSearch = el('institutionSearch');
const institutionMenu = el('institutionMenu');
const institutionInput = el('institution');
const institutionOtherInput = el('institutionOtherInput');

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

const toggleInput = (input, enabled, clearOnDisable = true) => {
    if (input.disabled === !enabled) return;

    input.disabled = !enabled;

    if (!enabled && clearOnDisable) {
        if (input.type === 'checkbox') input.checked = false;
        else input.value = '';
    }
};

const setGroupState = (input, enabled) => {
    const group = input.closest('.form-group');

    if (group) {
        group.style.opacity = enabled ? '1' : '0.5';
        group.style.transition = 'opacity 180ms ease';
    }
};

const openModal = id => {
    el(id).classList.add('open');
    el(id).setAttribute('aria-hidden', 'false');
};

const closeModal = id => {
    el(id).classList.remove('open');
    el(id).setAttribute('aria-hidden', 'true');
};

const setButtonLoading = (button, loading, normal, text) => {
    button.disabled = loading;
    button.innerHTML = loading
        ? `<span class="spinner"></span>&nbsp;&nbsp;${text}`
        : normal;
};

const detailRow = (label, value) => `
    <div class="detail-row">
        <div class="detail-label">${escapeHtml(label)}</div>
        <div class="detail-value">${escapeHtml(value)}</div>
    </div>
`;

const showResultModal = ({
    type = 'success',
    title,
    message,
    details = '',
    buttonText = 'Selesai',
    focusAfterClose = null
}) => {
    el('resultIcon').className = `status-icon ${type}`;
    el('resultIcon').textContent = type === 'success' ? '✓' : '!';
    el('resultTitle').textContent = title;
    el('resultMessage').textContent = message;
    el('resultDetails').innerHTML = details;
    el('resultDetails').style.display = details ? 'block' : 'none';
    el('resultAction').textContent = buttonText;
    el('resultAction').dataset.focus = focusAfterClose || '';

    openModal('resultModal');
};

const readJson = async response => {
    try {
        return await response.json();
    } catch {
        return { message: 'Respons server tidak dapat dibaca.' };
    }
};

const institutionMatches = value => {
    const tokens = normalizeText(value).split(' ').filter(Boolean);
    const query = normalizeText(value);

    const rank = item => {
        const name = normalizeText(item.name);
        const alias = normalizeText(item.alias);

        if (!query) return 4;
        if (name === query || alias === query) return 0;
        if (name.startsWith(query) || alias.startsWith(query)) return 1;
        if (name.split(' ').some(word => word.startsWith(query))) return 2;

        return 3;
    };

    return institutions
        .filter(item => tokens.every(token =>
            normalizeText(`${item.name ?? ''} ${item.alias ?? ''}`).includes(token)
        ))
        .sort((a, b) =>
            rank(a) - rank(b) ||
            String(a.name).localeCompare(String(b.name), 'id')
        );
};

const closeInstitutionMenu = () => {
    institutionMenu.classList.remove('open', 'drop-up', 'drop-down');
};

const positionInstitutionMenu = () => {
    if (!institutionMenu.classList.contains('open')) return;

    const viewport = window.visualViewport;
    const rect = institutionSearch.getBoundingClientRect();
    const bottom = (viewport
        ? viewport.offsetTop + viewport.height
        : window.innerHeight) - rect.bottom - 8;
    const top = rect.top - (viewport ? viewport.offsetTop : 0) - 8;
    const openUp = bottom < 155 && top > bottom;

    institutionMenu.classList.remove('drop-up', 'drop-down');
    institutionMenu.classList.add(openUp ? 'drop-up' : 'drop-down');

    const list = institutionMenu.querySelector('.institution-list');

    if (list && !list.classList.contains('is-empty')) {
        list.style.maxHeight = `${Math.max(
            72,
            Math.min(108, (openUp ? top : bottom) - 45)
        )}px`;
    }
};

const renderInstitutionMenu = () => {
    if (institutionSearch.disabled) return;

    institutionResults = institutionMatches(institutionSearch.value);
    institutionActiveIndex = -1;

    institutionMenu.innerHTML = `
        <div class="institution-list${institutionResults.length ? '' : ' is-empty'}">
            ${institutionResults.map((item, index) => `
                <button class="institution-option" type="button" data-institution-index="${index}">
                    <span class="institution-option-name">${escapeHtml(item.name)}</span>
                    ${item.alias
                        ? `<span class="institution-option-alias">${escapeHtml(item.alias)}</span>`
                        : ''}
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

const resetInstitutionPicker = () => {
    institutionOtherMode = false;
    institutionInput.value = '';
    institutionSearch.value = '';
    institutionOtherInput.value = '';
    institutionSearch.setCustomValidity('');
    institutionOtherInput.setCustomValidity('');

    el('institutionOther').classList.remove('open');
    el('institutionSelect').classList.remove('hidden');
    el('institutionModeLabel').textContent = 'Pilih instansi';

    closeInstitutionMenu();
};

const updateMainForm = () => {
    const nikValid = /^\d{16}$/.test(el('nik').value);

    toggleInput(el('nip'), nikValid);
    setGroupState(el('nip'), nikValid);

    const nipValid = nikValid && /^\d{18}$/.test(el('nip').value);

    if (!nipValid && (
        institutionInput.value ||
        institutionSearch.value ||
        institutionOtherMode
    )) {
        resetInstitutionPicker();
    }

    toggleInput(
        institutionSearch,
        nipValid && !institutionOtherMode,
        false
    );

    toggleInput(
        institutionOtherInput,
        nipValid && institutionOtherMode,
        false
    );

    institutionPicker.style.opacity = nipValid ? '1' : '0.5';
    institutionPicker.style.pointerEvents = nipValid ? '' : 'none';

    const institutionValid =
        nipValid &&
        institutionInput.value.trim() !== '' &&
        (
            institutionOtherMode
                ? institutionOtherInput.value.trim() !== ''
                : institutions.some(item =>
                    normalizeText(item.name) === normalizeText(institutionSearch.value) ||
                    (
                        item.alias &&
                        normalizeText(item.alias) === normalizeText(institutionSearch.value)
                    )
                )
        );

    if (!checking) {
        checkButton.disabled = !institutionValid;
    }
};

const selectInstitution = item => {
    institutionSearch.value = item.name;
    institutionInput.value = item.name;
    institutionSearch.setCustomValidity('');

    closeInstitutionMenu();
    institutionSearch.blur();
    updateMainForm();
};

const activateInstitutionOther = () => {
    institutionOtherMode = true;
    institutionInput.value = '';
    institutionSearch.value = '';
    institutionOtherInput.value = '';

    closeInstitutionMenu();

    el('institutionSelect').classList.add('hidden');
    el('institutionOther').classList.add('open');
    el('institutionModeLabel').textContent = 'Instansi lainnya';

    updateMainForm();

    setTimeout(() => {
        institutionOtherInput.focus({ preventScroll: true });
    }, 100);
};

institutionSearch.addEventListener('focus', renderInstitutionMenu);
institutionSearch.addEventListener('click', renderInstitutionMenu);

institutionSearch.addEventListener('input', () => {
    institutionInput.value = '';
    institutionSearch.setCustomValidity('');
    renderInstitutionMenu();
    updateMainForm();
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

        options.forEach((option, index) => {
            option.classList.toggle('active', index === institutionActiveIndex);
        });

        options[institutionActiveIndex].scrollIntoView({
            block: 'nearest'
        });
    }

    if (
        event.key === 'Enter' &&
        institutionActiveIndex >= 0 &&
        institutionResults[institutionActiveIndex]
    ) {
        event.preventDefault();
        selectInstitution(institutionResults[institutionActiveIndex]);
    }

    if (event.key === 'Escape') {
        closeInstitutionMenu();
    }
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
    institutionInput.value = institutionOtherInput.value
        .replace(/\s+/g, ' ')
        .trimStart();

    updateMainForm();
});

el('institutionBack').addEventListener('pointerdown', event => {
    event.preventDefault();

    resetInstitutionPicker();
    updateMainForm();

    setTimeout(() => {
        institutionSearch.focus({ preventScroll: true });
        renderInstitutionMenu();
    }, 50);
});

const positionRegionMenu = (search, menu) => {
    if (!menu.classList.contains('open')) return;

    const viewport = window.visualViewport;
    const rect = search.getBoundingClientRect();
    const bottom = (viewport
        ? viewport.offsetTop + viewport.height
        : window.innerHeight) - rect.bottom - 8;
    const top = rect.top - (viewport ? viewport.offsetTop : 0) - 8;
    const openUp = bottom < 125 && top > bottom;

    menu.classList.remove('drop-up', 'drop-down');
    menu.classList.add(openUp ? 'drop-up' : 'drop-down');

    const list = menu.querySelector('.region-list');

    if (list) {
        list.style.maxHeight = `${Math.max(
            72,
            Math.min(108, (openUp ? top : bottom) - 10)
        )}px`;
    }
};

const fetchRegionJson = async url => {
    const response = await fetch(url, {
        headers: { Accept: 'application/json' }
    });

    const data = await readJson(response);

    if (!response.ok) {
        throw new Error(data.message || 'Data wilayah gagal dimuat.');
    }

    return Array.isArray(data) ? data : [];
};

const createRegionPicker = ({
    searchId,
    menuId,
    valueId,
    codeId,
    emptyText,
    onOpen,
    onSelect
}) => {
    const search = el(searchId);
    const menu = el(menuId);
    const value = el(valueId);
    const code = codeId ? el(codeId) : null;

    let items = [];
    let results = [];
    let activeIndex = -1;
    let loading = false;

    const close = () => {
        menu.classList.remove('open', 'drop-up', 'drop-down');
    };

    const setItems = data => {
        items = Array.isArray(data) ? data : [];
    };

    const choose = item => {
        search.value = item.name ?? '';
        value.value = item.name ?? '';

        if (code) {
            code.value = item.id ?? item.code ?? '';
        }

        search.setCustomValidity('');
        close();

        if (onSelect) {
            onSelect(item);
        }

        search.blur();
    };

    const filtered = () => {
        const query = normalizeText(search.value);

        return items
            .filter(item =>
                query.split(' ').filter(Boolean).every(token =>
                    normalizeText(`${item.name ?? ''} ${item.alias ?? ''}`).includes(token)
                )
            )
            .sort((a, b) =>
                String(a.name).localeCompare(String(b.name), 'id')
            )
            .slice(0, 20);
    };

    const renderItems = () => {
        if (search.disabled) return;

        results = filtered();
        activeIndex = -1;

        menu.innerHTML = results.length
            ? `<div class="region-list">
                ${results.map((item, index) => `
                    <div class="region-option" role="button" tabindex="-1" data-region-index="${index}">
                        <span class="region-option-name">${escapeHtml(item.name)}</span>
                        ${item.alias
                            ? `<span class="region-option-alias">${escapeHtml(item.alias)}</span>`
                            : ''}
                    </div>
                `).join('')}
            </div>`
            : `<div class="region-empty">${escapeHtml(emptyText || 'Data tidak ditemukan.')}</div>`;

        menu.classList.add('open');

        requestAnimationFrame(() => {
            positionRegionMenu(search, menu);
        });
    };

    const open = async () => {
        if (search.disabled || loading) return;

        try {
            if (onOpen) {
                loading = true;
                menu.innerHTML = '<div class="region-loading"><span class="spinner"></span> Memuat data...</div>';
                menu.classList.add('open');

                await onOpen();
                loading = false;
            }

            renderItems();
        } catch (error) {
            loading = false;
            menu.innerHTML = `<div class="region-empty">${escapeHtml(error.message || 'Data wilayah gagal dimuat.')}</div>`;
            menu.classList.add('open');
        }
    };

    const reset = () => {
        search.value = '';
        value.value = '';

        if (code) {
            code.value = '';
        }

        search.setCustomValidity('');
        close();
    };

    const setDisabled = (disabled, placeholder) => {
        if (placeholder !== undefined) {
            search.placeholder = placeholder;
        }

        if (disabled && !search.disabled) {
            reset();
        }

        search.disabled = disabled;

        const group = search.closest('.form-group');

        if (group) {
            group.style.opacity = disabled ? '0.5' : '1';
        }
    };

    const commitExact = () => {
        const match = items.find(item =>
            normalizeText(item.name) === normalizeText(search.value)
        );

        if (!match) return false;

        choose(match);
        return true;
    };

    search.addEventListener('focus', open);
    search.addEventListener('click', open);

    search.addEventListener('input', () => {
        value.value = '';

        if (code) {
            code.value = '';
        }

        search.setCustomValidity('');

        if (items.length) {
            renderItems();
        } else {
            open();
        }

        updateManualForm();
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

            menu.querySelectorAll('.region-option').forEach((option, index) => {
                option.classList.toggle('active', index === activeIndex);
            });

            menu.querySelectorAll('.region-option')[activeIndex]?.scrollIntoView({
                block: 'nearest'
            });
        }

        if (
            event.key === 'Enter' &&
            activeIndex >= 0 &&
            results[activeIndex]
        ) {
            event.preventDefault();
            choose(results[activeIndex]);
        }

        if (event.key === 'Escape') {
            close();
        }
    });

    menu.addEventListener('pointerdown', event => {
        const option = event.target.closest('[data-region-index]');

        if (!option) return;

        event.preventDefault();

        const item = results[Number(option.dataset.regionIndex)];

        if (item) {
            choose(item);
        }
    });

    document.addEventListener('pointerdown', event => {
        if (event.target !== search && !menu.contains(event.target)) {
            close();
        }
    });

    window.addEventListener('resize', () => {
        positionRegionMenu(search, menu);
    });

    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', () => {
            positionRegionMenu(search, menu);
        });

        window.visualViewport.addEventListener('scroll', () => {
            positionRegionMenu(search, menu);
        });
    }

    return {
        setItems,
        reset,
        setDisabled,
        commitExact,
        getSearch: () => search,
        getValue: () => value.value
    };
};

let provincesLoaded = false;
let regenciesParent = '';
let districtsParent = '';
let villagesParent = '';

let provincePicker;
let regencyPicker;
let districtPicker;
let villagePicker;

const resetRtRw = () => {
    for (const id of ['rw', 'rt']) {
        el(id).value = '';
        el(id).disabled = true;
        el(id).setCustomValidity('');
    }
};

const loadProvinces = async () => {
    if (provincesLoaded) return;

    const data = await fetchRegionJson('/wilayah/provinsi');

    provincePicker.setItems(data.map(item => ({
        id: item.id ?? item.code,
        name: item.name
    })));

    provincesLoaded = true;
};

const loadRegencies = async () => {
    const code = el('province_code').value;

    if (!code || regenciesParent === code) return;

    const data = await fetchRegionJson(
        `/wilayah/kabupaten-kota?province=${encodeURIComponent(code)}`
    );

    if (el('province_code').value !== code) return;

    regencyPicker.setItems(data.map(item => ({
        id: item.id ?? item.code,
        name: item.name
    })));

    regenciesParent = code;
};

const loadDistricts = async () => {
    const code = el('regency_code').value;

    if (!code || districtsParent === code) return;

    const data = await fetchRegionJson(
        `/wilayah/kecamatan?regency=${encodeURIComponent(code)}`
    );

    if (el('regency_code').value !== code) return;

    districtPicker.setItems(data.map(item => ({
        id: item.id ?? item.code,
        name: item.name
    })));

    districtsParent = code;
};

const loadVillages = async () => {
    const code = el('district_code').value;

    if (!code || villagesParent === code) return;

    const data = await fetchRegionJson(
        `/wilayah/kelurahan?district=${encodeURIComponent(code)}`
    );

    if (el('district_code').value !== code) return;

    villagePicker.setItems(data.map(item => ({
        id: item.id ?? item.code,
        name: item.name
    })));

    villagesParent = code;
};

provincePicker = createRegionPicker({
    searchId: 'provinceSearch',
    menuId: 'provinceMenu',
    valueId: 'province_name',
    codeId: 'province_code',
    emptyText: 'Provinsi tidak ditemukan.',
    onOpen: loadProvinces,
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
        resetRtRw();
        updateManualForm();
    }
});

regencyPicker = createRegionPicker({
    searchId: 'regencySearch',
    menuId: 'regencyMenu',
    valueId: 'regency_name',
    codeId: 'regency_code',
    emptyText: 'Kabupaten / kota tidak ditemukan.',
    onOpen: loadRegencies,
    onSelect: () => {
        districtsParent = '';
        villagesParent = '';

        districtPicker.setItems([]);
        villagePicker.setItems([]);

        districtPicker.reset();
        villagePicker.reset();
        resetRtRw();
        updateManualForm();
    }
});

districtPicker = createRegionPicker({
    searchId: 'districtSearch',
    menuId: 'districtMenu',
    valueId: 'district_name',
    codeId: 'district_code',
    emptyText: 'Kecamatan tidak ditemukan.',
    onOpen: loadDistricts,
    onSelect: () => {
        villagesParent = '';

        villagePicker.setItems([]);
        villagePicker.reset();
        resetRtRw();
        updateManualForm();
    }
});

villagePicker = createRegionPicker({
    searchId: 'villageSearch',
    menuId: 'villageMenu',
    valueId: 'village_name',
    codeId: 'village_code',
    emptyText: 'Kelurahan / desa tidak ditemukan.',
    onOpen: loadVillages,
    onSelect: updateManualForm
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

    provincePicker.setDisabled(true, 'Isi Gmail terlebih dahulu');
    regencyPicker.setDisabled(true, 'Pilih provinsi dahulu');
    districtPicker.setDisabled(true, 'Pilih kabupaten / kota dahulu');
    villagePicker.setDisabled(true, 'Pilih kecamatan dahulu');

    resetRtRw();
};

const updateManualForm = () => {
    const nameValid = el('name').value.trim().length > 0;

    toggleInput(el('manual_email'), nameValid);
    setGroupState(el('manual_email'), nameValid);

    const email = el('manual_email').value.trim().toLowerCase();

    const emailValid =
        nameValid &&
        el('manual_email').checkValidity() &&
        /^[^\s@]+@gmail\.com$/.test(email);

    toggleInput(el('email_consent'), emailValid);

    provincePicker.setDisabled(
        !emailValid,
        emailValid ? 'Cari atau pilih provinsi' : 'Isi Gmail terlebih dahulu'
    );

    const provinceValid = emailValid && el('province_name').value !== '';

    regencyPicker.setDisabled(
        !provinceValid,
        provinceValid ? 'Cari atau pilih kabupaten / kota' : 'Pilih provinsi dahulu'
    );

    const regencyValid = provinceValid && el('regency_name').value !== '';

    districtPicker.setDisabled(
        !regencyValid,
        regencyValid ? 'Cari atau pilih kecamatan' : 'Pilih kabupaten / kota dahulu'
    );

    const districtValid = regencyValid && el('district_name').value !== '';

    villagePicker.setDisabled(
        !districtValid,
        districtValid ? 'Cari atau pilih kelurahan / desa' : 'Pilih kecamatan dahulu'
    );

    const villageValid = districtValid && el('village_name').value !== '';

    toggleInput(el('rw'), villageValid);
    setGroupState(el('rw'), villageValid);

    const rw = el('rw').value.trim();
    const rwValid = !rw || /^\d{1,3}$/.test(rw);
    const rtEnabled = villageValid && /^\d{3}$/.test(rw);

    toggleInput(el('rt'), rtEnabled);
    setGroupState(el('rt'), rtEnabled);

    const rt = el('rt').value.trim();
    const rtValid = !rt || /^\d{1,3}$/.test(rt);

    const complete =
        el('registration_token').value !== '' &&
        /^\d{18}$/.test(el('manual_nip').value) &&
        nameValid &&
        emailValid &&
        villageValid &&
        rwValid &&
        rtValid;

    if (!saving) {
        saveButton.disabled = !complete;
    }
};

const validateRegionForm = () => {
    const required = [
        [provincePicker, 'Silakan pilih provinsi dari daftar.'],
        [regencyPicker, 'Silakan pilih kabupaten / kota dari daftar.'],
        [districtPicker, 'Silakan pilih kecamatan dari daftar.'],
        [villagePicker, 'Silakan pilih kelurahan / desa dari daftar.']
    ];

    for (const [picker, message] of required) {
        if (picker.getValue() || picker.commitExact()) continue;

        picker.getSearch().setCustomValidity(message);
        picker.getSearch().reportValidity();
        picker.getSearch().focus();

        return false;
    }

    return true;
};

document.addEventListener('pointerdown', event => {
    if (!institutionPicker.contains(event.target)) {
        closeInstitutionMenu();
    }
});

window.addEventListener('resize', positionInstitutionMenu);

window.addEventListener('orientationchange', () => {
    setTimeout(positionInstitutionMenu, 100);
});

if (window.visualViewport) {
    window.visualViewport.addEventListener('resize', positionInstitutionMenu);
    window.visualViewport.addEventListener('scroll', positionInstitutionMenu);
}

document.querySelectorAll('[data-close-modal]').forEach(button => {
    button.addEventListener('click', () => {
        closeModal(button.dataset.closeModal);

        const focus = button.dataset.focus;

        if (focus) {
            setTimeout(() => el(focus)?.focus(), 100);
        }
    });
});

document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('pointerdown', event => {
        if (event.target === modal) {
            closeModal(modal.id);
        }
    });
});

document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;

    closeInstitutionMenu();

    document.querySelectorAll('.region-menu.open').forEach(menu => {
        menu.classList.remove('open', 'drop-up', 'drop-down');
    });

    document.querySelectorAll('.modal.open').forEach(modal => {
        closeModal(modal.id);
    });
});

el('nik').addEventListener('input', event => {
    event.target.value = event.target.value.replace(/\D/g, '').slice(0, 16);
    event.target.setCustomValidity('');
    updateMainForm();
});

el('nip').addEventListener('input', event => {
    event.target.value = event.target.value.replace(/\D/g, '').slice(0, 18);
    event.target.setCustomValidity('');
    updateMainForm();
});

el('name').addEventListener('input', updateManualForm);

el('manual_email').addEventListener('input', event => {
    event.target.setCustomValidity('');
    updateManualForm();
});

el('manual_email').addEventListener('blur', event => {
    event.target.value = event.target.value.trim().toLowerCase();
    updateManualForm();
});

for (const id of ['rw', 'rt']) {
    el(id).addEventListener('input', event => {
        event.target.value = event.target.value.replace(/\D/g, '').slice(0, 3);
        event.target.setCustomValidity('');
        updateManualForm();
    });

    el(id).addEventListener('blur', event => {
        const value = event.target.value.replace(/\D/g, '').slice(0, 3);
        event.target.value = value ? value.padStart(3, '0') : '';
        updateManualForm();
    });
}

el('openManualFormButton').addEventListener('click', () => {
    closeModal('notFoundModal');

    manualError.style.display = 'none';
    manualError.textContent = '';

    el('name').value = '';
    el('manual_email').value = '';
    el('email_consent').checked = false;

    resetRegionForm();
    updateManualForm();
    openModal('manualModal');

    setTimeout(() => {
        el('name').focus();
    }, 120);
});

checkForm.addEventListener('submit', async event => {
    event.preventDefault();

    const nik = el('nik').value.replace(/\D/g, '');
    const nip = el('nip').value.replace(/\D/g, '');

    if (nik.length !== 16 || nip.length !== 18) {
        updateMainForm();
        return;
    }

    if (institutionOtherMode) {
        const name = institutionOtherInput.value
            .replace(/\s+/g, ' ')
            .trim();

        if (!name) {
            institutionOtherInput.setCustomValidity('Nama instansi wajib diisi.');
            institutionOtherInput.reportValidity();
            return;
        }

        institutionInput.value = name;
    } else {
        const selected = institutions.find(item =>
            normalizeText(item.name) === normalizeText(institutionSearch.value) ||
            (
                item.alias &&
                normalizeText(item.alias) === normalizeText(institutionSearch.value)
            )
        );

        if (selected) {
            selectInstitution(selected);
        }

        if (!institutionInput.value) {
            institutionSearch.setCustomValidity(
                'Silakan pilih instansi dari daftar atau gunakan Instansi Lainnya.'
            );
            institutionSearch.reportValidity();
            return;
        }
    }

    closeInstitutionMenu();

    checking = true;
    setButtonLoading(checkButton, true, 'Cek NIK', 'Memeriksa');

    try {
        const response = await fetch('/cek-unit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                nik,
                nip,
                institution: institutionInput.value
            })
        });

        const result = await readJson(response);

        if (!response.ok) {
            showResultModal({
                type: 'error',
                title: 'Pengecekan Gagal',
                message: result.errors
                    ? Object.values(result.errors).flat().join(' ')
                    : result.message || 'Pengecekan gagal.'
            });

            return;
        }

        if (result.result === 'found') {
            const already = result.data.status === 'Sudah Didata';

            showResultModal({
                type: already ? 'success' : 'warning',
                title: already ? 'Data Sudah Didata' : 'Data Belum Didata',
                message: result.message,
                details:
                    detailRow('NIK', result.data.masked_nik) +
                    detailRow('Nama', result.data.name || '-') +
                    detailRow('Status', result.data.status || '-')
            });

            return;
        }

        if (result.result === 'manual_found') {
            showResultModal({
                type: 'success',
                title: 'Data Sudah Dikirim',
                message: result.message,
                details:
                    detailRow('NIK', result.data.masked_nik) +
                    detailRow('Instansi', result.data.institution)
            });

            return;
        }

        if (result.result === 'not_found') {
            el('registration_token').value = result.data.registration_token;
            el('manual_nik').value = result.data.masked_nik;
            el('manual_institution').value = result.data.institution;
            el('manual_nip').value = nip;

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
        setButtonLoading(checkButton, false, 'Cek NIK', 'Memeriksa');
        updateMainForm();
    }
});

manualForm.addEventListener('submit', async event => {
    event.preventDefault();

    manualError.style.display = 'none';
    manualError.textContent = '';

    el('manual_email').value = el('manual_email').value.trim().toLowerCase();
    el('manual_email').setCustomValidity('');

    if (
        !el('manual_email').checkValidity() ||
        !/^[^\s@]+@gmail\.com$/.test(el('manual_email').value)
    ) {
        el('manual_email').setCustomValidity(
            'Gunakan alamat Gmail dengan domain @gmail.com.'
        );

        el('manual_email').reportValidity();
        el('manual_email').focus();
        return;
    }

    if (!validateRegionForm()) return;

    saving = true;
    setButtonLoading(saveButton, true, 'Simpan Data', 'Menyimpan');

    try {
        const payload = Object.fromEntries(
            new FormData(manualForm).entries()
        );

        const response = await fetch('/pendaftaran-manual', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(payload)
        });

        const result = await readJson(response);

        if (!response.ok) {
            manualError.textContent = result.errors
                ? Object.values(result.errors).flat().join(' ')
                : result.message || 'Data gagal disimpan.';

            manualError.style.display = 'block';
            return;
        }

        closeModal('manualModal');

        checkForm.reset();
        manualForm.reset();

        resetInstitutionPicker();
        resetRegionForm();

        showResultModal({
            type: 'success',
            title: 'Data Berhasil Disimpan',
            message: result.message || 'Data Anda telah berhasil disimpan.',
            details:
                detailRow('Nama', result.data?.name || '-') +
                detailRow('Instansi', result.data?.institution || '-'),
            focusAfterClose: 'nik'
        });
    } catch {
        manualError.textContent =
            'Terjadi kesalahan saat menyimpan data. Silakan coba kembali.';

        manualError.style.display = 'block';
    } finally {
        saving = false;

        setButtonLoading(saveButton, false, 'Simpan Data', 'Menyimpan');

        updateMainForm();
        updateManualForm();
    }
});

checkForm.addEventListener('reset', () => {
    setTimeout(updateMainForm, 0);
});

manualForm.addEventListener('reset', () => {
    setTimeout(updateManualForm, 0);
});

resetRegionForm();
updateMainForm();
updateManualForm();
</script>
</body>
</html>
