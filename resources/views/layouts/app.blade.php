<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Informasi Pemantauan Proyek Nasional (PANTAU.ID) - Platform integrasi data geospasial dan monitoring progres fisik serta realisasi pendanaan proyek infrastruktur Pemerintah dan Swasta.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PANTAU.ID - Sistem Pemantauan Proyek Nasional')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23F59E0B'><path d='M12 2L2 7l10 5 10-5-10-5zm0 7.5L4.4 7 12 3.2 19.6 7 12 9.5zm-8 4.7l8 4 8-4v2.5l-8 4-8-4v-2.5z'/></svg>">

    <!-- Speculation Rules API for instant same-origin navigation (Core Web Vitals) -->
    <script type="speculationrules">
    {
      "prerender": [{
        "where": { "href_matches": "/*" },
        "eagerness": "moderate"
      }]
    }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome (Pinned CDN with fallback) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" />

    <!-- Leaflet CSS (Pinned with SRI) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        pupr: {
                            navy: '#0B192C',
                            blue: '#1E3E62',
                            royal: '#1D4ED8',
                            gold: '#F59E0B',
                            amber: '#D97706',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Core UI & Layout Styles -->
    <style>
        /* ===== FAST PRELOADER ===== */
        #globalPreloader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: #0B192C;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.35s ease, visibility 0.35s ease;
        }
        #globalPreloader.hidden-loader {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .loader-spinner {
            width: 44px;
            height: 44px;
            border: 3px solid rgba(255, 255, 255, 0.15);
            border-top-color: #F59E0B;
            border-radius: 50%;
            animation: spinFast 0.8s linear infinite;
        }
        @keyframes spinFast {
            to { transform: rotate(360deg); }
        }

        /* ===== NAVIGATION STYLES ===== */
        .nav-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 0.95rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            border-radius: 0.5rem;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .nav-item:hover {
            color: #0B192C;
            background-color: #F1F5F9;
        }
        .nav-item.active {
            color: #FFFFFF;
            background-color: #0B192C;
        }
        .nav-item.active i {
            color: #F59E0B;
        }

        /* ===== SEMANTIC BADGES ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: 0.01em;
        }
        .badge-pemerintah {
            background-color: #EFF6FF;
            color: #1D4ED8;
            border: 1px solid #BFDBFE;
        }
        .badge-swasta {
            background-color: #ECFDF5;
            color: #047857;
            border: 1px solid #A7F3D0;
        }
        .badge-berjalan {
            background-color: #FFFBEB;
            color: #B45309;
            border: 1px solid #FDE68A;
        }
        .badge-selesai {
            background-color: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }
        .badge-tertunda {
            background-color: #FEF2F2;
            color: #B91C1C;
            border: 1px solid #FECACA;
        }
        .badge-perencanaan {
            background-color: #FAF5FF;
            color: #7E22CE;
            border: 1px solid #E9D5FF;
        }

        /* ===== METRIC CARDS ===== */
        .stat-card {
            background-color: #FFFFFF;
            border-radius: 0.875rem;
            border: 1px solid #E2E8F0;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            border-color: #CBD5E1;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
        }

        /* ===== PROJECT CARDS ===== */
        .project-card {
            background-color: #FFFFFF;
            border-radius: 0.875rem;
            border: 1px solid #E2E8F0;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            transition: border-color 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
        }
        .project-card:hover {
            border-color: #94A3B8;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }
        .project-card .card-body {
            padding: 1.25rem;
            flex: 1;
        }
        .project-card .card-footer {
            padding: 0.85rem 1.25rem;
            background-color: #F8FAFC;
            border-top: 1px solid #F1F5F9;
        }

        /* ===== PROGRESS BARS ===== */
        .progress-bar-container {
            width: 100%;
            background-color: #E2E8F0;
            border-radius: 9999px;
            height: 0.5rem;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.4s ease;
        }

        /* ===== FILTER PILLS ===== */
        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.85rem;
            font-size: 0.8125rem;
            font-weight: 600;
            border-radius: 0.5rem;
            border: 1px solid #CBD5E1;
            color: #475569;
            background-color: #FFFFFF;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .filter-chip:hover {
            background-color: #F8FAFC;
            color: #0F172A;
            border-color: #94A3B8;
        }
        .filter-chip.active {
            background-color: #0B192C;
            color: #FFFFFF;
            border-color: #0B192C;
        }
        .filter-chip.active i {
            color: #F59E0B;
        }

        /* ===== SEARCH INPUTS ===== */
        .search-field {
            width: 100%;
            padding: 0.625rem 1rem 0.625rem 2.5rem;
            font-size: 0.875rem;
            border: 1px solid #CBD5E1;
            border-radius: 0.5rem;
            background-color: #FFFFFF;
            color: #0F172A;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .search-field:focus {
            outline: none;
            border-color: #1D4ED8;
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.15);
        }

        /* ===== MAP POPUPS ===== */
        .leaflet-popup-content-wrapper {
            border-radius: 10px !important;
            padding: 0 !important;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18) !important;
            border: 1px solid #CBD5E1;
        }
        .leaflet-popup-content {
            margin: 0 !important;
            min-width: 270px;
        }
        .leaflet-container {
            font-family: inherit !important;
        }

        /* Hero Banner Background */
        .hero-banner-gradient {
            background: linear-gradient(135deg, #0B192C 0%, #152A47 60%, #1E3E62 100%);
        }

        /* Clean State Dot (No endless pulse) */
        .state-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 99px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">

    <!-- ==================== FAST PRELOADER ==================== -->
    <div id="globalPreloader" role="status" aria-live="polite">
        <div class="flex flex-col items-center gap-3">
            <div class="loader-spinner"></div>
            <div class="text-center">
                <p class="text-white font-bold text-sm tracking-wider">PANTAU.ID</p>
                <p class="text-slate-400 text-xs">Memuat Data Pemantauan Proyek...</p>
            </div>
        </div>
    </div>

    <!-- ==================== TOP BAR PEMERINTAH ==================== -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center flex-wrap gap-2">
            <!-- Left Badges -->
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 font-semibold text-amber-400">
                    <i class="fas fa-landmark text-amber-400"></i>
                    <span>SISTEM INFORMASI PEMANTAUAN PROYEK NASIONAL</span>
                </span>
                <span class="hidden md:inline text-slate-700">|</span>
                <span class="hidden md:inline-flex items-center gap-1.5 text-slate-400">
                    <span>Standar Simpul KPBU DJPI</span>
                </span>
            </div>

            <!-- Right Contacts & Time -->
            <div class="flex items-center gap-4 text-slate-300">
                <a href="tel:02172284444" class="hidden sm:inline-flex items-center gap-1.5 hover:text-white transition">
                    <i class="fas fa-phone-alt text-slate-400 text-[10px]"></i>
                    <span class="font-mono">(021) 7228-4444</span>
                </a>
                <a href="mailto:info@pantauId.pu.go.id" class="hidden sm:inline-flex items-center gap-1.5 hover:text-white transition">
                    <i class="fas fa-envelope text-slate-400 text-[10px]"></i>
                    <span>info@pantauId.pu.go.id</span>
                </a>
                <span class="inline-flex items-center gap-1.5 text-[11px] text-slate-300 font-medium">
                    <i class="fas fa-calendar-check text-slate-400 text-[10px]"></i>
                    {{ now()->locale('id')->translatedFormat('d F Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- ==================== MAIN HEADER NAVIGATION ==================== -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-18 py-3">
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group text-decoration-none">
                    <div class="w-10 h-10 bg-slate-900 rounded-lg flex items-center justify-center text-amber-400 shadow-sm">
                        <i class="fas fa-city text-lg"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-extrabold text-slate-900 tracking-tight leading-none">PANTAU.ID</span>
                            <span class="px-1.5 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded border border-amber-200">
                                DJPI
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                            Pemantauan Proyek Pemerintah & Swasta
                        </p>
                    </div>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="{{ url('/') }}" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                        <i class="fas fa-home text-sm"></i>
                        <span>Beranda</span>
                    </a>
                    <a href="{{ url('/peta') }}" class="nav-item {{ request()->is('peta') ? 'active' : '' }}">
                        <i class="fas fa-map-location-dot text-sm"></i>
                        <span>Peta Proyek</span>
                    </a>
                    <a href="{{ url('/proyek') }}" class="nav-item {{ request()->is('proyek*') ? 'active' : '' }}">
                        <i class="fas fa-layer-group text-sm"></i>
                        <span>Daftar Proyek</span>
                    </a>
                    <a href="{{ url('/statistik') }}" class="nav-item {{ request()->is('statistik') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie text-sm"></i>
                        <span>Statistik</span>
                    </a>
                </nav>

                <!-- Action CTA -->
                <div class="hidden md:flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        <span class="state-dot bg-emerald-500"></span>
                        <span>15 Proyek Aktif</span>
                    </span>
                    <a href="{{ url('/peta') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition">
                        <i class="fas fa-location-crosshairs text-amber-400"></i>
                        <span>Buka Peta</span>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" class="lg:hidden p-2 rounded-lg text-slate-700 hover:bg-slate-100 focus:outline-none transition" onclick="document.getElementById('mobileNavPanel').classList.toggle('hidden')">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Panel -->
        <div id="mobileNavPanel" class="hidden lg:hidden bg-white border-t border-slate-200 px-4 py-3 shadow-md">
            <div class="flex flex-col gap-1">
                <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->is('/') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-home w-5 text-center text-amber-400"></i> Beranda
                </a>
                <a href="{{ url('/peta') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->is('peta') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-map-location-dot w-5 text-center text-amber-400"></i> Peta Proyek
                </a>
                <a href="{{ url('/proyek') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->is('proyek*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-layer-group w-5 text-center text-amber-400"></i> Daftar Proyek
                </a>
                <a href="{{ url('/statistik') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold {{ request()->is('statistik') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    <i class="fas fa-chart-pie w-5 text-center text-amber-400"></i> Statistik
                </a>
            </div>
        </div>
    </header>

    <!-- ==================== MAIN CONTENT ==================== -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- ==================== FOOTER ==================== -->
    <footer class="bg-slate-900 text-slate-300 mt-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Col 1: Brand & Purpose -->
                <div class="lg:col-span-2 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center text-amber-400">
                            <i class="fas fa-city"></i>
                        </div>
                        <span class="text-lg font-extrabold text-white tracking-tight">PANTAU.ID</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-md">
                        Sistem Informasi Pemantauan Proyek terpadu yang memadukan visualisasi geospasial (GIS) dengan katalog data proyek infrastruktur prioritas dari sektor Pemerintah (APBN/KPBU) dan Swasta di seluruh Indonesia.
                    </p>
                </div>

                <!-- Col 2: Navigation -->
                <div>
                    <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-3">Navigasi Utama</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="{{ url('/') }}" class="hover:text-white transition">Beranda Utama</a></li>
                        <li><a href="{{ url('/peta') }}" class="hover:text-white transition">Peta Geospasial</a></li>
                        <li><a href="{{ url('/proyek') }}" class="hover:text-white transition">Daftar Proyek Prioritas</a></li>
                        <li><a href="{{ url('/statistik') }}" class="hover:text-white transition">Statistik & Analitik</a></li>
                    </ul>
                </div>

                <!-- Col 3: Contact -->
                <div>
                    <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-3">Kontak Instansi</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-map-pin text-slate-400 mt-0.5"></i>
                            <span>Jl. Pattimura No. 20, Kebayoran Baru, Jakarta Selatan</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-phone text-slate-400"></i>
                            <a href="tel:02172284444" class="hover:text-white transition font-mono">(021) 7228-4444</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-envelope text-slate-400"></i>
                            <a href="mailto:info@pantauID.pu.go.id" class="hover:text-white transition">info@pantauId.pu.go.id</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Subfooter -->
        <div class="border-t border-slate-800 bg-slate-950 py-3.5">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-2">
                <span>&copy; {{ date('Y') }} SIPANTAU · Sistem Informasi Pemantauan Proyek Nasional. Mengacu standar Simpul DJPI PU.</span>
                <span>Standar Geospasial WGS84</span>
            </div>
        </div>
    </footer>

    <!-- Leaflet JS (Pinned with SRI) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Global Loader Script -->
    <script>
        function dismissPreloader() {
            const loader = document.getElementById('globalPreloader');
            if (loader && !loader.classList.contains('hidden-loader')) {
                loader.classList.add('hidden-loader');
                loader.setAttribute('aria-hidden', 'true');
                setTimeout(() => {
                    if (loader) loader.style.display = 'none';
                }, 360);
            }
        }
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(dismissPreloader, 150);
        });
        setTimeout(dismissPreloader, 450);
    </script>

    @stack('scripts')
</body>
</html>
