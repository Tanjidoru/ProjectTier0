@extends('layouts.app')

@section('title', 'Peta Geospasial Proyek - PANTAU.ID')

@push('styles')
<style>
    /* Full viewport map container calculation */
    #mapViewport {
        height: calc(100vh - 105px);
        min-height: 560px;
    }
    .gis-sidebar {
        width: 410px;
        height: calc(100vh - 105px);
        min-height: 560px;
    }
    @media (max-width: 1024px) {
        .gis-sidebar {
            width: 100%;
            height: 45vh;
            position: relative;
            z-index: 20;
        }
        #mapViewport {
            height: 55vh;
        }
    }
    .project-item-card {
        transition: background-color 0.15s ease;
        border-left: 3px solid transparent;
    }
    .project-item-card:hover {
        background-color: #F1F5F9;
    }
    .project-item-card.is-active {
        background-color: #EFF6FF;
        border-left-color: #1D4ED8;
    }

    /* Custom Leaflet Pin Styling */
    .pin-marker {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);
        border: 2px solid #FFFFFF;
        cursor: pointer;
        transition: transform 0.15s ease;
    }
    .pin-marker:hover {
        transform: rotate(-45deg) scale(1.15);
        z-index: 9999 !important;
    }
    .pin-marker-inner {
        transform: rotate(45deg);
        color: #FFFFFF;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
@php
    $proyekList = config('proyek');
    $pemerintahCount = collect($proyekList)->where('sumber_dana', 'pemerintah')->count();
    $swastaCount = collect($proyekList)->where('sumber_dana', 'swasta')->count();
@endphp

<!-- GIS Container -->
<div class="flex flex-col lg:flex-row relative bg-slate-100 overflow-hidden">
    <!-- ==================== LEFT SIDEBAR PANEL ==================== -->
    <div class="gis-sidebar bg-white border-r border-slate-200 flex flex-col z-20 shadow-md">
        <!-- Sidebar Header with Brand Tone -->
        <div class="p-3.5 bg-slate-900 text-white border-b border-slate-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded bg-amber-500 text-slate-950 flex items-center justify-center font-bold text-xs">
                        <i class="fas fa-map-location-dot"></i>
                    </span>
                    <div>
                        <h2 class="font-bold text-xs tracking-tight text-white">Peta Proyek Nasional</h2>
                        <p class="text-[10px] text-slate-400">Monitoring Geospasial</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 bg-slate-800 rounded text-xs font-mono font-bold text-amber-300 border border-slate-700">
                    <span id="activeMarkerCount">{{ count($proyekList) }}</span> Proyek
                </span>
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="p-3 bg-slate-50 border-b border-slate-200 space-y-2.5">
            <!-- Keyword Search -->
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="gisSearchInput" placeholder="Cari nama proyek, lokasi, kontraktor..."
                    class="search-field text-xs pl-8 pr-7 py-2 w-full">
                <button id="clearSearchBtn" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                    <i class="fas fa-times-circle text-xs"></i>
                </button>
            </div>

            <!-- Quick Filter Tabs (Sumber Dana) -->
            <div class="flex gap-1 p-1 bg-slate-200 rounded-md text-xs">
                <button class="flex-1 py-1 px-2 rounded font-semibold transition text-center bg-slate-900 text-white"
                    data-filter="semua" onclick="setMapSourceFilter('semua', this)">
                    Semua
                </button>
                <button class="flex-1 py-1 px-2 rounded font-semibold transition text-center text-slate-700 hover:text-slate-900"
                    data-filter="pemerintah" onclick="setMapSourceFilter('pemerintah', this)">
                    Pemerintah
                </button>
                <button class="flex-1 py-1 px-2 rounded font-semibold transition text-center text-slate-700 hover:text-slate-900"
                    data-filter="swasta" onclick="setMapSourceFilter('swasta', this)">
                    Swasta
                </button>
            </div>

            <!-- Status Filter Chips -->
            <div class="flex gap-1 flex-wrap">
                <button class="filter-chip text-[11px] py-0.5 px-2 active" data-status="" onclick="setMapStatusFilter('', this)">
                    Semua Status
                </button>
                <button class="filter-chip text-[11px] py-0.5 px-2" data-status="berjalan" onclick="setMapStatusFilter('berjalan', this)">
                    <span class="state-dot bg-amber-500"></span> Berjalan
                </button>
                <button class="filter-chip text-[11px] py-0.5 px-2" data-status="selesai" onclick="setMapStatusFilter('selesai', this)">
                    <span class="state-dot bg-emerald-500"></span> Selesai
                </button>
                <button class="filter-chip text-[11px] py-0.5 px-2" data-status="tertunda" onclick="setMapStatusFilter('tertunda', this)">
                    <span class="state-dot bg-red-500"></span> Tertunda
                </button>
            </div>
        </div>

        <!-- Scrollable Project List -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-100" id="gisProjectList">
            @foreach($proyekList as $proyek)
            <div class="project-item-card p-3 cursor-pointer"
                 id="card-proj-{{ $proyek['id'] }}"
                 data-id="{{ $proyek['id'] }}"
                 data-sumber="{{ $proyek['sumber_dana'] }}"
                 data-status="{{ $proyek['status'] }}"
                 data-sektor="{{ $proyek['sektor'] }}"
                 data-query="{{ strtolower($proyek['nama'] . ' ' . $proyek['kontraktor'] . ' ' . $proyek['kabupaten'] . ' ' . $proyek['provinsi']) }}"
                 onclick="focusProjectOnMap({{ $proyek['id'] }}, {{ $proyek['lat'] }}, {{ $proyek['lng'] }})">

                <div class="flex items-start gap-3">
                    @php
                        $colorBg = match($proyek['status']) {
                            'selesai' => 'bg-emerald-600',
                            'tertunda' => 'bg-red-600',
                            'perencanaan' => 'bg-purple-600',
                            default => 'bg-amber-600',
                        };
                    @endphp
                    <div class="w-9 h-9 rounded {{ $colorBg }} text-white font-bold text-xs flex flex-col items-center justify-center flex-shrink-0">
                        <span>{{ $proyek['progres'] }}%</span>
                        <span class="text-[7px] uppercase font-medium">Fisik</span>
                    </div>

                    <!-- Meta Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="badge text-[10px] py-0 px-1.5 {{ $proyek['sumber_dana'] === 'pemerintah' ? 'badge-pemerintah' : 'badge-swasta' }}">
                                {{ ucfirst($proyek['sumber_dana']) }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $proyek['kode'] }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 leading-snug line-clamp-2 hover:text-blue-700 transition">
                            {{ $proyek['nama'] }}
                        </h4>
                        <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500">
                            <span class="truncate">
                                <i class="fas fa-location-dot text-red-500 mr-1"></i>
                                {{ $proyek['kabupaten'] }}, {{ $proyek['provinsi'] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between mt-1.5 pt-1.5 border-t border-slate-100 text-[11px]">
                            <span class="text-slate-600 font-bold">
                                Rp {{ number_format($proyek['anggaran'] / 1e9, 0, ',', '.') }} M
                            </span>
                            <span class="text-blue-700 hover:text-blue-900 font-semibold">
                                Fokus Peta
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Empty State for Sidebar Search -->
            <div id="gisEmptyList" class="hidden p-6 text-center text-slate-500">
                <i class="fas fa-search-location text-2xl text-slate-300 mb-2"></i>
                <p class="text-xs font-bold text-slate-700">Tidak ada proyek ditemukan</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Sesuaikan filter atau reset kata kunci pencarian.</p>
            </div>
        </div>

        <!-- Legend Footer -->
        <div class="p-2.5 bg-slate-900 text-slate-300 border-t border-slate-800 text-xs">
            <div class="flex items-center justify-between text-[11px]">
                <span class="flex items-center gap-1.5">
                    <span class="state-dot bg-amber-500"></span> Berjalan
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="state-dot bg-emerald-500"></span> Selesai
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="state-dot bg-red-500"></span> Tertunda
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="state-dot bg-purple-500"></span> Rencana
                </span>
            </div>
        </div>
    </div>

    <!-- ==================== MAIN MAP VIEWPORT ==================== -->
    <div class="flex-1 relative">
        <div id="mapViewport" class="w-full"></div>

        <!-- Floating Controls (Top Right) -->
        <div class="absolute top-4 right-4 z-[400] flex flex-col gap-2">
            <!-- Map Layer Switcher -->
            <div class="bg-white rounded-lg shadow-md border border-slate-200 p-1 flex gap-1 text-xs font-bold text-slate-700">
                <button id="layerStreet" class="px-2.5 py-1 rounded bg-slate-900 text-white transition" onclick="switchBaseLayer('street')">
                    Standar
                </button>
                <button id="layerSatellite" class="px-2.5 py-1 rounded hover:bg-slate-100 transition" onclick="switchBaseLayer('satellite')">
                    Satelit
                </button>
                <button id="layerTopo" class="px-2.5 py-1 rounded hover:bg-slate-100 transition" onclick="switchBaseLayer('topo')">
                    Topo
                </button>
            </div>

            <!-- Reset View Button -->
            <button onclick="resetMapView()" class="bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs px-3 py-1.5 rounded-lg shadow border border-slate-200 flex items-center gap-1.5 transition self-end">
                <i class="fas fa-crosshairs text-blue-600"></i>
                <span>Reset Wilayah RI</span>
            </button>
        </div>

        <!-- Map Info Watermark -->
        <div class="absolute bottom-3 left-3 z-[400] bg-slate-900 text-white text-[10px] px-2.5 py-1 rounded border border-slate-700 shadow pointer-events-none hidden sm:flex items-center gap-1.5">
            <span>Standar Geospasial WGS84 · Simpul DJPI Compatible</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawProjects = @json($proyekList);
    let map;
    let markersMap = {};
    let activeSourceFilter = 'semua';
    let activeStatusFilter = '';
    let baseLayers = {};
    let currentBaseLayer;

    const statusMap = {
        'berjalan': { label: 'Sedang Berjalan', color: '#F59E0B' },
        'selesai': { label: 'Selesai', color: '#10B981' },
        'tertunda': { label: 'Tertunda', color: '#EF4444' },
        'perencanaan': { label: 'Perencanaan', color: '#8B5CF6' },
    };

    map = L.map('mapViewport', {
        zoomControl: true,
        attributionControl: false
    }).setView([-2.2, 118], 5);

    baseLayers.street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 });
    baseLayers.satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 18 });
    baseLayers.topo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', { maxZoom: 17 });

    currentBaseLayer = baseLayers.street.addTo(map);

    function createPinIcon(project) {
        const conf = statusMap[project.status] || { color: '#1D4ED8' };
        const iconClass = project.sumber_dana === 'pemerintah' ? 'fa-landmark' : 'fa-building';

        return L.divIcon({
            className: 'custom-pin-container',
            html: `
                <div class="pin-marker" style="background-color: ${conf.color};">
                    <span class="pin-marker-inner"><i class="fas ${iconClass}"></i></span>
                </div>
            `,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -36]
        });
    }

    function createPopupHTML(p) {
        const conf = statusMap[p.status] || { label: p.status, color: '#1D4ED8' };
        const anggaranFormat = (p.anggaran >= 1e12)
            ? 'Rp ' + (p.anggaran / 1e12).toFixed(1) + ' Triliun'
            : 'Rp ' + (p.anggaran / 1e9).toFixed(0) + ' Miliar';

        const realisasiFormat = (p.realisasi >= 1e12)
            ? 'Rp ' + (p.realisasi / 1e12).toFixed(1) + ' Triliun'
            : 'Rp ' + (p.realisasi / 1e9).toFixed(0) + ' Miliar';

        return `
            <div style="font-family: inherit; width: 280px;">
                <div style="background: #0B192C; color: white; padding: 10px 12px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 3px;">
                        <span style="font-family: monospace; font-size: 10px; color: #F59E0B; font-weight: bold;">${p.kode}</span>
                        <span style="background: ${p.sumber_dana === 'pemerintah' ? '#EFF6FF' : '#ECFDF5'}; color: ${p.sumber_dana === 'pemerintah' ? '#1D4ED8' : '#047857'}; font-size: 10px; font-weight: 700; padding: 1px 7px; border-radius: 99px;">
                            ${p.sumber_dana.toUpperCase()}
                        </span>
                    </div>
                    <h4 style="font-size: 12px; font-weight: 700; line-height: 1.3; margin: 0; color: #FFFFFF;">${p.nama}</h4>
                </div>

                <div style="padding: 10px 12px; background: #FFFFFF; font-size: 11px;">
                    <div style="color: #64748B; margin-bottom: 6px;">
                        <i class="fas fa-location-dot" style="color: #EF4444; margin-right: 4px;"></i>
                        <strong>${p.kabupaten}</strong>, ${p.provinsi}
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px; background: #F8FAFC; border: 1px solid #E2E8F0; padding: 6px 8px; border-radius: 6px; margin-bottom: 8px;">
                        <div>
                            <span style="font-size: 9px; color: #94A3B8; text-transform: uppercase; font-weight: bold; display: block;">Anggaran</span>
                            <div style="font-weight: 700; color: #0F172A;">${anggaranFormat}</div>
                        </div>
                        <div>
                            <span style="font-size: 9px; color: #94A3B8; text-transform: uppercase; font-weight: bold; display: block;">Realisasi</span>
                            <div style="font-weight: 700; color: #1D4ED8;">${realisasiFormat}</div>
                        </div>
                    </div>

                    <div style="margin-bottom: 8px;">
                        <div style="display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 2px;">
                            <span style="color: #475569; font-weight: 600;">Progres Fisik</span>
                            <span style="font-weight: 700; color: ${conf.color};">${p.progres}%</span>
                        </div>
                        <div style="width: 100%; background: #E2E8F0; border-radius: 99px; height: 5px; overflow: hidden;">
                            <div style="width: ${p.progres}%; background: ${conf.color}; height: 100%;"></div>
                        </div>
                    </div>

                    <a href="/proyek/${p.id}" style="display: block; text-align: center; background: #0B192C; color: white; padding: 5px 8px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 11px;">
                        Lihat Profil Proyek
                    </a>
                </div>
            </div>
        `;
    }

    rawProjects.forEach(p => {
        const marker = L.marker([p.lat, p.lng], { icon: createPinIcon(p) })
            .bindPopup(createPopupHTML(p), { maxWidth: 290 })
            .addTo(map);

        marker.on('click', function() {
            highlightSidebarCard(p.id);
        });

        markersMap[p.id] = { marker, data: p };
    });

    function highlightSidebarCard(id) {
        document.querySelectorAll('.project-item-card').forEach(el => el.classList.remove('is-active'));
        const target = document.getElementById('card-proj-' + id);
        if (target) {
            target.classList.add('is-active');
            target.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    window.focusProjectOnMap = function(id, lat, lng) {
        map.flyTo([lat, lng], 13, { duration: 1.2 });
        if (markersMap[id]) {
            markersMap[id].marker.openPopup();
        }
        highlightSidebarCard(id);
    };

    window.switchBaseLayer = function(type) {
        map.removeLayer(currentBaseLayer);
        currentBaseLayer = baseLayers[type].addTo(map);

        document.getElementById('layerStreet').className = 'px-2.5 py-1 rounded hover:bg-slate-100 transition';
        document.getElementById('layerSatellite').className = 'px-2.5 py-1 rounded hover:bg-slate-100 transition';
        document.getElementById('layerTopo').className = 'px-2.5 py-1 rounded hover:bg-slate-100 transition';

        const activeId = type === 'street' ? 'layerStreet' : (type === 'satellite' ? 'layerSatellite' : 'layerTopo');
        document.getElementById(activeId).className = 'px-2.5 py-1 rounded bg-slate-900 text-white transition';
    };

    window.resetMapView = function() {
        map.flyTo([-2.2, 118], 5, { duration: 1 });
    };

    const searchInput = document.getElementById('gisSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');

    function applyMapFilters() {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        clearBtn.classList.toggle('hidden', query === '');

        Object.values(markersMap).forEach(({ marker, data }) => {
            let matches = true;

            if (activeSourceFilter !== 'semua' && data.sumber_dana !== activeSourceFilter) matches = false;
            if (activeStatusFilter && data.status !== activeStatusFilter) matches = false;

            if (query) {
                const searchHaystack = (data.nama + ' ' + data.kontraktor + ' ' + data.kabupaten + ' ' + data.provinsi + ' ' + data.sektor).toLowerCase();
                if (!searchHaystack.includes(query)) matches = false;
            }

            if (matches) {
                marker.addTo(map);
                visibleCount++;
            } else {
                map.removeLayer(marker);
            }
        });

        document.querySelectorAll('.project-item-card').forEach(card => {
            const sumber = card.dataset.sumber;
            const status = card.dataset.status;
            const queryData = card.dataset.query;

            let cardMatches = true;
            if (activeSourceFilter !== 'semua' && sumber !== activeSourceFilter) cardMatches = false;
            if (activeStatusFilter && status !== activeStatusFilter) cardMatches = false;
            if (query && !queryData.includes(query)) cardMatches = false;

            card.style.display = cardMatches ? '' : 'none';
        });

        document.getElementById('activeMarkerCount').textContent = visibleCount;
        const emptyListEl = document.getElementById('gisEmptyList');
        if (emptyListEl) {
            emptyListEl.classList.toggle('hidden', visibleCount > 0);
        }
    }

    let mapSearchDebounce;
    searchInput.addEventListener('input', function() {
        clearTimeout(mapSearchDebounce);
        mapSearchDebounce = setTimeout(applyMapFilters, 100);
    });
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        applyMapFilters();
    });

    window.setMapSourceFilter = function(filter, btn) {
        activeSourceFilter = filter;
        btn.parentElement.querySelectorAll('button').forEach(b => {
            b.className = 'flex-1 py-1 px-2 rounded font-semibold transition text-center text-slate-700 hover:text-slate-900';
        });
        btn.className = 'flex-1 py-1 px-2 rounded font-semibold transition text-center bg-slate-900 text-white';
        applyMapFilters();
    };

    window.setMapStatusFilter = function(status, btn) {
        activeStatusFilter = status;
        document.querySelectorAll('[data-status]').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        applyMapFilters();
    };
});
</script>
@endpush
