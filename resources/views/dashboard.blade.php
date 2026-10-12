@extends('layouts.app')

@section('title', 'Dashboard Analitik')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark m-0 d-flex align-items-center gap-2">
                <span>Dashboard Tanikuy</span>
                <span class="badge bg-success-subtle text-success fs-6 border border-success-subtle">Online</span>
            </h1>
            <p class="text-muted small m-0 mt-1">Sistem Cerdas Manajemen Inventory Pertanian & Algoritma EOQ</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('eoq.index') }}"
                    class="btn btn-outline-success fw-semibold d-inline-flex align-items-center gap-2">
                    <i class="bi bi-cpu"></i>
                    <span>Analisis EOoooQ</span>
                </a>
            @endif
            <a href="{{ route('transactions.create') }}" class="btn btn-brand-lime">
                <i class="bi bi-cart-plus-fill"></i>
                <span>Transaksi Baru</span>
            </a>
        </div>
    </div>

    <!-- Top KPI Stats Row (Spark Admin Theme) -->
    <div class="row g-4 mb-4">
        <!-- Card 1: Executive Dark Forest Hero Promo Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100 text-white position-relative overflow-hidden"
                style="background-color: var(--brand-forest-dark); border-color: var(--border-dark-green);">
                <div class="position-absolute end-0 top-0 p-3 opacity-25">
                    <i class="bi bi-asterisk display-3 text-warning"></i>
                </div>
                <div class="small fw-bold text-uppercase text-white-50 mb-1" style="letter-spacing: 0.08em;">Status
                    Inventory</div>
                <div class="h3 fw-bold mb-2 text-warning">
                    {{ $criticalProducts->count() }} Produk
                </div>
                <div class="small text-white-50 mb-3">
                    @if($criticalProducts->count() > 0)
                        Memerlukan pemesanan ulang (Reorder EOQ) segera.
                    @else
                        Semua stok produk dalam batas aman.
                    @endif
                </div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('eoq.index') }}"
                        class="btn btn-sm btn-outline-light rounded-pill px-3 mt-auto align-self-start fw-semibold">
                        Lihat Kebutuhan <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @else
                    <a href="{{ route('stock.index') }}"
                        class="btn btn-sm btn-outline-light rounded-pill px-3 mt-auto align-self-start fw-semibold">
                        Lihat Stok Barang <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Card 2: Penjualan Bulan Ini -->
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Penjualan Bulan Ini</span>
                    <div class="p-2 rounded-circle" style="background-color: var(--sys-green-bg); color: var(--sys-green);">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <div class="h3 fw-bold text-dark mb-1">
                    {{ formatRupiah($monthRevenue) }}
                </div>
                <div class="small text-muted d-flex align-items-center gap-1">
                    <i class="bi bi-check2-circle text-success"></i>
                    <span>{{ $totalTransactionsMonth }} transaksi diselesaikan</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Penjualan Hari Ini -->
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Penjualan Hari Ini</span>
                    <div class="p-2 rounded-circle"
                        style="background-color: rgba(180, 241, 5, 0.2); color: var(--brand-forest-dark);">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </div>
                </div>
                <div class="h3 fw-bold text-dark mb-1">
                    {{ formatRupiah($todayRevenue) }}
                </div>
                <div class="small text-muted">
                    Tanggal: {{ now()->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Card 4: Total SKU & Master Data -->
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Katalog Produk</span>
                    <div class="p-2 rounded-circle bg-light text-primary">
                        <i class="bi bi-box-seam fs-5"></i>
                    </div>
                </div>
                <div class="h3 fw-bold text-dark mb-1">
                    {{ $totalProducts }} SKU
                </div>
                <div class="small text-muted d-flex justify-content-between">
                    <span>{{ $totalCategories }} Kategori</span>
                    <span>&bull;</span>
                    <span>{{ $totalSuppliers }} Supplier</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Section -->
    <div class="row g-4 mb-4">
        <!-- Left Column: Sales Chart & Stock Alerts -->
        <div class="col-lg-8">
            <!-- Sales Trend ApexCharts Card -->
            <div class="table-card-custom mb-4">
                <div class="table-card-header">
                    <div>
                        <h5 class="fw-bold text-dark m-0">Tren Penjualan (14 Hari Terakhir)</h5>
                        <span class="text-muted small">Grafik akumulasi omset harian hasil transaksi</span>
                    </div>
                    <div class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                        Total Hari Ini: {{ formatRupiah($todayRevenue) }}
                    </div>
                </div>
                <div class="p-3">
                    <div id="salesTrendChart" style="min-height: 300px;"></div>
                </div>
            </div>

            <!-- Critical Stock & Reorder Alert Table -->
            <div class="table-card-custom mb-4">
                <div class="table-card-header">
                    <div>
                        <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                            <span>Peringatan Stok & Saran Order EOQ</span>
                        </h5>
                        <span class="text-muted small">Barang yang menyentuh batas minimum dan harus segera dipesan</span>
                    </div>
                    <a href="{{ route('eoq.index') }}" class="btn btn-sm btn-outline-success fw-semibold">
                        Semua Rekomendasi EOQ
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Kode & Produk</th>
                                <th>Kategori</th>
                                <th>Sisa Stok</th>
                                <th>Batas Min</th>
                                <th>Rekomendasi EOQ</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($criticalProducts as $prod)
                                @php
                                    $eoqCalc = app(\App\Services\EoqService::class)->calculate($prod);
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $prod->nama_barang }}</div>
                                        <div class="text-muted small">{{ $prod->kode_barang }}</div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill"
                                            style="background-color: {{ $prod->kategori->warna ?? '#10B981' }}20; color: {{ $prod->kategori->warna ?? '#10B981' }}; font-weight: 600;">
                                            {{ $prod->kategori->nama ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge-critical">
                                            {{ $prod->stok_saat_ini }} {{ $prod->satuan }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $prod->stok_minimum }} {{ $prod->satuan }}
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            Pesan {{ $eoqCalc['eoq_qty'] }} {{ $prod->satuan }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            Biaya order ~ {{ formatRupiah($eoqCalc['eoq_qty'] * $prod->harga_beli) }}
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('stock.index') }}" class="btn btn-sm btn-light border"
                                            title="Tambah Stok Masuk">
                                            <i class="bi bi-plus-lg"></i> Restock
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-shield-check fs-2 text-success d-block mb-1"></i>
                                        Semua stok produk saat ini dalam batas aman di atas minimum.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Top Products & Recent Transactions -->
        <div class="col-lg-4">
            <!-- Top Selling Products Card -->
            <div class="table-card-custom mb-4">
                <div class="table-card-header">
                    <div>
                        <h5 class="fw-bold text-dark m-0">Produk Terlaris</h5>
                        <span class="text-muted small">Paling banyak terjual</span>
                    </div>
                </div>
                <div class="p-3">
                    <div class="list-group list-group-flush">
                        @forelse($topProducts as $index => $top)
                            <div class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between border-0">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                        style="width: 32px; height: 32px; background: {{ $index === 0 ? 'var(--brand-lime)' : '#E9EFEF' }}; color: var(--brand-forest-dark);">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 170px;">
                                            {{ $top->nama_barang }}</div>
                                        <div class="text-muted small">{{ formatRupiah($top->total_omset) }}</div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        {{ $top->total_qty }} {{ $top->satuan }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small text-center py-3">Belum ada data penjualan tercatat.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Card -->
            <div class="table-card-custom">
                <div class="table-card-header">
                    <div>
                        <h5 class="fw-bold text-dark m-0">Transaksi Terakhir</h5>
                        <span class="text-muted small">Penjualan terbaru</span>
                    </div>
                    <a href="{{ route('transactions.index') }}"
                        class="small text-success fw-semibold text-decoration-none">Lihat Semua</a>
                </div>
                <div class="p-3">
                    <div class="list-group list-group-flush">
                        @forelse($recentTransactions as $tx)
                            <div class="list-group-item px-0 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">{{ $tx->nomor_transaksi }}</span>
                                    <span class="fw-bold text-success small">{{ formatRupiah($tx->total) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-muted"
                                    style="font-size: 0.75rem;">
                                    <span>{{ $tx->nama_pelanggan ?? 'Umum' }} &bull;
                                        {{ strtoupper($tx->metode_pembayaran) }}</span>
                                    <span>{{ $tx->tanggal->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small text-center py-3">Belum ada transaksi.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const labels = @json($chartLabels);
            const salesData = @json($chartSales);

            const options = {
                series: [{
                    name: 'Omset Penjualan (Rp)',
                    data: salesData
                }],
                chart: {
                    type: 'area',
                    height: 320,
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                colors: ['#072F1F'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 90, 100],
                        colorStops: [
                            { offset: 0, color: '#B4F105', opacity: 0.6 },
                            { offset: 100, color: '#072F1F', opacity: 0.05 }
                        ]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    colors: ['#072F1F']
                },
                markers: {
                    size: 4,
                    colors: ['#B4F105'],
                    strokeColors: '#072F1F',
                    strokeWidth: 2,
                    hover: { size: 6 }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: labels,
                    labels: { style: { colors: '#6C7E75', fontSize: '12px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                            if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                            return value;
                        },
                        style: { colors: '#6C7E75', fontSize: '12px' }
                    }
                },
                grid: {
                    borderColor: '#E9EFEF',
                    strokeDashArray: 4
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#salesTrendChart"), options);
            chart.render();
        });
    </script>
@endpush