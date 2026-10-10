
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-7">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm slide-up" style="animation-delay:.08s">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-slate-500">Total Data</p>
                <p class="text-3xl font-bold text-slate-900 mt-2" x-text="filteredEmployees.length"></p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5m10 0v-4H7v4m10 0H7"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm slide-up" style="animation-delay:.14s">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-slate-500">Sudah Didata</p>
                <p class="text-3xl font-bold text-emerald-600 mt-2" x-text="filteredSudahDidata"></p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm slide-up" style="animation-delay:.20s">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-slate-500">Belum Didata</p>
                <p class="text-3xl font-bold text-rose-600 mt-2" x-text="filteredBelumDidata"></p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.5L2.8 17a2 2 0 001.75 3h14.9a2 2 0 001.75-3L13.7 3.5a2 2 0 00-3.4 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm slide-up" style="animation-delay:.26s">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-slate-500">Persentase</p>
                <p class="text-3xl font-bold text-blue-600 mt-2" x-text="filteredPercentage + '%'"></p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 14h16M8 16V9m4 7V6m4 10v-4"/>
                </svg>
            </div>
        </div>
        <div class="mt-4 h-2 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-500" :style="`width: ${filteredPercentage}%`"></div>
        </div>
    </div>
</section>
