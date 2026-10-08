
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
                <p class="brand-subtitle">
                    Pengecekan status pendataan Sensus Ekonomi bagi ASN
                </p>
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
                    <p>
                        Masukkan NIK dan pilih instansi untuk mengecek status pendataan Sensus Ekonomi ASN.
                    </p>
                </div>

                <form id="checkForm" autocomplete="off">
                    <div class="form-group">
                        <label class="form-label" for="nik">
                            <span>NIK</span>
                            <span class="form-label-meta">16 digit</span>
                        </label>

                        <input class="input" id="nik" name="nik" type="text" inputmode="numeric" maxlength="16" placeholder="Masukkan 16 digit NIK" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="institutionSearch">
                            <span>Instansi</span>
                            <span class="form-label-meta" id="institutionModeLabel">Pilih instansi</span>
                        </label>

                        <div class="institution-picker" id="institutionPicker">
                            <div class="institution-select" id="institutionSelect">
                                <input class="input" id="institutionSearch" type="text" autocomplete="off" placeholder="Cari atau pilih instansi">
                                <span class="institution-chevron">⌄</span>
                                <div class="institution-menu" id="institutionMenu"></div>
                            </div>

                            <div class="institution-other" id="institutionOther">
                                <div class="institution-other-box">
                                    <input class="input" id="institutionOtherInput" type="text" maxlength="255" autocomplete="off" placeholder="Masukkan nama instansi">
                                    <button class="institution-back" id="institutionBack" type="button">← Kembali ke daftar instansi</button>
                                </div>
                            </div>

                            <input type="hidden" id="institution" name="institution">
                        </div>
                    </div>

                    <button class="btn btn-primary btn-block" id="checkButton" type="submit">Cek NIK</button>
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
    <div class="modal-dialog" role="dialog" aria-modal="true">
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
    <div class="modal-dialog" role="dialog" aria-modal="true">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <span class="status-icon warning">!</span>
                <h3 class="modal-title">Lengkapi Data</h3>
            </div>

            <button class="modal-close" type="button" data-close-modal="notFoundModal" aria-label="Tutup">×</button>
        </div>

        <div class="modal-body">
            <p class="modal-message">
                NIK tidak ditemukan pada data master. Silakan lengkapi data yang dibutuhkan untuk melanjutkan pendataan.
            </p>
        </div>

        <div class="modal-footer">
            <button class="btn btn-primary" type="button" id="openManualFormButton">Lengkapi Data</button>
        </div>
    </div>
</div>

<div class="modal" id="manualModal" aria-hidden="true">
    <div class="modal-dialog large" role="dialog" aria-modal="true">
        <div class="modal-header">
            <div class="modal-title-wrap">
                <span class="status-icon warning">!</span>
                <h3 class="modal-title">Lengkapi Data</h3>
            </div>

            <button class="modal-close" type="button" data-close-modal="manualModal" aria-label="Tutup">×</button>
        </div>

        <form id="manualForm" autocomplete="off">
            <div class="modal-body">
                <p class="manual-intro">
                    Lengkapi informasi berikut dengan data yang sesuai untuk melanjutkan pendataan.
                </p>

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

                    <div class="form-group span-2">
                        <label class="form-label" for="name">
                            <span>Nama Lengkap</span>
                        </label>
                        <input class="input" id="name" name="name" type="text" maxlength="255" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="provinceSearch">
                            <span>Provinsi</span>
                        </label>

                        <div class="region-picker" id="provincePicker">
                            <div class="region-select">
                                <input class="input region-search" id="provinceSearch" type="text" autocomplete="off" placeholder="Cari atau pilih provinsi">
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

                                <input
                                    class="input"
                                    id="rw"
                                    name="rw"
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="3"
                                    autocomplete="off"
                                    placeholder="Contoh: 001"
                                    disabled
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="rt">
                                    <span>RT</span>
                                </label>

                                <input
                                    class="input"
                                    id="rt"
                                    name="rt"
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="3"
                                    autocomplete="off"
                                    placeholder="Contoh: 001"
                                    disabled
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-close-modal="manualModal">Batal</button>
                <button class="btn btn-primary" type="submit" id="saveButton">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const institutions = @json($institutions ?? []);

const checkForm = document.getElementById('checkForm');
const checkButton = document.getElementById('checkButton');
const manualForm = document.getElementById('manualForm');
const saveButton = document.getElementById('saveButton');
const manualError = document.getElementById('manualError');

const resultIcon = document.getElementById('resultIcon');
const resultTitle = document.getElementById('resultTitle');
const resultMessage = document.getElementById('resultMessage');
const resultDetails = document.getElementById('resultDetails');
const resultAction = document.getElementById('resultAction');

const institutionPicker = document.getElementById('institutionPicker');
const institutionSelect = document.getElementById('institutionSelect');
const institutionSearch = document.getElementById('institutionSearch');
const institutionMenu = document.getElementById('institutionMenu');
const institutionOther = document.getElementById('institutionOther');
const institutionOtherInput = document.getElementById('institutionOtherInput');
const institutionBack = document.getElementById('institutionBack');
const institutionInput = document.getElementById('institution');
const institutionModeLabel = document.getElementById('institutionModeLabel');

let institutionOtherMode = false;
let institutionResults = [];
let institutionActiveIndex = -1;

const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
}[char]));

const normalizeText = value => String(value ?? '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/\s+/g, ' ')
    .trim()
    .toLowerCase();

const openModal = id => {
    const modal = document.getElementById(id);
    if (!modal) return;

    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
};

const closeModal = id => {
    const modal = document.getElementById(id);
    if (!modal) return;

    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
};

const setButtonLoading = (button, loading, normalText, loadingText) => {
    button.disabled = loading;
    button.innerHTML = loading
        ? `<span class="spinner"></span>&nbsp;&nbsp;${loadingText}`
        : normalText;
};

const detailRow = (label, value) => `
    <div class="detail-row">
        <div class="detail-label">${escapeHtml(label)}</div>
        <div class="detail-value">${value}</div>
    </div>
`;

const showResultModal = ({
    type = 'success',
    title,
    message,
    details = '',
    buttonText = 'Selesai',
    focusAfterClose = null,
}) => {
    const iconMap = {
        success: '✓',
        warning: '!',
        error: '!',
    };

    resultIcon.className = `status-icon ${type}`;
    resultIcon.textContent = iconMap[type] || '✓';
    resultTitle.textContent = title;
    resultMessage.textContent = message;
    resultDetails.innerHTML = details;
    resultDetails.style.display = details ? 'block' : 'none';
    resultAction.textContent = buttonText;
    resultAction.dataset.focus = focusAfterClose || '';

    openModal('resultModal');
};

const readJson = async response => {
    try {
        return await response.json();
    } catch {
        return { message: 'Respons server tidak dapat dibaca.' };
    }
};

const getInstitutionResults = query => {
    const normalized = normalizeText(query);
    const tokens = normalized.split(' ').filter(Boolean);

    return institutions
        .filter(item => {
            const text = normalizeText(`${item.name ?? ''} ${item.alias ?? ''}`);
            return tokens.every(token => text.includes(token));
        })
        .sort((a, b) => {
            const aName = normalizeText(a.name);
            const bName = normalizeText(b.name);
            const aAlias = normalizeText(a.alias);
            const bAlias = normalizeText(b.alias);

            const score = (name, alias) => {
                if (!normalized) return 4;
                if (name === normalized || alias === normalized) return 0;
                if (name.startsWith(normalized) || alias.startsWith(normalized)) return 1;
                if (name.split(' ').some(word => word.startsWith(normalized))) return 2;
                return 3;
            };

            const difference = score(aName, aAlias) - score(bName, bAlias);
            return difference !== 0
                ? difference
                : aName.localeCompare(bName, 'id');
        });
};

const positionInstitutionMenu = () => {
    if (!institutionMenu.classList.contains('open')) return;

    const viewport = window.visualViewport;
    const viewportHeight = viewport ? viewport.height : window.innerHeight;
    const viewportTop = viewport ? viewport.offsetTop : 0;

    const rect = institutionSearch.getBoundingClientRect();
    const spaceBelow = viewportTop + viewportHeight - rect.bottom - 8;
    const spaceAbove = rect.top - viewportTop - 8;
    const openUp = spaceBelow < 155 && spaceAbove > spaceBelow;

    institutionMenu.classList.remove('drop-up', 'drop-down');
    institutionMenu.classList.add(openUp ? 'drop-up' : 'drop-down');

    const list = institutionMenu.querySelector('.institution-list');

    if (!list || list.classList.contains('is-empty')) return;

    const availableSpace = openUp ? spaceAbove : spaceBelow;
    const listHeight = Math.max(72, Math.min(108, availableSpace - 45));

    list.style.height = `${listHeight}px`;
    list.style.maxHeight = `${listHeight}px`;
};

const renderInstitutionMenu = () => {
    institutionResults = getInstitutionResults(institutionSearch.value);
    institutionActiveIndex = -1;

    const options = institutionResults
        .map((item, index) => `
            <button class="institution-option" type="button" data-institution-index="${index}">
                <span class="institution-option-name">${escapeHtml(item.name)}</span>
                ${item.alias ? `<span class="institution-option-alias">${escapeHtml(item.alias)}</span>` : ''}
            </button>
        `)
        .join('');

    const emptyClass = institutionResults.length ? '' : ' is-empty';

    institutionMenu.innerHTML = `
        <div class="institution-list${emptyClass}">
            ${options || '<div class="institution-empty">Tidak ada instansi yang cocok.</div>'}
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

    institutionMenu.classList.remove('open', 'drop-up', 'drop-down');
    institutionSearch.setCustomValidity('');
    institutionSearch.blur();
};

const activateInstitutionOther = () => {
    institutionOtherMode = true;
    institutionInput.value = '';
    institutionSearch.value = '';
    institutionOtherInput.value = '';

    institutionMenu.classList.remove('open', 'drop-up', 'drop-down');
    institutionSelect.classList.add('hidden');
    institutionOther.classList.add('open');

    institutionModeLabel.textContent = 'Instansi lainnya';

    setTimeout(() => {
        institutionOtherInput.focus({ preventScroll: true });
    }, 100);
};

const resetInstitutionPicker = () => {
    institutionOtherMode = false;
    institutionInput.value = '';
    institutionSearch.value = '';
    institutionOtherInput.value = '';

    institutionSearch.setCustomValidity('');
    institutionOtherInput.setCustomValidity('');

    institutionOther.classList.remove('open');
    institutionSelect.classList.remove('hidden');
    institutionMenu.classList.remove('open', 'drop-up', 'drop-down');

    institutionModeLabel.textContent = 'Pilih instansi';
};

institutionSearch.addEventListener('focus', () => {
    renderInstitutionMenu();
    setTimeout(positionInstitutionMenu, 50);
    setTimeout(positionInstitutionMenu, 200);
    setTimeout(positionInstitutionMenu, 400);
});

institutionSearch.addEventListener('click', () => {
    renderInstitutionMenu();
    setTimeout(positionInstitutionMenu, 20);
});

institutionSearch.addEventListener('input', () => {
    institutionInput.value = '';
    institutionSearch.setCustomValidity('');
    renderInstitutionMenu();
    setTimeout(positionInstitutionMenu, 20);
});

institutionSearch.addEventListener('keydown', event => {
    const options = [...institutionMenu.querySelectorAll('.institution-option')];

    if (event.key === 'ArrowDown') {
        event.preventDefault();

        if (!institutionMenu.classList.contains('open')) {
            renderInstitutionMenu();
            return;
        }

        if (!options.length) return;

        institutionActiveIndex = institutionActiveIndex >= options.length - 1
            ? 0
            : institutionActiveIndex + 1;

        options.forEach((option, index) => {
            option.classList.toggle('active', index === institutionActiveIndex);
        });

        options[institutionActiveIndex]?.scrollIntoView({ block: 'nearest' });
    }

    if (event.key === 'ArrowUp') {
        event.preventDefault();

        if (!options.length) return;

        institutionActiveIndex = institutionActiveIndex <= 0
            ? options.length - 1
            : institutionActiveIndex - 1;

        options.forEach((option, index) => {
            option.classList.toggle('active', index === institutionActiveIndex);
        });

        options[institutionActiveIndex]?.scrollIntoView({ block: 'nearest' });
    }

    if (
        event.key === 'Enter' &&
        institutionMenu.classList.contains('open') &&
        institutionActiveIndex >= 0 &&
        institutionResults[institutionActiveIndex]
    ) {
        event.preventDefault();
        selectInstitution(institutionResults[institutionActiveIndex]);
    }

    if (event.key === 'Escape') {
        institutionMenu.classList.remove('open', 'drop-up', 'drop-down');
    }
});

institutionMenu.addEventListener('pointerdown', event => {
    const option = event.target.closest('[data-institution-index]');
    const other = event.target.closest('[data-institution-other]');

    if (!option && !other) return;

    event.preventDefault();
    event.stopPropagation();

    if (option) {
        const item = institutionResults[Number(option.dataset.institutionIndex)];
        if (item) selectInstitution(item);
        return;
    }

    activateInstitutionOther();
});

institutionMenu.addEventListener('keydown', event => {
    const other = event.target.closest('[data-institution-other]');

    if (!other) return;

    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        event.stopPropagation();
        activateInstitutionOther();
    }
});

institutionOtherInput.addEventListener('input', () => {
    institutionOtherInput.setCustomValidity('');
    institutionInput.value = institutionOtherInput.value
        .replace(/\s+/g, ' ')
        .trimStart();
});

institutionBack.addEventListener('pointerdown', event => {
    event.preventDefault();
    event.stopPropagation();

    resetInstitutionPicker();

    setTimeout(() => {
        institutionSearch.focus({ preventScroll: true });
        renderInstitutionMenu();
    }, 50);
});

const positionRegionMenu = (search, menu) => {
    if (!menu.classList.contains('open')) return;

    const viewport = window.visualViewport;
    const viewportHeight = viewport ? viewport.height : window.innerHeight;
    const viewportTop = viewport ? viewport.offsetTop : 0;

    const rect = search.getBoundingClientRect();
    const spaceBelow = viewportTop + viewportHeight - rect.bottom - 8;
    const spaceAbove = rect.top - viewportTop - 8;
    const openUp = spaceBelow < 125 && spaceAbove > spaceBelow;

    menu.classList.remove('drop-up', 'drop-down');
    menu.classList.add(openUp ? 'drop-up' : 'drop-down');

    const list = menu.querySelector('.region-list');
    if (!list) return;

    const availableSpace = openUp ? spaceAbove : spaceBelow;
    const listHeight = Math.max(72, Math.min(108, availableSpace - 10));

    list.style.height = `${listHeight}px`;
    list.style.maxHeight = `${listHeight}px`;
};

const fetchRegionJson = async url => {
    const response = await fetch(url, {
        headers: {
            Accept: 'application/json',
        },
    });

    const result = await readJson(response);

    if (!response.ok) {
        throw new Error(result.message || 'Data wilayah gagal dimuat.');
    }

    return Array.isArray(result) ? result : [];
};

const createRegionPicker = ({
    searchId,
    menuId,
    valueId,
    codeId = null,
    emptyText = 'Data tidak ditemukan.',
    onOpen = null,
    onSelect = null,
}) => {
    const search = document.getElementById(searchId);
    const menu = document.getElementById(menuId);
    const valueInput = document.getElementById(valueId);
    const codeInput = codeId ? document.getElementById(codeId) : null;

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

    const scoreItem = (item, query) => {
        if (!query) return 10;

        const name = normalizeText(item.name);
        const alias = normalizeText(item.alias);
        const combined = normalizeText(`${item.name ?? ''} ${item.alias ?? ''}`);

        if (name === query) return 0;
        if (alias === query) return 1;
        if (name.startsWith(query)) return 2;
        if (alias.startsWith(query)) return 3;
        if (name.split(' ').some(word => word.startsWith(query))) return 4;
        if (alias.split(' ').some(word => word.startsWith(query))) return 5;
        if (name.includes(query)) return 6;
        if (alias.includes(query)) return 7;
        if (combined.includes(query)) return 8;

        return 20;
    };

    const getFiltered = () => {
        const query = normalizeText(search.value);

        if (!query) {
            return [...items]
                .sort((a, b) => String(a.name).localeCompare(String(b.name), 'id'))
                .slice(0, 20);
        }

        const tokens = query.split(' ').filter(Boolean);

        return items
            .filter(item => {
                const text = normalizeText(`${item.name ?? ''} ${item.alias ?? ''}`);
                return tokens.every(token => text.includes(token));
            })
            .map(item => ({
                ...item,
                _score: scoreItem(item, query),
            }))
            .sort((a, b) => {
                return a._score !== b._score
                    ? a._score - b._score
                    : String(a.name).localeCompare(String(b.name), 'id');
            })
            .slice(0, 20);
    };

    const choose = item => {
        search.value = item.name ?? '';
        valueInput.value = item.name ?? '';

        if (codeInput) {
            codeInput.value = item.id ?? item.code ?? '';
        }

        search.setCustomValidity('');
        close();

        if (typeof onSelect === 'function') {
            onSelect(item);
        }

        search.blur();
    };

    const renderItems = () => {
        results = getFiltered();
        activeIndex = -1;

        const options = results
            .map((item, index) => `
                <div class="region-option" role="button" tabindex="-1" data-region-index="${index}">
                    <span class="region-option-name">${escapeHtml(item.name)}</span>
                    ${item.alias ? `<span class="region-option-alias">${escapeHtml(item.alias)}</span>` : ''}
                </div>
            `)
            .join('');

        menu.innerHTML = results.length
            ? `<div class="region-list">${options}</div>`
            : `<div class="region-empty">${escapeHtml(emptyText)}</div>`;

        menu.classList.add('open');

        requestAnimationFrame(() => {
            positionRegionMenu(search, menu);
        });
    };

    const render = async () => {
        if (search.disabled || loading) return;

        try {
            if (typeof onOpen === 'function') {
                loading = true;

                menu.innerHTML = `
                    <div class="region-loading">
                        <span class="spinner"></span>
                        <span>Memuat data...</span>
                    </div>
                `;

                menu.classList.add('open');

                requestAnimationFrame(() => {
                    positionRegionMenu(search, menu);
                });

                await onOpen();
                loading = false;
            }

            renderItems();
        } catch (error) {
            loading = false;

            menu.innerHTML = `
                <div class="region-empty">
                    ${escapeHtml(error.message || 'Data wilayah gagal dimuat.')}
                </div>
            `;

            menu.classList.add('open');

            requestAnimationFrame(() => {
                positionRegionMenu(search, menu);
            });
        }
    };

    const reset = () => {
        search.value = '';
        search.setCustomValidity('');
        valueInput.value = '';

        if (codeInput) {
            codeInput.value = '';
        }

        close();
    };

    const setDisabled = (disabled, placeholder = null) => {
        search.disabled = disabled;

        if (placeholder !== null) {
            search.placeholder = placeholder;
        }

        if (disabled) {
            reset();
        }
    };

    const commitExact = () => {
        const query = normalizeText(search.value);

        if (!query) return false;

        const exact = items.find(item => normalizeText(item.name) === query);

        if (!exact) return false;

        choose(exact);
        return true;
    };

    search.addEventListener('focus', () => {
        render();

        setTimeout(() => positionRegionMenu(search, menu), 50);
        setTimeout(() => positionRegionMenu(search, menu), 200);
        setTimeout(() => positionRegionMenu(search, menu), 400);
    });

    search.addEventListener('click', () => {
        render();
        setTimeout(() => positionRegionMenu(search, menu), 20);
    });

    search.addEventListener('input', () => {
        valueInput.value = '';

        if (codeInput) {
            codeInput.value = '';
        }

        search.setCustomValidity('');

        if (items.length) {
            renderItems();
        } else {
            render();
        }
    });

    search.addEventListener('keydown', event => {
        const options = [...menu.querySelectorAll('.region-option')];

        if (event.key === 'ArrowDown') {
            event.preventDefault();

            if (!menu.classList.contains('open')) {
                render();
                return;
            }

            if (!options.length) return;

            activeIndex = activeIndex >= options.length - 1
                ? 0
                : activeIndex + 1;

            options.forEach((option, index) => {
                option.classList.toggle('active', index === activeIndex);
            });

            options[activeIndex]?.scrollIntoView({ block: 'nearest' });
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();

            if (!options.length) return;

            activeIndex = activeIndex <= 0
                ? options.length - 1
                : activeIndex - 1;

            options.forEach((option, index) => {
                option.classList.toggle('active', index === activeIndex);
            });

            options[activeIndex]?.scrollIntoView({ block: 'nearest' });
        }

        if (event.key === 'Enter') {
            if (activeIndex >= 0 && results[activeIndex]) {
                event.preventDefault();
                choose(results[activeIndex]);
                return;
            }

            if (results.length === 1) {
                event.preventDefault();
                choose(results[0]);
            }
        }

        if (event.key === 'Escape') {
            close();
        }
    });

    menu.addEventListener('pointerdown', event => {
        const option = event.target.closest('[data-region-index]');

        if (!option) return;

        event.preventDefault();
        event.stopPropagation();

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
        getValue: () => valueInput.value,
        getCode: () => codeInput?.value || '',
    };
};

let provincesLoaded = false;
let regenciesParent = '';
let districtsParent = '';
let villagesParent = '';

let provincePickerInstance;
let regencyPickerInstance;
let districtPickerInstance;
let villagePickerInstance;

const resetRtRw = () => {
    const rwInput = document.getElementById('rw');
    const rtInput = document.getElementById('rt');

    rwInput.value = '';
    rtInput.value = '';

    rwInput.disabled = true;
    rtInput.disabled = true;

    rwInput.setCustomValidity('');
    rtInput.setCustomValidity('');
};

const enableRtRw = () => {
    const rwInput = document.getElementById('rw');
    const rtInput = document.getElementById('rt');

    rwInput.value = '';
    rtInput.value = '';

    rwInput.disabled = false;
    rtInput.disabled = false;

    rwInput.setCustomValidity('');
    rtInput.setCustomValidity('');
};

const loadProvinces = async () => {
    if (provincesLoaded) return;

    const data = await fetchRegionJson('/wilayah/provinsi');

    provincePickerInstance.setItems(
        data.map(item => ({
            id: item.id ?? item.code,
            name: item.name,
        }))
    );

    provincesLoaded = true;
};

const loadRegencies = async () => {
    const code = document.getElementById('province_code').value;

    if (!code || regenciesParent === code) return;

    const data = await fetchRegionJson(
        `/wilayah/kabupaten-kota?province=${encodeURIComponent(code)}`
    );

    regencyPickerInstance.setItems(
        data.map(item => ({
            id: item.id ?? item.code,
            name: item.name,
        }))
    );

    regenciesParent = code;
};

const loadDistricts = async () => {
    const code = document.getElementById('regency_code').value;

    if (!code || districtsParent === code) return;

    const data = await fetchRegionJson(
        `/wilayah/kecamatan?regency=${encodeURIComponent(code)}`
    );

    districtPickerInstance.setItems(
        data.map(item => ({
            id: item.id ?? item.code,
            name: item.name,
        }))
    );

    districtsParent = code;
};

const loadVillages = async () => {
    const code = document.getElementById('district_code').value;

    if (!code || villagesParent === code) return;

    const data = await fetchRegionJson(
        `/wilayah/kelurahan?district=${encodeURIComponent(code)}`
    );

    villagePickerInstance.setItems(
        data.map(item => ({
            id: item.id ?? item.code,
            name: item.name,
        }))
    );

    villagesParent = code;
};

provincePickerInstance = createRegionPicker({
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

        regencyPickerInstance.setItems([]);
        districtPickerInstance.setItems([]);
        villagePickerInstance.setItems([]);

        regencyPickerInstance.reset();
        districtPickerInstance.reset();
        villagePickerInstance.reset();

        resetRtRw();

        regencyPickerInstance.setDisabled(
            false,
            'Cari atau pilih kabupaten / kota'
        );

        districtPickerInstance.setDisabled(
            true,
            'Pilih kabupaten / kota dahulu'
        );

        villagePickerInstance.setDisabled(
            true,
            'Pilih kecamatan dahulu'
        );
    },
});

regencyPickerInstance = createRegionPicker({
    searchId: 'regencySearch',
    menuId: 'regencyMenu',
    valueId: 'regency_name',
    codeId: 'regency_code',
    emptyText: 'Kabupaten / kota tidak ditemukan.',
    onOpen: loadRegencies,
    onSelect: () => {
        districtsParent = '';
        villagesParent = '';

        districtPickerInstance.setItems([]);
        villagePickerInstance.setItems([]);

        districtPickerInstance.reset();
        villagePickerInstance.reset();

        resetRtRw();

        districtPickerInstance.setDisabled(
            false,
            'Cari atau pilih kecamatan'
        );

        villagePickerInstance.setDisabled(
            true,
            'Pilih kecamatan dahulu'
        );
    },
});

districtPickerInstance = createRegionPicker({
    searchId: 'districtSearch',
    menuId: 'districtMenu',
    valueId: 'district_name',
    codeId: 'district_code',
    emptyText: 'Kecamatan tidak ditemukan.',
    onOpen: loadDistricts,
    onSelect: () => {
        villagesParent = '';

        villagePickerInstance.setItems([]);
        villagePickerInstance.reset();

        resetRtRw();

        villagePickerInstance.setDisabled(
            false,
            'Cari atau pilih kelurahan / desa'
        );
    },
});

villagePickerInstance = createRegionPicker({
    searchId: 'villageSearch',
    menuId: 'villageMenu',
    valueId: 'village_name',
    codeId: 'village_code',
    emptyText: 'Kelurahan / desa tidak ditemukan.',
    onOpen: loadVillages,
    onSelect: () => {
        enableRtRw();
    },
});

const resetRegionForm = () => {
    regenciesParent = '';
    districtsParent = '';
    villagesParent = '';

    provincePickerInstance.reset();
    regencyPickerInstance.reset();
    districtPickerInstance.reset();
    villagePickerInstance.reset();

    regencyPickerInstance.setItems([]);
    districtPickerInstance.setItems([]);
    villagePickerInstance.setItems([]);

    provincePickerInstance.setDisabled(
        false,
        'Cari atau pilih provinsi'
    );

    regencyPickerInstance.setDisabled(
        true,
        'Pilih provinsi dahulu'
    );

    districtPickerInstance.setDisabled(
        true,
        'Pilih kabupaten / kota dahulu'
    );

    villagePickerInstance.setDisabled(
        true,
        'Pilih kecamatan dahulu'
    );

    resetRtRw();
};

const validateRegionForm = () => {
    const requiredPickers = [
        [provincePickerInstance, 'Silakan pilih provinsi dari daftar.'],
        [regencyPickerInstance, 'Silakan pilih kabupaten / kota dari daftar.'],
        [districtPickerInstance, 'Silakan pilih kecamatan dari daftar.'],
        [villagePickerInstance, 'Silakan pilih kelurahan / desa dari daftar.'],
    ];

    for (const [picker, message] of requiredPickers) {
        if (!picker.getValue() && !picker.commitExact()) {
            const search = picker.getSearch();

            search.setCustomValidity(message);
            search.reportValidity();
            search.focus();

            return false;
        }
    }

    return true;
};

document.addEventListener('pointerdown', event => {
    if (!institutionPicker.contains(event.target)) {
        institutionMenu.classList.remove('open', 'drop-up', 'drop-down');
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
        const id = button.dataset.closeModal;
        const focusId = button.dataset.focus || '';

        closeModal(id);

        if (focusId) {
            setTimeout(() => {
                document.getElementById(focusId)?.focus();
            }, 100);
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

    institutionMenu.classList.remove('open', 'drop-up', 'drop-down');

    document.querySelectorAll('.region-menu.open').forEach(menu => {
        menu.classList.remove('open', 'drop-up', 'drop-down');
    });

    document.querySelectorAll('.modal.open').forEach(modal => {
        closeModal(modal.id);
    });
});

document.getElementById('nik').addEventListener('input', event => {
    event.target.value = event.target.value.replace(/\D/g, '').slice(0, 16);
});

['rw', 'rt'].forEach(id => {
    const input = document.getElementById(id);

    input.addEventListener('input', event => {
        event.target.value = event.target.value
            .replace(/\D/g, '')
            .slice(0, 3);

        event.target.setCustomValidity('');
    });

    input.addEventListener('blur', event => {
        const value = event.target.value
            .replace(/\D/g, '')
            .slice(0, 3);

        if (!value) {
            event.target.value = '';
            return;
        }

        event.target.value = value.padStart(3, '0');
    });
});

document.getElementById('openManualFormButton').addEventListener('click', () => {
    closeModal('notFoundModal');

    manualError.style.display = 'none';
    manualError.textContent = '';

    document.getElementById('name').value = '';

    resetRegionForm();
    openModal('manualModal');

    setTimeout(() => {
        document.getElementById('name').focus();
    }, 120);
});

checkForm.addEventListener('submit', async event => {
    event.preventDefault();

    const nikInput = document.getElementById('nik');
    const nik = nikInput.value.replace(/\D/g, '');

    if (nik.length !== 16) {
        nikInput.setCustomValidity('NIK harus terdiri dari 16 digit.');
        nikInput.reportValidity();
        nikInput.focus();
        return;
    }

    nikInput.setCustomValidity('');

    if (institutionOtherMode) {
        const value = institutionOtherInput.value
            .replace(/\s+/g, ' ')
            .trim();

        if (!value) {
            institutionOtherInput.setCustomValidity('Nama instansi wajib diisi.');
            institutionOtherInput.reportValidity();
            institutionOtherInput.focus();
            return;
        }

        institutionOtherInput.setCustomValidity('');
        institutionInput.value = value;
    } else {
        const query = normalizeText(institutionSearch.value);

        const exact = institutions.find(item => {
            return normalizeText(item.name) === query ||
                normalizeText(item.alias) === query;
        });

        if (!institutionInput.value && exact) {
            selectInstitution(exact);
        }

        if (!institutionInput.value) {
            institutionSearch.setCustomValidity(
                'Silakan pilih instansi dari daftar atau gunakan Instansi Lainnya.'
            );

            institutionSearch.reportValidity();
            institutionSearch.focus();
            return;
        }

        institutionSearch.setCustomValidity('');
    }

    institutionMenu.classList.remove('open', 'drop-up', 'drop-down');

    setButtonLoading(checkButton, true, 'Cek NIK', 'Memeriksa');

    try {
        const response = await fetch('/cek-unit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({
                nik,
                institution: institutionInput.value,
            }),
        });

        const result = await readJson(response);

        if (!response.ok) {
            const message = result.errors
                ? Object.values(result.errors).flat().join(' ')
                : result.message || 'Terjadi kesalahan saat melakukan pengecekan.';

            showResultModal({
                type: 'error',
                title: 'Pengecekan Gagal',
                message,
                buttonText: 'Tutup',
            });

            return;
        }

        if (result.result === 'found') {
            const sudah = result.data.status === 'Sudah Didata';

            const status = `
                <span class="status-badge ${sudah ? 'success' : 'warning'}">
                    ${escapeHtml(result.data.status)}
                </span>
            `;

            const details =
                detailRow('NIK', escapeHtml(result.data.masked_nik)) +
                detailRow('Nama', escapeHtml(result.data.name || '-')) +
                detailRow('Status', status);

            showResultModal({
                type: sudah ? 'success' : 'warning',
                title: sudah ? 'Data Sudah Didata' : 'Data Belum Didata',
                message: result.message,
                details,
                buttonText: 'Selesai',
            });

            return;
        }

        if (result.result === 'manual_found') {
            showResultModal({
                type: 'success',
                title: 'Data Sudah Dikirim',
                message: result.message,
                details:
                    detailRow('NIK', escapeHtml(result.data.masked_nik)) +
                    detailRow('Instansi', escapeHtml(result.data.institution)),
                buttonText: 'Selesai',
            });

            return;
        }

        if (result.result === 'not_found') {
            document.getElementById('registration_token').value =
                result.data.registration_token;

            document.getElementById('manual_nik').value =
                result.data.masked_nik;

            document.getElementById('manual_institution').value =
                result.data.institution;

            openModal('notFoundModal');
            return;
        }

        showResultModal({
            type: 'error',
            title: 'Pengecekan Gagal',
            message: 'Respons server tidak dikenali.',
            buttonText: 'Tutup',
        });
    } catch {
        showResultModal({
            type: 'error',
            title: 'Koneksi Bermasalah',
            message: 'Tidak dapat menghubungi server. Silakan coba kembali.',
            buttonText: 'Tutup',
        });
    } finally {
        setButtonLoading(checkButton, false, 'Cek NIK', 'Memeriksa');
    }
});

manualForm.addEventListener('submit', async event => {
    event.preventDefault();

    manualError.style.display = 'none';
    manualError.textContent = '';

    if (!validateRegionForm()) return;

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
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify(payload),
        });

        const result = await readJson(response);

        if (!response.ok) {
            const message = result.errors
                ? Object.values(result.errors).flat().join(' ')
                : result.message || 'Data gagal disimpan.';

            manualError.textContent = message;
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
            message: 'Data Anda telah berhasil disimpan.',
            details:
                detailRow('Nama', escapeHtml(result.data?.name || '-')) +
                detailRow('NIK', escapeHtml(result.data?.masked_nik || '-')) +
                detailRow('Instansi', escapeHtml(result.data?.institution || '-')),
            buttonText: 'Selesai',
            focusAfterClose: 'nik',
        });
    } catch {
        manualError.textContent =
            'Terjadi kesalahan saat menyimpan data. Silakan coba kembali.';

        manualError.style.display = 'block';
    } finally {
        setButtonLoading(saveButton, false, 'Simpan Data', 'Menyimpan');
    }
});

resetRegionForm();
</script>
</body>
</html>
