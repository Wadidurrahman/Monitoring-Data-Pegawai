
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Monitoring Data ASN SE2026</title>
    <style>
        :root {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #14233d;
            background: #f4f7fb;
            font-synthesis: none;
            --row-height: 56px;
        }

        * { box-sizing: border-box; }

        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        body {
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        button, input, select { font: inherit; }
        button { cursor: pointer; }
        button:disabled { cursor: not-allowed; opacity: .55; }
        [hidden] { display: none !important; }

        .monitor-shell {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100dvh;
            overflow: hidden;
            background: #f4f7fb;
        }

        .monitor-header {
            flex: none;
            height: 61px;
            padding: 8px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
        }

        .brand {
            display: flex;
            align-items: center;
            min-width: 0;
            gap: 11px;
        }

        .brand-logo {
            flex: none;
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            color: #fff;
            font-size: 19px;
            font-weight: 800;
            border-radius: 9px;
            background: linear-gradient(135deg, #2563eb, #4338ca);
        }

        .brand-text { min-width: 0; }

        .brand h1 {
            margin: 0;
            font-size: clamp(14px, 1.45vw, 19px);
            font-weight: 750;
            line-height: 1.25;
            letter-spacing: -.4px;
        }

        .brand p {
            margin: 3px 0 0;
            color: #8190a6;
            font-size: 11px;
        }

        .header-total {
            flex: none;
            padding: 7px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .header-total span {
            display: block;
            color: #94a3b8;
            font-size: 10px;
        }

        .header-total strong {
            display: block;
            margin-top: 2px;
            color: #17243c;
            font-size: 16px;
            font-weight: 750;
            font-variant-numeric: tabular-nums;
        }

        .monitor-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            gap: 9px;
            padding: 11px 24px 9px;
        }

        .stats {
            flex: none;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 9px;
        }

        .stat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 0;
            min-height: 68px;
            padding: 11px 15px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            gap: 10px;
        }

        .stat-label {
            color: #718198;
            font-size: 11px;
        }

        .stat-value {
            display: block;
            margin-top: 3px;
            color: #17243c;
            font-size: clamp(19px, 2vw, 25px);
            line-height: 1.1;
            font-weight: 760;
            letter-spacing: -.5px;
            font-variant-numeric: tabular-nums;
        }

        .stat-icon {
            flex: none;
            display: grid;
            place-items: center;
            width: 35px;
            height: 35px;
            font-size: 16px;
            border-radius: 9px;
            background: #eff6ff;
            color: #2563eb;
        }

        .stat-green .stat-value { color: #059669; }
        .stat-green .stat-icon { color: #059669; background: #ecfdf5; }
        .stat-red .stat-value { color: #e11d48; }
        .stat-red .stat-icon { color: #e11d48; background: #fff1f2; }
        .stat-blue .stat-value { color: #2563eb; }

        .panel {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            background: #fff;
            border: 1px solid #dfe7f1;
            border-radius: 11px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .035);
        }

        .toolbar {
            position: relative;
            z-index: 6;
            flex: none;
            display: flex;
            align-items: center;
            gap: 9px;
            min-height: 57px;
            padding: 9px 13px;
            border-bottom: 1px solid #e8edf4;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 0;
        }

        .search-wrap svg {
            position: absolute;
            top: 50%;
            left: 13px;
            width: 16px;
            height: 16px;
            color: #94a3b8;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .field {
            width: 100%;
            min-width: 0;
            height: 37px;
            padding: 0 11px;
            color: #334155;
            background: #fff;
            border: 1px solid #dfe7f1;
            border-radius: 7px;
            outline: none;
            font-size: 12px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .08);
        }

        .search-wrap .field {
            padding-left: 39px;
            background: #f8fafc;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 37px;
            padding: 0 12px;
            color: #334155;
            background: #fff;
            border: 1px solid #dfe7f1;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
            transition: background .15s ease, border-color .15s ease;
        }

        .btn:hover:not(:disabled) { background: #f8fafc; }
        .btn svg { width: 15px; height: 15px; }

        .btn.active {
            color: #1d4ed8;
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .btn-primary {
            color: #fff;
            background: #2563eb;
            border-color: #2563eb;
        }

        .btn-primary:hover:not(:disabled) { background: #1d4ed8; }
        .btn-primary:disabled { background: #91adf5; border-color: #91adf5; }

        .filter-anchor, .export-anchor {
            position: relative;
            flex: none;
        }

        .filter-popover {
            position: absolute;
            z-index: 30;
            top: calc(100% + 10px);
            right: 0;
            width: min(620px, calc(100vw - 48px));
            background: #fff;
            border: 1px solid #dfe7f1;
            border-radius: 10px;
            box-shadow: 0 18px 50px rgba(15, 23, 42, .16);
        }

        .popover-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #eef2f7;
        }

        .popover-header h2 {
            margin: 0;
            font-size: 14px;
            font-weight: 750;
        }

        .popover-header p {
            margin: 4px 0 0;
            color: #94a3b8;
            font-size: 11px;
        }

        .close-icon {
            display: grid;
            place-items: center;
            width: 29px;
            height: 29px;
            border: 0;
            border-radius: 6px;
            color: #64748b;
            background: #f1f5f9;
            font-size: 18px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 13px;
            padding: 16px;
        }

        .filter-group { min-width: 0; }

        .filter-group label {
            display: block;
            margin-bottom: 7px;
            color: #64748b;
            font-size: 11px;
            font-weight: 650;
        }

        .popover-footer {
            display: flex;
            justify-content: flex-end;
            gap: 9px;
            padding: 12px 16px;
            background: #f8fafc;
            border-top: 1px solid #eef2f7;
            border-radius: 0 0 10px 10px;
        }

        .filter-backdrop { display: none; }

        .export-menu {
            position: absolute;
            z-index: 35;
            top: calc(100% + 8px);
            right: 0;
            width: 225px;
            padding: 5px;
            background: #fff;
            border: 1px solid #dfe7f1;
            border-radius: 9px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .14);
        }

        .export-menu a {
            display: block;
            padding: 11px 10px;
            color: #334155;
            border-radius: 6px;
            text-decoration: none;
            font-size: 11px;
        }

        .export-menu a:hover {
            color: #1d4ed8;
            background: #eff6ff;
        }

        .table-meta {
            flex: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 43px;
            padding: 7px 13px;
            border-bottom: 1px solid #eef2f7;
        }

        .table-meta strong {
            display: block;
            color: #17243c;
            font-size: 12px;
            font-weight: 750;
        }

        .table-meta p {
            margin: 3px 0 0;
            color: #94a3b8;
            font-size: 10px;
        }

        .legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            color: #64748b;
            font-size: 10px;
        }

        .legend-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            margin-right: 5px;
            border-radius: 50%;
        }

        .table-scroller {
            position: relative;
            flex: 1;
            min-height: 0;
            overflow: auto;
            overscroll-behavior: contain;
            scrollbar-gutter: stable;
        }

        .table-scroller::-webkit-scrollbar,
        .detail-body::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }

        .table-scroller::-webkit-scrollbar-thumb,
        .detail-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9px;
        }

        .data-table {
            width: 100%;
            min-width: 920px;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            text-align: left;
        }

        .data-table thead th {
            position: sticky;
            top: 0;
            z-index: 3;
            height: 37px;
            padding: 8px 11px;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
        }

        .data-row { height: var(--row-height); }

        .data-table tbody td {
            height: var(--row-height);
            padding: 7px 11px;
            color: #475569;
            border-bottom: 1px solid #eef2f7;
            font-size: 12px;
            vertical-align: middle;
            overflow: hidden;
        }

        .data-row:hover { background: #f8fbff; }

        .cell-primary {
            overflow: hidden;
            color: #17243c;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .cell-sub {
            margin-top: 4px;
            overflow: hidden;
            color: #94a3b8;
            font-size: 10px;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .cell-ellipsis {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            max-width: 100%;
            padding: 5px 8px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-success { color: #047857; background: #d1fae5; }
        .badge-danger { color: #be123c; background: #ffe4e6; }
        .badge-warning { color: #a16207; background: #fef3c7; }
        .badge-source { color: #475569; background: #f1f5f9; }
        .badge-manual { color: #6d28d9; background: #ede9fe; }

        .action-link {
            padding: 7px 9px;
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .action-link:hover { background: #dbeafe; }

        .spacer-row td {
            padding: 0 !important;
            border: 0 !important;
            background: transparent;
        }

        .empty-state {
            height: 120px !important;
            padding: 20px !important;
            color: #94a3b8;
            text-align: center;
            font-size: 12px;
        }

        .table-footer {
            flex: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            min-height: 32px;
            padding: 7px 13px;
            color: #64748b;
            background: #fff;
            border-top: 1px solid #e2e8f0;
            border-radius: 0 0 10px 10px;
            font-size: 10px;
        }

        .loading {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #2563eb;
        }

        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid #bfdbfe;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .monitor-bottom {
            flex: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            min-height: 15px;
            padding: 0 2px;
            color: #94a3b8;
            font-size: 10px;
        }

        .modal {
            position: fixed;
            inset: 0;
            z-index: 60;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 18px;
            background: rgba(15, 23, 42, .58);
        }

        .modal-dialog {
            display: flex;
            flex-direction: column;
            width: min(690px, 100%);
            max-height: min(85dvh, 730px);
            min-height: 0;
            overflow: hidden;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, .2);
        }

        .detail-header {
            flex: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 16px 19px;
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-header h2 {
            margin: 0;
            color: #17243c;
            font-size: 16px;
        }

        .detail-header p {
            margin: 5px 0 0;
            color: #94a3b8;
            font-size: 11px;
        }

        .detail-body {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 17px 19px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .detail-item {
            min-width: 0;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 7px;
        }

        .detail-item span {
            display: block;
            margin-bottom: 6px;
            color: #94a3b8;
            font-size: 10px;
        }

        .detail-item strong {
            display: block;
            color: #1e293b;
            font-size: 12px;
            font-weight: 650;
            overflow-wrap: anywhere;
        }

        .detail-footer {
            flex: none;
            display: flex;
            justify-content: flex-end;
            padding: 11px 19px;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 1000px) {
            .monitor-header { padding: 8px 15px; }
            .monitor-main { padding: 10px 15px 8px; }
            .stat { padding: 10px 12px; }
            .stat-icon { width: 31px; height: 31px; }
            .data-table { min-width: 850px; }
        }

        @media (max-width: 700px) {
            :root { --row-height: 64px; }

            .monitor-header {
                height: 58px;
                padding: 8px 12px;
            }

            .brand-logo {
                width: 34px;
                height: 34px;
            }

            .brand h1 {
                max-width: 65vw;
                font-size: 12px;
                line-height: 1.3;
            }

            .brand p { display: none; }
            .header-total { padding: 6px 9px; }
            .header-total strong { font-size: 13px; }

            .monitor-main {
                padding: 8px 10px;
                gap: 8px;
            }

            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 6px;
            }

            .stat {
                min-height: 57px;
                padding: 9px 10px;
            }

            .stat-label { font-size: 10px; }
            .stat-value { font-size: 19px; }
            .stat-icon { width: 28px; height: 28px; font-size: 13px; }

            .toolbar {
                min-height: auto;
                padding: 9px;
                gap: 6px;
                flex-wrap: wrap;
            }

            .search-wrap { flex: 1 0 100%; }

            .btn {
                min-height: 34px;
                padding: 0 10px;
                font-size: 10px;
            }

            .field { height: 36px; }
            .filter-anchor { flex: 1; }
            .filter-anchor > .btn { width: 100%; }
            .table-meta { padding: 7px 10px; }
            .legend { display: none; }

            .data-table {
                min-width: 0;
                width: 100%;
                table-layout: fixed;
            }

            .data-table th,
            .data-table td {
                padding-left: 7px !important;
                padding-right: 7px !important;
            }

            .data-table thead th { font-size: 9px; }
            .data-table tbody td { font-size: 11px; }

            .col-no,
            .col-instansi,
            .col-rt,
            .col-source {
                display: none;
            }

            .cell-primary { font-size: 11px; }
            .cell-sub { font-size: 9px; }

            .action-link {
                padding: 7px 5px;
                font-size: 9px;
            }

            .badge {
                max-width: 100%;
                padding: 5px;
                font-size: 9px;
                white-space: normal;
                line-height: 1.2;
                text-align: center;
            }

            .monitor-bottom { font-size: 9px; }

            .filter-backdrop {
                position: fixed;
                z-index: 39;
                inset: 0;
                display: block;
                background: rgba(15, 23, 42, .48);
            }

            .filter-popover {
                position: fixed;
                z-index: 40;
                top: 50%;
                left: 50%;
                right: auto;
                width: min(440px, calc(100vw - 24px));
                max-height: calc(100dvh - 32px);
                overflow-y: auto;
                transform: translate(-50%, -50%);
            }

            .filter-grid {
                gap: 10px;
                padding: 13px;
            }

            .popover-header { padding: 12px 13px; }
            .popover-footer { padding: 11px 13px; }
            .detail-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 390px) {
            .brand h1 { font-size: 11px; }
            .brand-logo { width: 30px; height: 30px; }
            .header-total { display: none; }
            .export-anchor .btn { padding: 0 8px; }
            .monitor-bottom span:last-child { display: none; }
        }

        @media (max-height: 590px) and (min-width: 701px) {
            .monitor-header { height: 52px; }
            .monitor-main { gap: 6px; padding-top: 7px; padding-bottom: 6px; }
            .stat { min-height: 54px; padding: 7px 11px; }
            .stat-value { font-size: 20px; }
            .toolbar { min-height: 49px; padding-top: 6px; padding-bottom: 6px; }
            .monitor-bottom { display: none; }
        }
    </style>
</head>
<body>
<div class="monitor-shell">
    <header class="monitor-header">
        <div class="brand">
            <div class="brand-logo">▣</div>
            <div class="brand-text">
                <h1>Monitoring Data ASN pada Sensus Ekonomi Tahun 2026</h1>
                <p>Pantau status pendataan pegawai melalui data yang tersedia.</p>
            </div>
        </div>


    </header>

    <main class="monitor-main">
        <section class="stats">
            <div class="stat">
                <div>
                    <span class="stat-label">Total Data</span>
                    <strong class="stat-value">{{ number_format($totalData, 0, ',', '.') }}</strong>
                </div>
                <div class="stat-icon">▣</div>
            </div>

            <div class="stat stat-green">
                <div>
                    <span class="stat-label">Sudah Didata</span>
                    <strong class="stat-value">{{ number_format($sudahDidata, 0, ',', '.') }}</strong>
                </div>
                <div class="stat-icon">✓</div>
            </div>

            <div class="stat stat-red">
                <div>
                    <span class="stat-label">Belum Didata</span>
                    <strong class="stat-value">{{ number_format($belumDidata, 0, ',', '.') }}</strong>
                </div>
                <div class="stat-icon">!</div>
            </div>

            <div class="stat stat-blue">
                <div>
                    <span class="stat-label">Persentase Master</span>
                    <strong class="stat-value">{{ $persentase }}%</strong>
                </div>
                <div class="stat-icon">↗</div>
            </div>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div class="search-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>
                    <input id="searchInput" class="field" type="search" maxlength="100" placeholder="Cari nama, kepala keluarga, assignment ID, wilayah, instansi..." autocomplete="off">
                </div>

                <div class="filter-anchor" id="filterAnchor">
                    <button id="filterButton" class="btn" type="button" aria-expanded="false" aria-controls="filterPopover">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16M7 12h10M10 17h4"></path>
                        </svg>
                        Filter <span id="filterCount"></span>
                    </button>

                    <div id="filterBackdrop" class="filter-backdrop" hidden></div>

                    <div id="filterPopover" class="filter-popover" role="dialog" aria-modal="false" aria-labelledby="filterTitle" hidden>
                        <div class="popover-header">
                            <div>
                                <h2 id="filterTitle">Filter Data Pendataan</h2>
                                <p>Sesuaikan kriteria data yang ingin ditampilkan.</p>
                            </div>
                            <button id="filterClose" class="close-icon" type="button" aria-label="Tutup filter">×</button>
                        </div>

                        <div class="filter-grid">
                            <div class="filter-group">
                                <label for="sourceFilter">Sumber Data</label>
                                <select class="field" id="sourceFilter">
                                    <option value="all">Semua Sumber</option>
                                    <option value="master">Data Master</option>
                                    <option value="manual">Pendaftaran Manual</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="statusFilter">Status Pendataan</label>
                                <select class="field" id="statusFilter">
                                    <option value="">Semua Status</option>
                                    <option value="Belum Didata">Belum Didata</option>
                                    <option value="Sudah Didata">Sudah Didata</option>
                                    <option value="Perlu Tindak Lanjut">Perlu Tindak Lanjut</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="institutionFilter">Instansi</label>
                                <select class="field" id="institutionFilter">
                                    <option value="">Semua Instansi</option>
                                    @foreach($institutions as $institution)
                                        <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="districtFilter">Kecamatan</label>
                                <input class="field" id="districtFilter" type="text" maxlength="255" placeholder="Semua kecamatan">
                            </div>

                            <div class="filter-group">
                                <label for="villageFilter">Kelurahan / Desa</label>
                                <input class="field" id="villageFilter" type="text" maxlength="255" placeholder="Semua kelurahan">
                            </div>
                        </div>

                        <div class="popover-footer">
                            <button id="resetFilterButton" class="btn" type="button">Reset Filter</button>
                            <button id="applyFilterButton" class="btn btn-primary" type="button">Terapkan Filter</button>
                        </div>
                    </div>
                </div>

                <button id="resetButton" class="btn" type="button">Reset</button>

                <div class="export-anchor" id="exportAnchor">
                    <button id="exportButton" class="btn btn-primary" type="button" aria-expanded="false" aria-controls="exportMenu">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 3v12m0 0-4-4m4 4 4-4M5 16v4h14v-4"></path>
                        </svg>
                        Ekspor Excel
                    </button>

                    <div id="exportMenu" class="export-menu" hidden>
                        <a href="{{ route('monitoring.export.all') }}">
                            Ekspor Semua Data
                        </a>
                        <a id="exportFiltered" href="{{ route('monitoring.export.filtered') }}">
                            Ekspor Sesuai Filter
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-meta">
                <div>
                    <strong>Daftar Pendataan Pegawai</strong>
                    <p id="tableSummary">Memuat data bertahap, 50 baris per permintaan.</p>
                </div>

                <div class="legend">
                    <span><i class="legend-dot" style="background:#e11d48"></i>Belum Didata</span>
                    <span><i class="legend-dot" style="background:#059669"></i>Sudah Didata</span>
                    <span><i class="legend-dot" style="background:#7c3aed"></i>Manual</span>
                </div>
            </div>

            <div class="table-scroller" id="tableScroller">
                <table class="data-table" id="dataTable">
                    <colgroup>
                        <col class="col-no" style="width:5%">
                        <col style="width:23%">
                        <col class="col-instansi" style="width:19%">
                        <col style="width:17%">
                        <col class="col-rt" style="width:11%">
                        <col class="col-source" style="width:8%">
                        <col style="width:11%">
                        <col style="width:6%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="col-no">No.</th>
                            <th>Nama Pegawai / NIK</th>
                            <th class="col-instansi">Instansi</th>
                            <th>Wilayah</th>
                            <th class="col-rt">RT / RW</th>
                            <th class="col-source">Sumber</th>
                            <th>Status Pendataan</th>
                            <th style="text-align:center">Detail</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>

            <div class="table-footer">
                <span id="footerInformation">Menampilkan 0 data</span>
                <div class="loading" id="loadingIndicator" hidden>
                    <span class="spinner"></span>
                    Memuat data berikutnya...
                </div>
                <span id="loadState">Siap</span>
            </div>
        </section>

        <footer class="monitor-bottom">
            <span>© {{ now()->year }} Badan Pusat Statistik Kota Probolinggo</span>
            <span>Data ASN • Sensus Ekonomi 2026</span>
        </footer>
    </main>
</div>

<div class="modal" id="detailModal" aria-hidden="true" hidden>
    <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="detailTitle">
        <div class="detail-header">
            <div>
                <h2 id="detailTitle">Detail Data Pegawai</h2>
                <p id="detailSubtitle">Informasi pendataan</p>
            </div>
            <button class="btn" id="closeDetailTop" type="button">Tutup ×</button>
        </div>

        <div class="detail-body">
            <div class="detail-grid" id="detailContent"></div>
        </div>

        <div class="detail-footer">
            <button class="btn btn-primary" id="closeDetailBottom" type="button">Tutup</button>
        </div>
    </div>
</div>

<script>
const INITIAL_DATA = @json($employees);
const INITIAL_CURSOR = @json($nextCursor);
const INITIAL_HAS_MORE = @json($hasMore);
const MONITORING_URL = @json(route('monitoring.index'));
const EXPORT_FILTER_URL = @json(route('monitoring.export.filtered'));

const el = id => document.getElementById(id);

const defaultFilters = () => ({
    search: '',
    source: 'all',
    status: '',
    institution: '',
    kecamatan: '',
    kelurahan: ''
});

const state = {
    rows: Array.isArray(INITIAL_DATA) ? INITIAL_DATA : [],
    cursor: INITIAL_CURSOR,
    hasMore: Boolean(INITIAL_HAS_MORE),
    activeFilters: defaultFilters(),
    loading: false,
    loadingNext: false,
    requestController: null,
    prefetchController: null,
    prefetchPromise: null,
    prefetched: null,
    revision: 0,
    frame: null
};

const escapeHtml = value => String(value ?? '')
    .replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    }[character]));

const showValue = value => {
    if (value === null || value === undefined || value === '') return '-';
    return String(value);
};

const formatNumber = number => new Intl.NumberFormat('id-ID').format(number);
const getRowHeight = () => window.matchMedia('(max-width: 700px)').matches ? 64 : 56;

const readFilterInputs = () => ({
    search: el('searchInput').value.trim(),
    source: el('sourceFilter').value,
    status: el('statusFilter').value,
    institution: el('institutionFilter').value,
    kecamatan: el('districtFilter').value.trim(),
    kelurahan: el('villageFilter').value.trim()
});

const fillFilterInputs = () => {
    const filters = state.activeFilters;
    el('sourceFilter').value = filters.source;
    el('statusFilter').value = filters.status;
    el('institutionFilter').value = filters.institution;
    el('districtFilter').value = filters.kecamatan;
    el('villageFilter').value = filters.kelurahan;
};

const updateFilterCount = () => {
    const filters = state.activeFilters;

    const count = [
        filters.search,
        filters.source !== 'all' ? filters.source : '',
        filters.status,
        filters.institution,
        filters.kecamatan,
        filters.kelurahan
    ].filter(Boolean).length;

    el('filterCount').textContent = count ? `(${count})` : '';
    el('filterButton').classList.toggle('active', count > 0);
};

const openFilter = () => {
    fillFilterInputs();
    el('filterPopover').hidden = false;
    el('filterBackdrop').hidden = false;
    el('filterButton').setAttribute('aria-expanded', 'true');
};

const closeFilter = () => {
    el('filterPopover').hidden = true;
    el('filterBackdrop').hidden = true;
    el('filterButton').setAttribute('aria-expanded', 'false');
};

const createUrl = (cursor = null) => {
    const url = new URL(MONITORING_URL, window.location.origin);
    url.searchParams.set('data', '1');

    for (const [key, value] of Object.entries(state.activeFilters)) {
        if (value && !(key === 'source' && value === 'all')) {
            url.searchParams.set(key, value);
        }
    }

    if (cursor) {
        url.searchParams.set('phase', cursor.phase);
        url.searchParams.set('after', String(cursor.after));
    }

    return url.toString();
};

const fetchPage = async (cursor = null, signal = null) => {
    const response = await fetch(createUrl(cursor), {
        method: 'GET',
        credentials: 'same-origin',
        signal,
        headers: { Accept: 'application/json' }
    });

    if (!response.ok) {
        throw new Error(`Gagal memuat data (${response.status}).`);
    }

    const result = await response.json();

    if (!result || !Array.isArray(result.data)) {
        throw new Error('Format data monitoring tidak valid.');
    }

    return result;
};

const getStatusClass = status => {
    if (status === 'Sudah Didata') return 'badge-success';
    if (status === 'Belum Didata') return 'badge-danger';
    return 'badge-warning';
};

const renderRow = (row, index) => {
    const source = row.source === 'manual' ? 'Manual' : 'Master';

    return `
        <tr class="data-row">
            <td class="col-no">${formatNumber(index + 1)}</td>
            <td>
                <div class="cell-primary" title="${escapeHtml(showValue(row.nama))}">
                    ${escapeHtml(showValue(row.nama))}
                </div>
                <div class="cell-sub">
                    NIK: ${escapeHtml(showValue(row.nik_masked))}
                    ${row.source === 'manual' ? ' • Manual' : ''}
                </div>
            </td>
            <td class="col-instansi">
                <div class="cell-ellipsis" title="${escapeHtml(showValue(row.instansi))}">
                    ${escapeHtml(showValue(row.instansi))}
                </div>
            </td>
            <td>
                <div class="cell-primary">${escapeHtml(showValue(row.kecamatan))}</div>
                <div class="cell-sub">${escapeHtml(showValue(row.kelurahan))}</div>
            </td>
            <td class="col-rt">${escapeHtml(showValue(row.rt_rw))}</td>
            <td class="col-source">
                <span class="badge ${row.source === 'manual' ? 'badge-manual' : 'badge-source'}">
                    ${source}
                </span>
            </td>
            <td>
                <span class="badge ${getStatusClass(row.sudah_didata)}">
                    ${escapeHtml(showValue(row.sudah_didata))}
                </span>
            </td>
            <td style="text-align:center">
                <button class="action-link" type="button" data-detail-index="${index}">
                    Detail
                </button>
            </td>
        </tr>
    `;
};

const renderVirtualRows = () => {
    const tbody = el('tableBody');
    const scroller = el('tableScroller');

    if (!state.rows.length) {
        tbody.innerHTML = `
            <tr>
                <td class="empty-state" colspan="8">
                    Tidak ada data yang sesuai dengan pencarian atau filter.
                </td>
            </tr>
        `;
        return;
    }

    const rowHeight = getRowHeight();
    const overscan = 8;
    const scrollPosition = Math.max(0, scroller.scrollTop - 37);
    const visibleCount = Math.ceil(scroller.clientHeight / rowHeight) + 1;
    const start = Math.max(0, Math.floor(scrollPosition / rowHeight) - overscan);
    const end = Math.min(state.rows.length, start + visibleCount + overscan * 2);
    const topHeight = start * rowHeight;
    const bottomHeight = (state.rows.length - end) * rowHeight;

    let html = '';

    if (topHeight > 0) {
        html += `
            <tr class="spacer-row">
                <td colspan="8" style="height:${topHeight}px"></td>
            </tr>
        `;
    }

    for (let index = start; index < end; index++) {
        html += renderRow(state.rows[index], index);
    }

    if (bottomHeight > 0) {
        html += `
            <tr class="spacer-row">
                <td colspan="8" style="height:${bottomHeight}px"></td>
            </tr>
        `;
    }

    tbody.innerHTML = html;
};

const scheduleRender = () => {
    if (state.frame !== null) return;

    state.frame = requestAnimationFrame(() => {
        state.frame = null;
        renderVirtualRows();
    });
};

const updateTableInfo = () => {
    const total = state.rows.length;

    el('tableSummary').textContent = `Menampilkan ${formatNumber(total)} data yang sudah dimuat.`;
    el('footerInformation').textContent = `${formatNumber(total)} baris ditampilkan`;
    el('loadState').textContent = state.hasMore
        ? 'Gulir untuk melihat data berikutnya'
        : 'Semua hasil telah ditampilkan';
};

const setLoading = loading => {
    el('loadingIndicator').hidden = !loading;
};

const prefetchNext = () => {
    if (!state.hasMore || !state.cursor || state.prefetched || state.prefetchPromise) {
        return;
    }

    const revision = state.revision;
    const cursor = { ...state.cursor };
    const controller = new AbortController();

    state.prefetchController = controller;

    const promise = fetchPage(cursor, controller.signal)
        .then(result => {
            if (revision === state.revision) state.prefetched = result;
            return result;
        })
        .catch(error => {
            if (error.name !== 'AbortError') console.error(error);
            return null;
        })
        .finally(() => {
            if (state.prefetchPromise === promise) {
                state.prefetchPromise = null;
            }
        });

    state.prefetchPromise = promise;
};

const schedulePrefetch = () => {
    const revision = state.revision;

    setTimeout(() => {
        if (revision === state.revision) prefetchNext();
    }, 300);
};

const loadNext = async () => {
    if (!state.hasMore || !state.cursor || state.loading || state.loadingNext) {
        return;
    }

    state.loadingNext = true;
    setLoading(true);

    const revision = state.revision;

    try {
        if (state.prefetchPromise) await state.prefetchPromise;

        let result = state.prefetched;

        if (!result) {
            const controller = new AbortController();
            state.requestController = controller;
            result = await fetchPage(state.cursor, controller.signal);
        }

        if (revision !== state.revision) return;

        state.prefetched = null;

        const existing = new Set(state.rows.map(row => row.key));

        for (const row of result.data) {
            if (!existing.has(row.key)) {
                state.rows.push(row);
                existing.add(row.key);
            }
        }

        state.cursor = result.next_cursor;
        state.hasMore = Boolean(result.has_more);

        renderVirtualRows();
        updateTableInfo();
        schedulePrefetch();
    } catch (error) {
        if (error.name !== 'AbortError' && revision === state.revision) {
            el('loadState').textContent = 'Gagal memuat data. Gulir untuk mencoba lagi.';
            console.error(error);
        }
    } finally {
        if (revision === state.revision) {
            state.loadingNext = false;
            setLoading(false);
        }
    }
};

const refreshData = async () => {
    const revision = ++state.revision;

    state.requestController?.abort();
    state.prefetchController?.abort();

    state.rows = [];
    state.cursor = null;
    state.hasMore = false;
    state.prefetched = null;
    state.prefetchPromise = null;
    state.loading = true;
    state.loadingNext = false;

    el('tableScroller').scrollTop = 0;
    el('tableBody').innerHTML = `
        <tr>
            <td class="empty-state" colspan="8">Memuat data...</td>
        </tr>
    `;

    updateFilterCount();
    setLoading(true);

    const controller = new AbortController();
    state.requestController = controller;

    try {
        const result = await fetchPage(null, controller.signal);

        if (revision !== state.revision) return;

        state.rows = result.data;
        state.cursor = result.next_cursor;
        state.hasMore = Boolean(result.has_more);

        renderVirtualRows();
        updateTableInfo();
        schedulePrefetch();
    } catch (error) {
        if (error.name !== 'AbortError' && revision === state.revision) {
            el('tableBody').innerHTML = `
                <tr>
                    <td class="empty-state" colspan="8">
                        ${escapeHtml(error.message || 'Data tidak dapat dimuat.')}
                    </td>
                </tr>
            `;
            el('loadState').textContent = 'Gagal memuat data';
            console.error(error);
        }
    } finally {
        if (revision === state.revision) {
            state.loading = false;
            setLoading(false);
        }
    }
};

const detailFields = [
    ['Nama Pegawai', 'nama'],
    ['NIK', 'nik_masked'],
    ['Sumber Data', 'source'],
    ['Status Pendataan', 'sudah_didata'],
    ['Instansi', 'instansi'],
    ['Nama Kepala Keluarga', 'nama_kepala_keluarga'],
    ['Nomor KK', 'no_kk'],
    ['Kecamatan', 'kecamatan'],
    ['Kelurahan / Desa', 'kelurahan'],
    ['RT / RW', 'rt_rw'],
    ['Alamat', 'alamat'],
    ['ID Sub SLS', 'idsubsls'],
    ['Assignment ID', 'assignment_id'],
    ['Code Identity', 'code_identity'],
    ['Status Assignment', 'assignment_status_alias'],
    ['Profesi', 'profesi'],
    ['Status Kerja', 'status_kerja_label'],
    ['Profesi Lainnya', 'profesi_lainnya'],
    ['Assignment ID SE2026', 'respSE26_assignment_id'],
    ['Nomor KK SE2026', 'respSE26_no_kk'],
    ['Nama SE2026', 'respSE26_nama'],
    ['Code Identity SE2026', 'respSE26_code_identity'],
    ['Keberadaan Keluarga SE2026', 'respSE26_keberadaan_klrg']
];

const openDetail = index => {
    const row = state.rows[index];

    if (!row) return;

    el('detailTitle').textContent = showValue(row.nama);
    el('detailSubtitle').textContent = row.source === 'manual'
        ? 'Pendaftaran Manual'
        : 'Data Master';

    el('detailContent').innerHTML = detailFields.map(([label, key]) => {
        let value = row[key];

        if (key === 'source') {
            value = row.source === 'manual' ? 'Pendaftaran Manual' : 'Data Master';
        }

        return `
            <div class="detail-item">
                <span>${escapeHtml(label)}</span>
                <strong>${escapeHtml(showValue(value))}</strong>
            </div>
        `;
    }).join('');

    el('detailModal').hidden = false;
    el('detailModal').setAttribute('aria-hidden', 'false');
};

const closeDetail = () => {
    el('detailModal').hidden = true;
    el('detailModal').setAttribute('aria-hidden', 'true');
};

const clearAllFilters = () => {
    state.activeFilters = defaultFilters();
    el('searchInput').value = '';
    fillFilterInputs();
    updateFilterCount();
    closeFilter();
    refreshData();
};

let searchTimer = null;

const refreshSearch = () => {
    clearTimeout(searchTimer);

    searchTimer = setTimeout(() => {
        state.activeFilters.search = el('searchInput').value.trim();
        refreshData();
    }, 350);
};

el('filterButton').addEventListener('click', () => {
    if (el('filterPopover').hidden) openFilter();
    else closeFilter();
});

el('filterClose').addEventListener('click', closeFilter);
el('filterBackdrop').addEventListener('click', closeFilter);

el('applyFilterButton').addEventListener('click', () => {
    state.activeFilters = readFilterInputs();
    closeFilter();
    refreshData();
});

el('resetFilterButton').addEventListener('click', () => {
    el('sourceFilter').value = 'all';
    el('statusFilter').value = '';
    el('institutionFilter').value = '';
    el('districtFilter').value = '';
    el('villageFilter').value = '';

    state.activeFilters = readFilterInputs();
    closeFilter();
    refreshData();
});

el('resetButton').addEventListener('click', () => {
    clearTimeout(searchTimer);
    clearAllFilters();
});

el('searchInput').addEventListener('input', refreshSearch);

el('tableBody').addEventListener('click', event => {
    const button = event.target.closest('[data-detail-index]');
    if (button) openDetail(Number(button.dataset.detailIndex));
});

el('closeDetailTop').addEventListener('click', closeDetail);
el('closeDetailBottom').addEventListener('click', closeDetail);

el('detailModal').addEventListener('click', event => {
    if (event.target === el('detailModal')) closeDetail();
});

document.addEventListener('pointerdown', event => {
    if (
        !el('filterPopover').hidden &&
        !el('filterAnchor').contains(event.target)
    ) {
        closeFilter();
    }

    if (
        !el('exportMenu').hidden &&
        !el('exportAnchor').contains(event.target)
    ) {
        el('exportMenu').hidden = true;
        el('exportButton').setAttribute('aria-expanded', 'false');
    }
});

document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;

    closeFilter();
    closeDetail();
    el('exportMenu').hidden = true;
    el('exportButton').setAttribute('aria-expanded', 'false');
});

let scrollFrame = null;

el('tableScroller').addEventListener('scroll', () => {
    if (scrollFrame !== null) return;

    scrollFrame = requestAnimationFrame(() => {
        scrollFrame = null;
        renderVirtualRows();

        const scroller = el('tableScroller');
        const distance = scroller.scrollHeight - scroller.scrollTop - scroller.clientHeight;

        if (distance < 450) loadNext();
    });
}, { passive: true });

window.addEventListener('resize', scheduleRender);

el('exportButton').addEventListener('click', () => {
    const menu = el('exportMenu');
    const willOpen = menu.hidden;

    menu.hidden = !willOpen;

    el('exportButton').setAttribute(
        'aria-expanded',
        String(willOpen)
    );
});

el('exportFiltered').addEventListener('click', event => {
    event.preventDefault();

    const url = new URL(EXPORT_FILTER_URL, window.location.origin);

    for (const [key, value] of Object.entries(state.activeFilters)) {
        if (
            ['search', 'source', 'institution', 'status', 'kecamatan', 'kelurahan'].includes(key) &&
            value &&
            value !== 'all'
        ) {
            url.searchParams.set(key, value);
        }
    }

    window.location.assign(url.toString());
});

updateFilterCount();
renderVirtualRows();
updateTableInfo();
schedulePrefetch();
</script>
</body>
</html>
