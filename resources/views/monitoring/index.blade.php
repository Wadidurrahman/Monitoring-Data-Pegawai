<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Monitoring Data</title>

    @vite(['resources/css/monitoring.css', 'resources/js/monitoring/app.js'])


    <style>
        [x-cloak] {
            display: none !important;
        }

        /* =========================
           PAGE LOAD
        ========================= */

        .page-load {
            animation: pageLoad .8s cubic-bezier(.22, 1, .36, 1);
        }

        @keyframes pageLoad {
            0% {
                opacity: 0;
                transform: translateY(12px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* =========================
           FADE
        ========================= */

        .fade-in {
            animation: fadeIn .7s ease-out both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .fade-in-slow {
            animation: fadeInSlow 1s ease-out both;
        }

        @keyframes fadeInSlow {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* =========================
           SLIDE
        ========================= */

        .slide-up {
            animation: slideUp .7s cubic-bezier(.22, 1, .36, 1) both;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-up-fast {
            animation: slideUpFast .5s cubic-bezier(.22, 1, .36, 1) both;
        }

        @keyframes slideUpFast {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-down {
            animation: slideDown .7s cubic-bezier(.22, 1, .36, 1) both;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* =========================
           SCALE
        ========================= */

        .scale-in {
            animation: scaleIn .25s cubic-bezier(.22, 1, .36, 1) both;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* =========================
           FLOAT
        ========================= */

        .float {
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* =========================
           PULSE
        ========================= */

        .pulse-soft {
            animation: pulseSoft 2s ease-in-out infinite;
        }

        @keyframes pulseSoft {
            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .75;
            }
        }

        /* =========================
           SHIMMER
        ========================= */

        .shimmer {
            position: relative;
            overflow: hidden;
        }

        .shimmer::after {
            content: "";
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, .4),
                transparent
            );
            animation: shimmer 2.5s infinite;
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        /* =========================
           MODAL
        ========================= */

        .modal-backdrop {
            animation: modalBackdrop .2s ease-out both;
        }

        @keyframes modalBackdrop {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            animation: modalContent .3s cubic-bezier(.22, 1, .36, 1) both;
        }

        @keyframes modalContent {
            from {
                opacity: 0;
                transform: translateY(20px) scale(.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* =========================
           SMOOTH & RESPONSIVE
        ========================= */

        html {
            scroll-behavior: smooth;
        }

        body {
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        button,
        input,
        select,
        tr,
        span,
        div {
            -webkit-tap-highlight-color: transparent;
        }

        /* =========================
           TOUCH DEVICE
        ========================= */

        @media (hover: none) {
            button:hover,
            tr:hover {
                background-color: inherit;
            }
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 640px) {

            .page-load {
                animation-duration: .55s;
            }

            .slide-up {
                animation-duration: .55s;
            }

            .slide-down {
                animation-duration: .55s;
            }

            main {
                padding-top: 1.25rem !important;
                padding-bottom: 1.25rem !important;
            }

            .hero-mobile {
                padding: 1.25rem !important;
                border-radius: 1.5rem !important;
            }

            .modal-mobile {
                max-height: calc(100dvh - 1rem) !important;
                border-radius: 1.5rem !important;
            }

            .modal-body-mobile {
                max-height: calc(100dvh - 210px) !important;
            }

            .table-scroll {
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }

            .custom-scrollbar {
                -webkit-overflow-scrolling: touch;
            }
        }

        /* =========================
           TABLET
        ========================= */

        @media (min-width: 641px) and (max-width: 1024px) {

            main {
                padding-top: 1.5rem !important;
            }
        }

        /* =========================
           VERY SMALL PHONE
        ========================= */

        @media (max-width: 380px) {

            .mobile-compact {
                padding-left: .875rem !important;
                padding-right: .875rem !important;
            }

            .mobile-title {
                font-size: 1.25rem !important;
            }

            .mobile-hero-title {
                font-size: 1.5rem !important;
            }
        }

        /* =========================
           REDUCE MOTION
        ========================= */

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 overflow-x-hidden">

<div
    x-data="monitoringData()"
    class="min-h-screen page-load"
    @keydown.escape.window="closeDetail()"
>

@include('monitoring.components.header')


    <!-- =========================================================
         MAIN
    ========================================================== -->

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mobile-compact">

        <!-- =====================================================
             HERO
        ====================================================== -->

        <section class="mb-7 slide-up">

            <div class="hero-mobile relative overflow-hidden rounded-3xl bg-linear-to-br from-blue-600 via-blue-600 to-indigo-700 p-7 sm:p-9 text-white shadow-xl shadow-blue-100">

                {{-- <div class="absolute -top-20 -right-20 w-60 h-60 bg-white/10 rounded-full blur-2xl"></div>

                <div class="absolute -bottom-24 -left-16 w-72 h-72 bg-indigo-400/20 rounded-full blur-3xl"></div> --}}

                {{-- <div class="relative z-10 max-w-2xl">

                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/15 border border-white/20 text-xs font-semibold mb-4">

                        <span class="w-2 h-2 rounded-full bg-emerald-300 pulse-soft"></span>

                        Sistem Monitoring Aktif

                    </span>

                    <h2 class="mobile-hero-title text-2xl sm:text-3xl font-bold tracking-tight">
                        Monitoring Pendataan
                    </h2>

                    <p class="mt-3 text-blue-100 leading-relaxed">
                        Pantau status pendataan setiap individu secara cepat
                        melalui data yang tersedia.
                    </p>

                </div> --}}

            </div>

        </section>


        <!-- =====================================================
             STATISTICS
        ====================================================== -->

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-7">

            <!-- TOTAL -->

            <div
                class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm slide-up"
                style="animation-delay:.08s"
            >

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Total Data
                        </p>

                        <p
                            class="text-3xl font-bold text-slate-900 mt-2"
                            x-text="filteredEmployees.length"
                        ></p>

                    </div>

                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M17 20h5V4H2v16h5m10 0v-4H7v4m10 0H7"
                            />
                        </svg>

                    </div>

                </div>

            </div>


            <!-- SUDAH -->

            <div
                class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm slide-up"
                style="animation-delay:.14s"
            >

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Sudah Didata
                        </p>

                        <p
                            class="text-3xl font-bold text-emerald-600 mt-2"
                            x-text="filteredSudahDidata"
                        ></p>

                    </div>

                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>

                    </div>

                </div>

            </div>


            <!-- BELUM -->

            <div
                class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm slide-up"
                style="animation-delay:.20s"
            >

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Belum Didata
                        </p>

                        <p
                            class="text-3xl font-bold text-rose-600 mt-2"
                            x-text="filteredBelumDidata"
                        ></p>

                    </div>

                    <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v4m0 4h.01M10.3 3.5L2.8 17a2 2 0 001.75 3h14.9a2 2 0 001.75-3L13.7 3.5a2 2 0 00-3.4 0z"
                            />
                        </svg>

                    </div>

                </div>

            </div>


            <!-- PERSENTASE -->

            <div
                class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm slide-up"
                style="animation-delay:.26s"
            >

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Persentase
                        </p>

                        <p
                            class="text-3xl font-bold text-blue-600 mt-2"
                            x-text="filteredPercentage + '%'"
                        ></p>

                    </div>

                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 19V5m0 14h16M8 16V9m4 7V6m4 10v-4"
                            />
                        </svg>

                    </div>

                </div>

                <div class="mt-4 h-2 bg-slate-100 rounded-full overflow-hidden">

                    <div
                        class="h-full bg-linear-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-500"
                        :style="`width: ${filteredPercentage}%`"
                    ></div>

                </div>

            </div>

        </section>


        <!-- =====================================================
             FILTER & SEARCH
        ====================================================== -->

        <section
            class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-7 slide-up"
            style="animation-delay:.30s"
        >

            <div class="p-5">

                <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                    <!-- SEARCH -->

                    <div class="relative flex-1">

                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 text-slate-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35m2.1-5.4a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z"
                                />
                            </svg>

                        </div>

                        <input
                            type="text"
                            x-model="search"
                            placeholder="Cari nama, kepala keluarga, assignment ID, wilayah, instansi..."
                            class="w-full min-h-11 pl-11 pr-10 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition"
                        >

                        <button
                            x-show="search"
                            x-cloak
                            @click="search = ''"
                            type="button"
                            class="absolute inset-y-0 right-0 pr-4 text-slate-400 hover:text-slate-600"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>

                        </button>

                    </div>


                    <!-- FILTER BUTTON -->

                    <button
                        type="button"
                        @click="showFilters = !showFilters"
                        class="inline-flex items-center justify-center gap-2 min-h-11 px-4 py-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 active:scale-[.98] transition-all duration-200 font-medium text-slate-700"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 4h18M6 10h12M10 16h4M9 20h6"
                            />
                        </svg>

                        Filter

                        <span
                            x-show="activeFilterCount > 0"
                            x-cloak
                            x-text="activeFilterCount"
                            class="min-w-5 h-5 px-1.5 rounded-full bg-blue-600 text-white text-xs flex items-center justify-center"
                        ></span>

                    </button>

                </div>


                <!-- FILTER PANEL -->

                <div
                    x-show="showFilters"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="mt-5 pt-5 border-t border-slate-100"
                >

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                        <!-- KECAMATAN -->


                        <!-- INSTANSI -->

                        <div>

                            <label class="block text-sm font-medium text-slate-600 mb-2">
                                Instansi
                            </label>

                            <select
                                x-model="institution"
                                class="w-full min-h-11 px-3 py-2.5 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition"
                            >

                                <option value="">
                                    Semua Instansi
                                </option>

                                @foreach($institutions as $item)

                                    <option value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <!-- STATUS -->

                        <div>

                            <label class="block text-sm font-medium text-slate-600 mb-2">
                                Status Pendataan
                            </label>

                            <select
                                x-model="status"
                                class="w-full min-h-11 px-3 py-2.5 rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition"
                            >

                                <option value="">
                                    Semua Status
                                </option>

                                <option value="Sudah Didata">
                                    Sudah Didata
                                </option>

                                <option value="Belum Didata">
                                    Belum Didata
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- RESET -->

                    <div class="mt-4 flex justify-end">

                        <button
                            type="button"
                            @click="resetFilters()"
                            class="inline-flex items-center gap-2 min-h-10 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 hover:text-blue-600 hover:bg-blue-50 active:scale-[.98] transition-all duration-200"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 4v5h5M20 20v-5h-5M5.5 9A7.5 7.5 0 0118 6.5M18.5 15A7.5 7.5 0 016 17.5"
                                />
                            </svg>

                            Reset Filter

                        </button>

                    </div>

                </div>

            </div>

        </section>


        <!-- =====================================================
             TABLE
        ====================================================== -->

        <section
            class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden slide-up"
            style="animation-delay:.36s"
        >

            <!-- TABLE HEADER -->

            <div class="px-5 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                <div>

                    <h3 class="font-bold text-slate-900">
                        Data Pendataan
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">

                        Menampilkan

                        <span
                            class="font-semibold text-slate-700"
                            x-text="filteredEmployees.length"
                        ></span>

                        data

                    </p>

                </div>


                <div
                    x-show="activeFilterCount > 0"
                    x-cloak
                    class="text-xs text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full font-medium"
                >
                    Filter aktif
                </div>

            </div>


            <!-- TABLE -->

            <div class="overflow-x-auto table-scroll">

                <table class="w-full min-w-[900px] text-left">

                    <thead>

                        <tr class="bg-slate-50 border-b border-slate-200">

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                #
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Nama
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Kepala Keluarga
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Wilayah
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Instansi
                            </th>

                            <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        <template
                            x-for="(employee, index) in filteredEmployees"
                            :key="employee.id"
                        >

                            <tr class="hover:bg-slate-50/80 transition duration-200">

                                <!-- NO -->

                                <td class="px-5 py-4">

                                    <span
                                        class="text-sm text-slate-400 font-medium"
                                        x-text="index + 1"
                                    ></span>

                                </td>


                                <!-- NAMA -->

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="shrink-0 w-10 h-10 rounded-xl bg-linear-to-br from-blue-50 to-indigo-100 text-blue-600 flex items-center justify-center font-bold"
                                            x-text="(employee.nama || 'N').charAt(0).toUpperCase()"
                                        ></div>

                                        <div class="min-w-0">

                                            <p
                                                class="font-semibold text-slate-900 truncate max-w-[220px]"
                                                x-text="employee.nama || '-'"
                                            ></p>

                                            <p
                                                x-show="employee.assignment_id"
                                                class="text-xs text-slate-400 mt-0.5 truncate max-w-[220px]"
                                                x-text="employee.assignment_id"
                                            ></p>

                                        </div>

                                    </div>

                                </td>


                                <!-- KEPALA KELUARGA -->

                                <td class="px-5 py-4">

                                    <span
                                        class="text-slate-700 font-medium"
                                        x-text="employee.nama_kepala_keluarga || '-'"
                                    ></span>

                                </td>


                                <!-- WILAYAH -->

                                <td class="px-5 py-4">

                                    <div>

                                        <p
                                            class="text-sm font-medium text-slate-700"
                                            x-text="employee.kecamatan || '-'"
                                        ></p>

                                        <p
                                            class="text-xs text-slate-400 mt-0.5"
                                            x-text="employee.kelurahan || '-'"
                                        ></p>

                                    </div>

                                </td>


                                <!-- INSTANSI -->

                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium max-w-[200px] truncate"
                                        x-text="employee.institution?.name || employee.instansi || '-'"
                                    ></span>

                                </td>


                                <!-- STATUS -->

                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap"
                                        :class="
                                            employee.sudah_didata === 'Sudah Didata'
                                                ? 'bg-emerald-50 text-emerald-700'
                                                : 'bg-rose-50 text-rose-700'
                                        "
                                    >

                                        <span
                                            class="w-1.5 h-1.5 rounded-full"
                                            :class="
                                                employee.sudah_didata === 'Sudah Didata'
                                                    ? 'bg-emerald-500'
                                                    : 'bg-rose-500'
                                            "
                                        ></span>

                                        <span
                                            x-text="employee.sudah_didata || '-'"
                                        ></span>

                                    </span>

                                </td>


                                <!-- DETAIL -->

                                <td class="px-5 py-4 text-center">

                                    <button
                                        type="button"
                                        @click="openDetail(employee)"
                                        class="inline-flex items-center justify-center gap-2 min-h-10 px-3.5 py-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white active:scale-95 transition-all duration-200 font-semibold text-sm"
                                    >

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />

                                        </svg>

                                        Detail

                                    </button>

                                </td>

                            </tr>

                        </template>


                        <!-- EMPTY -->

                        <tr
                            x-show="filteredEmployees.length === 0"
                            x-cloak
                        >

                            <td colspan="7" class="px-5 py-16">

                                <div class="text-center">

                                    <div class="mx-auto w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="w-8 h-8"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M21 21l-4.35-4.35m2.1-5.4a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z"
                                            />
                                        </svg>

                                    </div>

                                    <h4 class="mt-4 font-semibold text-slate-700">
                                        Data tidak ditemukan
                                    </h4>

                                    <p class="mt-1 text-sm text-slate-400">
                                        Coba ubah kata pencarian atau filter.
                                    </p>

                                    <button
                                        type="button"
                                        @click="resetFilters()"
                                        class="mt-4 min-h-10 px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 active:scale-[.98] transition-all duration-200"
                                    >
                                        Reset Filter
                                    </button>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </section>


        <!-- =====================================================
             FOOTER
        ====================================================== -->

        <footer class="py-7 text-center fade-in-slow">

            <p class="text-sm text-slate-400">
                Data dapat dicari dan difilter secara langsung tanpa reload halaman.
            </p>

        </footer>

    </main>


    <!-- =========================================================
         DETAIL MODAL
    ========================================================== -->

    <div
        x-show="detailModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-6"
    >

        <!-- BACKDROP -->

        <div
            class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm modal-backdrop"
            @click="closeDetail()"
        ></div>


        <!-- MODAL -->

        <div
            x-show="detailModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-5 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-5 scale-95"
            class="modal-mobile relative w-full max-w-3xl max-h-[90dvh] bg-white rounded-3xl shadow-2xl overflow-hidden modal-content"
            @click.stop
        >

            <!-- MODAL HEADER -->

            <div class="relative overflow-hidden bg-linear-to-br from-blue-600 to-indigo-700 text-white">

                <div class="absolute -top-20 -right-20 w-48 h-48 rounded-full bg-white/10"></div>

                <div class="relative px-4 py-5 sm:px-7 sm:py-6">

                    <div class="flex items-start justify-between gap-3">

                        <div class="flex items-center gap-3 sm:gap-4 min-w-0">

                            <!-- AVATAR -->

                            <div
                                class="flex-shrink-0 w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center text-xl sm:text-2xl font-bold shadow-lg"
                                x-text="selectedEmployee ? (selectedEmployee.nama || 'N').charAt(0).toUpperCase() : 'N'"
                            ></div>


                            <div class="min-w-0">

                                <p class="text-blue-100 text-sm">
                                    Detail Data
                                </p>

                                <h2
                                    class="text-lg sm:text-2xl font-bold mt-0.5 truncate"
                                    x-text="selectedEmployee?.nama || '-'"
                                ></h2>

                                <p
                                    class="text-xs sm:text-sm text-blue-100 mt-1 truncate"
                                    x-text="selectedEmployee?.assignment_id || 'Tidak ada Assignment ID'"
                                ></p>

                            </div>

                        </div>


                        <!-- CLOSE -->

                        <button
                            type="button"
                            @click="closeDetail()"
                            class="flex-shrink-0 w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 flex items-center justify-center transition active:scale-95"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>

                        </button>

                    </div>


                    <!-- STATUS HEADER -->

                    <div class="mt-5 flex flex-wrap items-center gap-2">

                        <span
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/15 border border-white/20 text-sm font-semibold"
                        >

                            <span
                                class="w-2 h-2 rounded-full"
                                :class="
                                    selectedEmployee?.sudah_didata === 'Sudah Didata'
                                        ? 'bg-emerald-300'
                                        : 'bg-rose-300'
                                "
                            ></span>

                            <span
                                x-text="selectedEmployee?.sudah_didata || '-'"
                            ></span>

                        </span>


                        <span
                            x-show="selectedEmployee?.status"
                            x-text="selectedEmployee?.status"
                            class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/10 border border-white/10 text-sm"
                        ></span>

                    </div>

                </div>

            </div>


            <!-- MODAL BODY -->

            <div class="modal-body-mobile overflow-y-auto custom-scrollbar max-h-[calc(90dvh-230px)]">

                <div class="p-4 sm:p-7 space-y-6 sm:space-y-7">

                    <!-- =================================================
                         IDENTITAS
                    ================================================== -->

                    <div>

                        <div class="flex items-center gap-3 mb-4">

                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM4 21a8 8 0 0116 0"
                                    />
                                </svg>

                            </div>

                            <div>

                                <h3 class="font-bold text-slate-900">
                                    Identitas
                                </h3>

                                <p class="text-xs text-slate-400">
                                    Informasi identitas data
                                </p>

                            </div>

                        </div>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <!-- NAMA -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Nama
                                </p>

                                <p
                                    class="font-semibold text-slate-800 wrap-break-word"
                                    x-text="selectedEmployee?.nama || '-'"
                                ></p>

                            </div>


                            <!-- KEPALA KELUARGA -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Nama Kepala Keluarga
                                </p>

                                <p
                                    class="font-semibold text-slate-800 wrap-break-word"
                                    x-text="selectedEmployee?.nama_kepala_keluarga || '-'"
                                ></p>

                            </div>


                            <!-- NIK -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    NIK
                                </p>

                                <p
                                    class="font-semibold text-slate-800 break-all"
                                    x-text="selectedEmployee?.nik_lookup || selectedEmployee?.nik_encrypted || '-'"
                                ></p>

                            </div>


                            <!-- NO KK -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    No. KK
                                </p>

                                <p
                                    class="font-semibold text-slate-800 break-all"
                                    x-text="selectedEmployee?.no_kk || '-'"
                                ></p>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         WILAYAH
                    ================================================== -->

                    <div>

                        <div class="flex items-center gap-3 mb-4">

                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 21s8-4.5 8-10a8 8 0 10-16 0c0 5.5 8 10 8 10z"
                                    />

                                    <circle
                                        cx="12"
                                        cy="11"
                                        r="2.5"
                                    />

                                </svg>

                            </div>

                            <div>

                                <h3 class="font-bold text-slate-900">
                                    Wilayah
                                </h3>

                                <p class="text-xs text-slate-400">
                                    Informasi lokasi pendataan
                                </p>

                            </div>

                        </div>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <!-- KECAMATAN -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Kecamatan
                                </p>

                                <p
                                    class="font-semibold text-slate-800"
                                    x-text="selectedEmployee?.kecamatan || '-'"
                                ></p>

                            </div>


                            <!-- KELURAHAN -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Kelurahan
                                </p>

                                <p
                                    class="font-semibold text-slate-800"
                                    x-text="selectedEmployee?.kelurahan || '-'"
                                ></p>

                            </div>


                            <!-- RT RW -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    RT / RW
                                </p>

                                <p
                                    class="font-semibold text-slate-800"
                                    x-text="selectedEmployee?.rt_rw || '-'"
                                ></p>

                            </div>


                            <!-- ID SLS -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    ID SLS / SubSLS
                                </p>

                                <p
                                    class="font-semibold text-slate-800 break-all"
                                    x-text="selectedEmployee?.idsubsls || '-'"
                                ></p>

                            </div>


                            <!-- ALAMAT -->

                            <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Alamat
                                </p>

                                <p
                                    class="font-semibold text-slate-800 leading-relaxed wrap-break-word"
                                    x-text="selectedEmployee?.alamat || '-'"
                                ></p>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         PENDATAAN
                    ================================================== -->

                    <div>

                        <div class="flex items-center gap-3 mb-4">

                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12l2 2 4-4M7 4h10a2 2 0 012 2v14H5V6a2 2 0 012-2z"
                                    />
                                </svg>

                            </div>

                            <div>

                                <h3 class="font-bold text-slate-900">
                                    Informasi Pendataan
                                </h3>

                                <p class="text-xs text-slate-400">
                                    Status dan informasi penugasan
                                </p>

                            </div>

                        </div>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <!-- STATUS PENDATAAN -->

                            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50">

                                <p class="text-xs text-slate-400 mb-2">
                                    Status Pendataan
                                </p>

                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-semibold"
                                    :class="
                                        selectedEmployee?.sudah_didata === 'Sudah Didata'
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-rose-50 text-rose-700'
                                    "
                                >

                                    <span
                                        class="w-2 h-2 rounded-full"
                                        :class="
                                            selectedEmployee?.sudah_didata === 'Sudah Didata'
                                                ? 'bg-emerald-500'
                                                : 'bg-rose-500'
                                        "
                                    ></span>

                                    <span
                                        x-text="selectedEmployee?.sudah_didata || '-'"
                                    ></span>

                                </span>

                            </div>


                            <!-- STATUS ASLI -->

                            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50">

                                <p class="text-xs text-slate-400 mb-1">
                                    Status
                                </p>

                                <p
                                    class="font-semibold text-slate-800 wrap-break-word"
                                    x-text="selectedEmployee?.status || '-'"
                                ></p>

                            </div>


                            <!-- ASSIGNMENT -->

                            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50">

                                <p class="text-xs text-slate-400 mb-1">
                                    Assignment ID
                                </p>

                                <p
                                    class="font-semibold text-slate-800 break-all"
                                    x-text="selectedEmployee?.assignment_id || '-'"
                                ></p>

                            </div>


                            <!-- CODE IDENTITY -->

                            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50">

                                <p class="text-xs text-slate-400 mb-1">
                                    Code Identity
                                </p>

                                <p
                                    class="font-semibold text-slate-800 break-all"
                                    x-text="selectedEmployee?.code_identity || '-'"
                                ></p>

                            </div>


                            <!-- ASSIGNMENT STATUS -->

                            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50">

                                <p class="text-xs text-slate-400 mb-1">
                                    Assignment Status
                                </p>

                                <p
                                    class="font-semibold text-slate-800 wrap-break-word"
                                    x-text="selectedEmployee?.assignment_status_alias || '-'"
                                ></p>

                            </div>


                            <!-- INSTANSI -->

                            <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50">

                                <p class="text-xs text-slate-400 mb-1">
                                    Instansi
                                </p>

                                <p
                                    class="font-semibold text-slate-800 wrap-break-word
                                    x-text="selectedEmployee?.institution?.name || selectedEmployee?.instansi || '-'"
                                ></p>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         DATA PROFESI
                    ================================================== -->

                    <div>

                        <div class="flex items-center gap-3 mb-4">

                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4-8-4V7m8 4v10"
                                    />
                                </svg>

                            </div>

                            <div>

                                <h3 class="font-bold text-slate-900">
                                    Data Pekerjaan
                                </h3>

                                <p class="text-xs text-slate-400">
                                    Informasi pekerjaan individu
                                </p>

                            </div>

                        </div>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <!-- PROFESI -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Profesi
                                </p>

                                <p
                                    class="font-semibold text-slate-800 wrap-break-word"
                                    x-text="selectedEmployee?.profesi || '-'"
                                ></p>

                            </div>


                            <!-- STATUS KERJA -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Status Kerja
                                </p>

                                <p
                                    class="font-semibold text-slate-800 wrap-break-word"
                                    x-text="selectedEmployee?.status_kerja_label || '-'"
                                ></p>

                            </div>


                            <!-- PROFESI LAINNYA -->

                            <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Profesi Lainnya
                                </p>

                                <p
                                    class="font-semibold text-slate-800 wrap-break-word"
                                    x-text="selectedEmployee?.profesi_lainnya || '-'"
                                ></p>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         SE2026
                    ================================================== -->

                    <div>

                        <div class="flex items-center gap-3 mb-4">

                            <div class="w-9 h-9 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14H5V6a2 2 0 012-2z"
                                    />
                                </svg>

                            </div>

                            <div>

                                <h3 class="font-bold text-slate-900">
                                    Informasi SE2026
                                </h3>

                                <p class="text-xs text-slate-400">
                                    Data referensi SE2026
                                </p>

                            </div>

                        </div>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <!-- ASSIGNMENT SE2026 -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Assignment ID SE2026
                                </p>

                                <p
                                    class="font-semibold text-slate-800 break-all"
                                    x-text="selectedEmployee?.respSE26_assignment_id || '-'"
                                ></p>

                            </div>


                            <!-- NO KK SE2026 -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    No. KK SE2026
                                </p>

                                <p
                                    class="font-semibold text-slate-800 break-all"
                                    x-text="selectedEmployee?.respSE26_no_kk || '-'"
                                ></p>

                            </div>


                            <!-- NAMA SE2026 -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Nama SE2026
                                </p>

                                <p
                                    class="font-semibold text-slate-800 wrap-break-word"
                                    x-text="selectedEmployee?.respSE26_nama || '-'"
                                ></p>

                            </div>


                            <!-- CODE IDENTITY SE2026 -->

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Code Identity SE2026
                                </p>

                                <p
                                    class="font-semibold text-slate-800 break-all"
                                    x-text="selectedEmployee?.respSE26_code_identity || '-'"
                                ></p>

                            </div>


                            <!-- KEBERADAAN KELUARGA -->

                            <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                <p class="text-xs text-slate-400 mb-1">
                                    Keberadaan Keluarga
                                </p>

                                <p
                                    class="font-semibold text-slate-800 wrap-break-word"
                                    x-text="selectedEmployee?.respSE26_keberadaan_klrg || '-'"
                                ></p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- MODAL FOOTER -->

            <div class="px-4 sm:px-7 py-4 border-t border-slate-100 bg-white flex justify-end">

                <button
                    type="button"
                    @click="closeDetail()"
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-sm hover:bg-slate-800 active:scale-[.98] transition-all duration-200"
                >
                    Tutup
                </button>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     ALPINE DATA
========================================================== -->

<script>
    function monitoringData() {

        return {

            employees: @json($employees),

            search: '',
            institution: '',
            status: '',
            kecamatan: '',
            kelurahan: '',

            showFilters: false,

            detailModal: false,
            selectedEmployee: null,


            /* ==========================================
               FILTER DATA
            ========================================== */

            get filteredEmployees() {

                const search = this.search
                    .trim()
                    .toLowerCase();

                return this.employees.filter(employee => {

                    const searchableText = [
                        employee.nama,
                        employee.nama_kepala_keluarga,
                        employee.assignment_id,
                        employee.idsubsls,
                        employee.rt_rw,
                        employee.kecamatan,
                        employee.kelurahan,
                        employee.instansi,
                        employee.status,
                        employee.sudah_didata,
                        employee.institution?.name
                    ]
                    .filter(
                        value =>
                            value !== null &&
                            value !== undefined
                    )
                    .join(' ')
                    .toLowerCase();


                    const matchesSearch =
                        !search ||
                        searchableText.includes(search);


                    const matchesInstitution =
                        !this.institution ||
                        String(employee.institution_id ?? '') ===
                        String(this.institution);


                    const matchesStatus =
                        !this.status ||
                        employee.sudah_didata === this.status;


                    const matchesKecamatan =
                        !this.kecamatan ||
                        employee.kecamatan === this.kecamatan;


                    const matchesKelurahan =
                        !this.kelurahan ||
                        employee.kelurahan === this.kelurahan;


                    return (
                        matchesSearch &&
                        matchesInstitution &&
                        matchesStatus &&
                        matchesKecamatan &&
                        matchesKelurahan
                    );

                });

            },


            /* ==========================================
               STATISTICS
            ========================================== */

            get filteredSudahDidata() {

                return this.filteredEmployees.filter(
                    employee =>
                        employee.sudah_didata === 'Sudah Didata'
                ).length;

            },


            get filteredBelumDidata() {

                return this.filteredEmployees.filter(
                    employee =>
                        employee.sudah_didata === 'Belum Didata'
                ).length;

            },


            get filteredPercentage() {

                const total =
                    this.filteredEmployees.length;

                if (total === 0) {
                    return 0;
                }

                const sudah =
                    this.filteredEmployees.filter(
                        employee =>
                            employee.sudah_didata === 'Sudah Didata'
                    ).length;

                return Math.round(
                    (sudah / total) * 100
                );

            },


            /* ==========================================
               ACTIVE FILTER
            ========================================== */

            get activeFilterCount() {

                let count = 0;

                if (this.search.trim()) {
                    count++;
                }

                if (this.institution) {
                    count++;
                }

                if (this.status) {
                    count++;
                }

                if (this.kecamatan) {
                    count++;
                }

                if (this.kelurahan) {
                    count++;
                }

                return count;

            },


            /* ==========================================
               RESET FILTER
            ========================================== */

            resetFilters() {

                this.search = '';
                this.institution = '';
                this.status = '';
                this.kecamatan = '';
                this.kelurahan = '';

            },


            /* ==========================================
               OPEN DETAIL
            ========================================== */

            openDetail(employee) {

                this.selectedEmployee = employee;

                this.detailModal = true;

                document.body.classList.add('overflow-hidden');

            },


            /* ==========================================
               CLOSE DETAIL
            ========================================== */

            closeDetail() {

                this.detailModal = false;

                document.body.classList.remove('overflow-hidden');

                setTimeout(() => {

                    if (!this.detailModal) {

                        this.selectedEmployee = null;

                    }

                }, 200);

            }

        };

    }
</script>

</body>
</html>
