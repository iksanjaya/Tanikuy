<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Tanikuy Inventory & EOQ System</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Assets from Spark Admin -->
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/apexcharts/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <style>
        /* Custom polish for Tanikuy branding and contrast */
        .sidebar-wrapper {
            overflow-y: auto !important;
            overflow-x: hidden !important;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
        }
        .sidebar-wrapper::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-wrapper::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 10px;
        }
        .sidebar-wrapper::-webkit-scrollbar-thumb:hover {
            background: var(--brand-lime, #B4F105);
        }
        .sidebar-brand, .sidebar-profile, .sidebar-menu-section {
            flex-shrink: 0;
        }

        .brand-lime-badge {
            background-color: var(--brand-lime);
            color: var(--brand-forest-dark);
            font-weight: 700;
            border-radius: 50rem;
            padding: 0.2rem 0.6rem;
            font-size: 0.75rem;
        }
        .table-card-custom {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .table-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .btn-brand-lime {
            background-color: var(--brand-lime);
            color: var(--brand-forest-dark);
            font-weight: 700;
            border: none;
            border-radius: var(--radius-md);
            padding: 0.6rem 1.25rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-brand-lime:hover {
            background-color: var(--brand-lime-hover);
            color: var(--brand-forest-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--brand-lime-translucent);
        }
        .btn-brand-forest {
            background-color: var(--brand-forest-dark);
            color: #ffffff;
            font-weight: 600;
            border: none;
            border-radius: var(--radius-md);
            padding: 0.6rem 1.25rem;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-brand-forest:hover {
            background-color: var(--brand-forest-medium);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .status-badge-critical {
            background: var(--sys-red-bg);
            color: var(--sys-red);
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .status-badge-warning {
            background: var(--sys-orange-bg);
            color: var(--sys-orange);
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .status-badge-success {
            background: var(--sys-green-bg);
            color: var(--sys-green);
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        /* Notification Dropdown Styling */
        .hover-bg-light {
            transition: background-color 0.2s ease;
        }
        .hover-bg-light:hover {
            background-color: rgba(0, 0, 0, 0.06);
        }
        .notification-dropdown-menu {
            border-radius: var(--radius-lg);
            box-shadow: 0 12px 36px rgba(5, 28, 18, 0.2);
            animation: fadeInDropdown 0.2s ease-out;
        }
        @keyframes fadeInDropdown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .notif-item {
            text-decoration: none;
            color: inherit;
        }
        .notif-item:hover {
            background-color: #f8fafc;
        }
        .notif-filter-btn.active {
            background-color: var(--brand-forest-dark);
            color: #ffffff !important;
        }
        .notification-list-body::-webkit-scrollbar {
            width: 5px;
        }
        .notification-list-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        if (!isset($notifications)) {
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationsData = $notificationService->getNotifications(auth()->user());
            $notifications = $notificationsData['items'];
            $notificationCount = $notificationsData['total_count'];
            $urgentNotificationCount = $notificationsData['urgent_count'];
            $lowStockCount = $notificationsData['low_stock_count'];
        }
    @endphp

    <!-- Sidebar Component (Forest Dark #051C12) -->
    <aside class="sidebar-wrapper">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="bi bi-asterisk"></i>
            <span>Tanikuy</span>
        </a>

        <div class="sidebar-menu-section">
            <div class="sidebar-menu-title">UTAMA</div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item">
                    <a href="{{ route('dashboard') }}" class="sidebar-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('transactions.create') }}" class="sidebar-menu-link {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
                        <i class="bi bi-cart-plus-fill"></i>
                        <span>Kasir / Penjualan</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('transactions.index') }}" class="sidebar-menu-link {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.show') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>Riwayat Penjualan</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-menu-section">
            <div class="sidebar-menu-title">INVENTORY & EOQ</div>
            <ul class="sidebar-menu-list">
                <li class="sidebar-menu-item">
                    <a href="{{ route('products.index') }}" class="sidebar-menu-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>{{ auth()->user()->isAdmin() ? 'Kelola Barang' : 'Katalog Barang' }}</span>
                    </a>
                </li>
                @if(auth()->user()->isAdmin())
                    <li class="sidebar-menu-item">
                        <a href="{{ route('categories.index') }}" class="sidebar-menu-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="bi bi-tags-fill"></i>
                            <span>Kategori Produk</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item">
                        <a href="{{ route('suppliers.index') }}" class="sidebar-menu-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                            <i class="bi bi-truck"></i>
                            <span>Kelola Supplier</span>
                        </a>
                    </li>
                @endif
                <li class="sidebar-menu-item">
                    <a href="{{ route('stock.index') }}" class="sidebar-menu-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-left-right"></i>
                        <span>Pemantauan Stok</span>
                        @if($lowStockCount > 0)
                            <span class="sidebar-menu-badge bg-danger text-white">{{ $lowStockCount }}</span>
                        @endif
                    </a>
                </li>
                @if(auth()->user()->isAdmin())
                    <li class="sidebar-menu-item">
                        <a href="{{ route('eoq.index') }}" class="sidebar-menu-link {{ request()->routeIs('eoq.*') ? 'active' : '' }}">
                            <i class="bi bi-cpu-fill"></i>
                            <span>Algoritma EOQ</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        @if(auth()->user()->isAdmin())
            <div class="sidebar-menu-section">
                <div class="sidebar-menu-title">LAPORAN</div>
                <ul class="sidebar-menu-list">
                    <li class="sidebar-menu-item">
                        <a href="{{ route('reports.sales') }}" class="sidebar-menu-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                            <span>Laporan Bisnis</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-menu-section">
                <div class="sidebar-menu-title">SISTEM & AKSES</div>
                <ul class="sidebar-menu-list">
                    <li class="sidebar-menu-item">
                        <a href="{{ route('users.index') }}" class="sidebar-menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span>Kelola Pengguna</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif

        <!-- Bottom Profile Dock -->
        <div class="sidebar-profile">
            <img src="{{ asset('assets/images/user-4.jpg') }}" alt="User" class="sidebar-profile-img" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=072F1F&color=B4F105';">
            <div class="sidebar-profile-info">
                <div class="sidebar-profile-name d-flex align-items-center gap-1">
                    <span class="text-truncate" style="max-width: 95px;">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                    @if(auth()->user()->isAdmin())
                        <span class="badge bg-success text-white py-0 px-1" style="font-size: 0.62rem;">Admin</span>
                    @else
                        <span class="badge bg-warning text-dark py-0 px-1" style="font-size: 0.62rem;">Kasir</span>
                    @endif
                </div>
                <div class="sidebar-profile-email text-truncate" style="max-width: 140px;">{{ auth()->user()->email ?? 'user@tanikuy.com' }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline m-0 p-0">
                @csrf
                <button type="submit" class="btn btn-link text-white-50 p-1 text-decoration-none" title="Keluar / Logout">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="main-wrapper">
        <!-- Top Navbar Component -->
        <header class="navbar-custom">
            <div class="navbar-left">
                <a href="{{ route('transactions.create') }}" class="btn-quick-action">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Transaksi Baru</span>
                </a>
            </div>

            <div class="navbar-center d-flex align-items-center justify-content-center">
                <div class="text-muted small fw-semibold">
                    <i class="bi bi-calendar3 me-1 text-success"></i>
                    {{ now()->translatedFormat('l, d F Y') }} &bull; UMKM Pertanian Modern
                </div>
            </div>

            <div class="navbar-right d-flex align-items-center justify-content-end gap-3">
                <!-- Notification Bell Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link position-relative text-dark text-decoration-none p-2 rounded-circle hover-bg-light border-0 shadow-none d-flex align-items-center justify-content-center" 
                            type="button" 
                            id="notificationBellBtn" 
                            data-bs-toggle="dropdown" 
                            data-bs-auto-close="outside"
                            aria-expanded="false" 
                            title="Pemberitahuan & Notifikasi">
                        <i class="bi bi-bell-fill fs-5 text-secondary"></i>
                        @if(($urgentNotificationCount ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                {{ $urgentNotificationCount }}
                            </span>
                        @elseif(($notificationCount ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
                                <span class="visually-hidden">Notifikasi Baru</span>
                            </span>
                        @endif
                    </button>
                    
                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 notification-dropdown-menu" 
                         style="width: 380px; max-width: 92vw; border-radius: var(--radius-lg); overflow: hidden; z-index: 1060;">
                        <!-- Dropdown Header -->
                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="background-color: var(--brand-forest-dark); color: #ffffff;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-bell-fill" style="color: var(--brand-lime);"></i>
                                <span class="fw-bold fs-6">Notifikasi Sistem</span>
                            </div>
                            <span class="badge rounded-pill" style="background-color: rgba(180, 241, 5, 0.2); color: var(--brand-lime); font-size: 0.72rem; border: 1px solid rgba(180, 241, 5, 0.4);">
                                {{ count($notifications ?? []) }} Terkini
                            </span>
                        </div>

                        <!-- Filter Tabs -->
                        <div class="px-3 pt-2 pb-2 bg-light border-bottom d-flex gap-1 overflow-auto notif-filter-bar">
                            <button type="button" class="btn btn-sm py-1 px-2 rounded-pill notif-filter-btn active fw-semibold" data-filter="all" style="font-size: 0.75rem;">
                                Semua ({{ count($notifications ?? []) }})
                            </button>
                            @php
                                $critCount = collect($notifications ?? [])->where('type', 'critical')->count();
                                $txCount = collect($notifications ?? [])->where('type', 'transaction')->count();
                            @endphp
                            @if($critCount > 0)
                                <button type="button" class="btn btn-sm py-1 px-2 rounded-pill notif-filter-btn text-muted fw-semibold" data-filter="critical" style="font-size: 0.75rem;">
                                    Stok Kritis ({{ $critCount }})
                                </button>
                            @endif
                            @if($txCount > 0)
                                <button type="button" class="btn btn-sm py-1 px-2 rounded-pill notif-filter-btn text-muted fw-semibold" data-filter="transaction" style="font-size: 0.75rem;">
                                    Penjualan ({{ $txCount }})
                                </button>
                            @endif
                        </div>

                        <!-- Notification Items Scrollable Body -->
                        <div class="notification-list-body" style="max-height: 380px; overflow-y: auto;">
                            @forelse($notifications ?? [] as $notif)
                                <a href="{{ $notif['url'] }}" 
                                   class="dropdown-item d-flex align-items-start gap-3 p-3 border-bottom text-wrap notif-item notif-type-{{ $notif['type'] }}" 
                                   style="transition: background-color 0.15s ease; white-space: normal;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $notif['bg_color'] }}" style="width: 38px; height: 38px; margin-top: 2px;">
                                        <i class="{{ $notif['icon'] }} {{ $notif['icon_color'] }} fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                            <span class="badge {{ $notif['badge_class'] }}" style="font-size: 0.65rem; font-weight: 600;">
                                                {{ $notif['badge'] }}
                                            </span>
                                            <span class="text-muted small" style="font-size: 0.7rem;">
                                                <i class="bi bi-clock me-1"></i>{{ $notif['time'] }}
                                            </span>
                                        </div>
                                        <div class="fw-bold text-dark small mb-1" style="font-size: 0.83rem; line-height: 1.3;">
                                            {{ $notif['title'] }}
                                        </div>
                                        <div class="text-secondary small" style="font-size: 0.75rem; line-height: 1.35;">
                                            {{ $notif['message'] }}
                                        </div>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted align-self-center fs-6 flex-shrink-0 ms-1 opacity-50"></i>
                                </a>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    <div class="rounded-circle bg-light d-inline-flex p-3 mb-2 text-success">
                                        <i class="bi bi-check2-circle fs-2"></i>
                                    </div>
                                    <div class="fw-semibold small">Semua Berjalan Normal</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Tidak ada pemberitahuan atau stok kritis saat ini.</div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Dropdown Footer -->
                        <div class="p-2 bg-light border-top d-flex justify-content-between align-items-center gap-2">
                            <a href="{{ route('stock.index') }}" class="btn btn-sm btn-white border flex-fill text-dark fw-semibold" style="font-size: 0.75rem; background: #ffffff;">
                                <i class="bi bi-box-seam me-1 text-danger"></i> Pantau Stok
                            </a>
                            <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-white border flex-fill text-dark fw-semibold" style="font-size: 0.75rem; background: #ffffff;">
                                <i class="bi bi-receipt me-1 text-success"></i> Riwayat POS
                            </a>
                        </div>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-sm btn-light rounded-pill px-3 py-1 dropdown-toggle d-flex align-items-center gap-2 border" type="button" data-bs-toggle="dropdown">
                        <span class="fw-semibold text-dark">{{ auth()->user()->name ?? 'User' }}</span>
                        @if(auth()->user()->isAdmin())
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">Admin</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.68rem;">Kasir</span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: var(--radius-lg);">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <div class="fw-bold">{{ auth()->user()->name ?? 'Pengguna Tanikuy' }}</div>
                                <div class="small text-muted">{{ auth()->user()->email ?? 'user@tanikuy.com' }}</div>
                                <div class="mt-1">
                                    <span class="badge {{ auth()->user()->isAdmin() ? 'bg-success text-white' : 'bg-warning text-dark' }}" style="font-size: 0.7rem;">
                                        {{ auth()->user()->role_label }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @if(auth()->user()->isAdmin())
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('users.index') }}">
                                    <i class="bi bi-people me-2 text-secondary"></i> Kelola Pengguna
                                </a>
                            </li>
                        @endif
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                    <i class="bi bi-box-arrow-right"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert" style="border-radius: var(--radius-lg); background-color: var(--sys-green-bg); color: var(--sys-green);">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert" style="border-radius: var(--radius-lg); background-color: var(--sys-red-bg); color: var(--sys-red);">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2" role="alert" style="border-radius: var(--radius-lg); background-color: var(--sys-orange-bg); color: var(--sys-orange);">
                <i class="bi bi-exclamation-circle-fill fs-5"></i>
                <div>{{ session('warning') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Dynamic View Body -->
        @yield('content')

        <!-- Footer Component -->
        <footer class="mt-auto pt-4 pb-2 border-top text-center text-muted small">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    &copy; {{ date('Y') }} <strong>Tanikuy</strong> &bull; Sistem Inventory & Pencatatan Penjualan UMKM Pertanian (Algoritma EOQ).
                </div>
                <div>
                    Desain Berbasis <span class="fw-semibold text-dark">Spark Admin v1.0</span> (Forest & Neon Theme)
                </div>
            </div>
        </footer>
    </main>

    <!-- Core Scripts -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Filter interaktif kategori notifikasi
            const filterBtns = document.querySelectorAll('.notif-filter-btn');
            const notifItems = document.querySelectorAll('.notif-item');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation(); // Mencegah dropdown tertutup saat mengganti tab filter
                    
                    filterBtns.forEach(b => {
                        b.classList.remove('active');
                        b.classList.add('text-muted');
                    });
                    this.classList.add('active');
                    this.classList.remove('text-muted');

                    const filter = this.getAttribute('data-filter');
                    notifItems.forEach(item => {
                        if (filter === 'all' || item.classList.contains('notif-type-' + filter)) {
                            item.style.setProperty('display', 'flex', 'important');
                        } else {
                            item.style.setProperty('display', 'none', 'important');
                        }
                    });
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
