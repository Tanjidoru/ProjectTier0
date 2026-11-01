@extends('layouts.app')

@section('title', 'Beranda - PANTAU.ID Sistem Pemantauan Proyek Nasional')

@section('content')
@php
    $proyekList = config('proyek');
    $totalProyek = count($proyekList);
    $totalAnggaran = collect($proyekList)->sum('anggaran');
    $totalRealisasi = collect($proyekList)->sum('realisasi');
    $pemerintahCount = collect($proyekList)->where('sumber_dana', 'pemerintah')->count();
    $swastaCount = collect($proyekList)->where('sumber_dana', 'swasta')->count();
    $berjalanCount = collect($proyekList)->where('status', 'berjalan')->count();
    $selesaiCount = collect($proyekList)->where('status', 'selesai')->count();
    $avgProgres = collect($proyekList)->avg('progres');
@endphp

<!-- ==================== HERO SECTION ==================== -->
<section class="hero-banner-gradient text-white relative py-14 lg:py-16 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            <!-- Left Narrative (Span 7) -->
            <div class="lg:col-span-7 space-y-5">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                    Sistem Pemantauan Proyek Infrastruktur Nasional
                </h1>

                <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl">
                    Portal integrasi data geospasial dan monitoring progres fisik serta realisasi pendanaan untuk proyek infrastruktur strategis dari sektor <strong>Pemerintah</strong> (APBN/KPBU) dan <strong>Swasta</strong>.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <a href="{{ url('/peta') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm rounded-lg transition shadow-sm">
                        <i class="fas fa-map-location-dot"></i>
                        <span>Eksplor Peta Proyek</span>
                    </a>
                    <a href="{{ url('/proyek') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-sm rounded-lg border border-slate-700 transition">
                        <i class="fas fa-layer-group text-slate-300"></i>
                        <span>Daftar Proyek Prioritas</span>
                    </a>
                </div>

                <!-- Verified indicators without glowing loops -->

            </div>

            <!-- Right Interactive Mini Map Widget (Span 5) -->
            <div class="lg:col-span-5">
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-3.5 shadow-lg">
                    <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-800">
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-300">

                            <span>Pratinjau Sebaran Geospasial</span>
                        </div>
                        <a href="{{ url('/peta') }}" class="text-xs font-bold text-amber-400 hover:text-amber-300">
                            Buka Layar Penuh
                        </a>
                    </div>

                    <!-- Mini Leaflet Map Container with fixed aspect ratio to prevent layout shifts -->
                    <div id="heroPreviewMap" class="w-full h-64 sm:h-72 rounded-lg overflow-hidden border border-slate-800"></div>

                    <div class="mt-2.5 flex items-center justify-between text-xs text-slate-400 px-0.5">
                        <span class="flex items-center gap-1.5">
                            <span class="state-dot bg-blue-500"></span> Pemerintah: <strong class="text-slate-200">{{ $pemerintahCount }}</strong>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="state-dot bg-emerald-500"></span> Swasta: <strong class="text-slate-200">{{ $swastaCount }}</strong>
                        </span>
                        <span class="text-slate-300 font-semibold">
                            Rerata Fisik: <strong class="text-amber-400">{{ round($avgProgres) }}%</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== KEY KPI STATS SECTION ==================== -->
<section class="max-w-7xl mx-auto px-4 -mt-7 relative z-20">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Stat Card 1 -->
        <div class="stat-card">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Total Proyek Dipantau</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ $totalProyek }}</span>
                <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded">Nasional</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ $berjalanCount }} Berjalan · {{ $selesaiCount }} Selesai</p>
        </div>

        <!-- Stat Card 2 -->
        <div class="stat-card">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Total Alokasi Investasi</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-2xl font-black text-slate-900">Rp {{ number_format($totalAnggaran / 1e12, 1) }}</span>
                <span class="text-xs font-bold text-slate-600">Triliun</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Realisasi: Rp {{ number_format($totalRealisasi / 1e12, 1) }} T</p>
        </div>

        <!-- Stat Card 3 -->
        <div class="stat-card">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Portofolio Pemerintah</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ $pemerintahCount }}</span>
                <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">APBN / KPBU</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ round($pemerintahCount / $totalProyek * 100) }}% dari total proyek</p>
        </div>

        <!-- Stat Card 4 -->
        <div class="stat-card">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Portofolio Swasta</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-black text-slate-900">{{ $swastaCount }}</span>
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">B2B / Murni</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ round($swastaCount / $totalProyek * 100) }}% dari total proyek</p>
        </div>
    </div>
</section>

<!-- ==================== FEATURED HIGHLIGHTED PROJECTS ==================== -->
<section class="max-w-7xl mx-auto px-4 mt-14">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Sorotan Proyek Prioritas
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Proyek dengan bobot investasi dan dampak pembangunan infrastruktur tertinggi
            </p>
        </div>
        <a href="{{ url('/proyek') }}" class="text-xs font-bold text-slate-900 hover:text-blue-700 flex items-center gap-1.5 self-start sm:self-auto">
            <span>Lihat Semua Katalog</span>
            <i class="fas fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <!-- Project Cards Grid with Real Photos & Sized Media (CLS prevention) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(collect($proyekList)->where('status', 'berjalan')->sortByDesc('anggaran')->take(6) as $proyek)
        <div class="project-card group">
            <!-- Card Header Area with Real Photo -->
            <div class="relative h-44 overflow-hidden bg-slate-900">
                @if($loop->first)
                <img src="{{ $proyek['foto'] }}"
                     alt="{{ $proyek['nama'] }}"
                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                     width="400"
                     height="176"
                     fetchpriority="high">
                @else
                <img src="{{ $proyek['foto'] }}"
                     alt="{{ $proyek['nama'] }}"
                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                     width="400"
                     height="176"
                     loading="lazy">
                @endif

                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>

                <!-- Top Badges -->
                <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2 z-10">
                    <span class="badge {{ $proyek['sumber_dana'] === 'pemerintah' ? 'badge-pemerintah' : 'badge-swasta' }}">
                        <i class="fas {{ $proyek['sumber_dana'] === 'pemerintah' ? 'fa-landmark' : 'fa-building' }} text-xs"></i>
                        <span>{{ ucfirst($proyek['sumber_dana']) }}</span>
                    </span>

                    <span class="badge badge-berjalan">
                        <span class="state-dot bg-amber-500"></span>
                        <span>Berjalan</span>
                    </span>
                </div>

                <!-- Title & Code overlay -->
                <div class="absolute bottom-3 left-3 right-3 z-10">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="font-mono text-[10px] text-amber-300 font-bold bg-black/70 px-2 py-0.5 rounded">
                            {{ $proyek['kode'] }}
                        </span>
                        <span class="text-[11px] text-white font-semibold bg-slate-900/80 px-2 py-0.5 rounded truncate">
                            {{ $proyek['sektor'] }}
                        </span>
                    </div>
                    <h3 class="text-white font-bold text-sm leading-snug line-clamp-1 group-hover:text-amber-300 transition-colors">
                        {{ $proyek['nama'] }}
                    </h3>
                </div>
            </div>

            <!-- Card Body Content -->
            <div class="card-body space-y-3.5">
                <!-- Location & Contractor Meta -->
                <div class="space-y-1 text-xs text-slate-600">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-location-dot text-red-500 w-4 text-center"></i>
                        <span class="font-semibold text-slate-800">{{ $proyek['kabupaten'] }}</span>,
                        <span class="text-slate-500">{{ $proyek['provinsi'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-helmet-safety text-slate-500 w-4 text-center"></i>
                        <span class="text-slate-600 truncate">{{ $proyek['kontraktor'] }}</span>
                    </div>
                </div>

                <!-- Investment Numbers -->
                <div class="bg-slate-50 rounded-lg p-2.5 border border-slate-100 grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Pagu Anggaran</span>
                        <span class="text-xs font-bold text-slate-900 mt-0.5 block">
                            Rp {{ number_format($proyek['anggaran'] / 1e9, 0, ',', '.') }} M
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Realisasi</span>
                        <span class="text-xs font-bold text-blue-700 mt-0.5 block">
                            Rp {{ number_format($proyek['realisasi'] / 1e9, 0, ',', '.') }} M
                        </span>
                    </div>
                </div>

                <!-- Dual Progress Bar (Fisik) -->
                <div>
                    <div class="flex justify-between items-center text-xs mb-1">
                        <span class="font-semibold text-slate-600">Progres Fisik</span>
                        <span class="font-bold text-xs {{ $proyek['progres'] >= 75 ? 'text-emerald-600' : ($proyek['progres'] >= 50 ? 'text-blue-700' : 'text-amber-600') }}">
                            {{ $proyek['progres'] }}%
                        </span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill {{ $proyek['progres'] >= 75 ? 'bg-emerald-500' : ($proyek['progres'] >= 50 ? 'bg-blue-600' : 'bg-amber-500') }}"
                             style="width: {{ $proyek['progres'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="card-footer flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">
                    Target: {{ \Carbon\Carbon::parse($proyek['tanggal_selesai'])->translatedFormat('M Y') }}
                </span>
                <a href="{{ url('/proyek/' . $proyek['id']) }}" class="font-bold text-blue-900 hover:text-blue-700 transition">
                    Lihat Detail Profil
                </a>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- ==================== SECTOR DIRECTORY ==================== -->
<section class="max-w-7xl mx-auto px-4 mt-16 mb-8">
    <div class="bg-slate-900 rounded-xl p-6 sm:p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-extrabold text-white">Sektor Pembangunan Nasional</h3>
                <p class="text-xs text-slate-400 mt-0.5">Klasifikasi sektor infrastruktur prioritas</p>
            </div>
            <a href="{{ url('/proyek') }}" class="text-xs font-bold text-amber-400 hover:text-amber-300">
                Filter Seluruh Sektor di Katalog
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @php
                $sektors = [
                    ['icon' => 'fa-road', 'nama' => 'Jalan & Jembatan', 'count' => collect($proyekList)->where('sektor', 'Jalan & Jembatan')->count()],
                    ['icon' => 'fa-water', 'nama' => 'Sumber Daya Air', 'count' => collect($proyekList)->where('sektor', 'Sumber Daya Air')->count()],
                    ['icon' => 'fa-train-subway', 'nama' => 'Transportasi', 'count' => collect($proyekList)->where('sektor', 'Transportasi')->count()],
                    ['icon' => 'fa-solar-panel', 'nama' => 'Energi', 'count' => collect($proyekList)->where('sektor', 'Energi')->count()],
                    ['icon' => 'fa-industry', 'nama' => 'Kawasan Industri', 'count' => collect($proyekList)->where('sektor', 'Kawasan Industri')->count()],
                    ['icon' => 'fa-hospital', 'nama' => 'Fasilitas Publik', 'count' => 3],
                ];
            @endphp
            @foreach($sektors as $s)
            <a href="{{ url('/proyek?sektor=' . urlencode($s['nama'])) }}" class="bg-slate-800 hover:bg-slate-700/80 rounded-lg p-3.5 text-center border border-slate-700 transition block">
                <div class="w-10 h-10 rounded-lg bg-slate-900 text-amber-400 flex items-center justify-center mx-auto mb-2 text-base">
                    <i class="fas {{ $s['icon'] }}"></i>
                </div>
                <h4 class="text-xs font-bold text-white line-clamp-1">{{ $s['nama'] }}</h4>
                <p class="text-[11px] text-slate-400 mt-0.5 font-mono">{{ $s['count'] }} Proyek</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hero Mini Map
    const heroMap = L.map('heroPreviewMap', {
        zoomControl: false,
        attributionControl: false,
        scrollWheelZoom: false,
    }).setView([-2.5, 118], 4.5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(heroMap);

    const proyekData = @json($proyekList);

    const statusColors = {
        'berjalan': '#F59E0B',
        'selesai': '#10B981',
        'tertunda': '#EF4444',
        'perencanaan': '#8B5CF6'
    };

    proyekData.forEach(p => {
        const color = statusColors[p.status] || '#1D4ED8';
        const marker = L.circleMarker([p.lat, p.lng], {
            radius: p.sumber_dana === 'pemerintah' ? 7 : 6,
            fillColor: color,
            color: '#FFFFFF',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(heroMap);

        marker.bindPopup(`
            <div style="font-size: 11px; padding: 4px; font-family: inherit;">
                <strong style="color: #0B192C; display: block; margin-bottom: 2px;">${p.nama}</strong>
                <span style="color: #64748B;">${p.kabupaten}, ${p.provinsi}</span><br>
                <span style="color: ${color}; font-weight: bold;">Progres Fisik: ${p.progres}%</span>
            </div>
        `);
    });
});
</script>
@endpush
