@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-4"><i class="fas fa-user-tie"></i> Dashboard Petugas Perpustakaan</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--danger-color), #DC2626);">
                <div class="stat-value">{{ $pending_loans }}</div>
                <div class="stat-label"><i class="fas fa-clock"></i> Menunggu Persetujuan</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning-color), #D97706);">
                <div class="stat-value">{{ $approved_loans }}</div>
                <div class="stat-label"><i class="fas fa-check"></i> Sudah Disetujui</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--secondary-color), #059669);">
                <div class="stat-value">{{ $active_loans }}</div>
                <div class="stat-label"><i class="fas fa-exchange-alt"></i> Peminjaman Aktif</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                <div class="stat-value">{{ $available_books }}</div>
                <div class="stat-label"><i class="fas fa-book"></i> Buku Tersedia</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pending Loans -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-hourglass-half"></i> Peminjaman Menunggu Persetujuan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Peminjam</th>
                                    <th>Buku</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pending_list as $loan)
                                    <tr>
                                        <td>
                                            <strong>{{ $loan->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $loan->user->email }}</small>
                                        </td>
                                        <td>{{ $loan->book->title }}</td>
                                        <td>
                                            <form action="{{ route('loans.approve', $loan) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Tidak ada peminjaman menunggu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tasks"></i> Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('loans.pending') }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-clock"></i> Kelola Persetujuan
                        </a>
                        <a href="{{ route('loans.return-form') }}" class="btn btn-info btn-lg">
                            <i class="fas fa-undo"></i> Proses Pengembalian
                        </a>
                        <a href="{{ route('loans.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-list"></i> Lihat Semua Peminjaman
                        </a>
                        <a href="{{ route('books.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-book"></i> Koleksi Buku
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scan Barcode Section -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-barcode"></i> Scan Barcode Peminjaman</h5>
                </div>
                <div class="card-body">
                    <form id="scanForm">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg" id="barcodeInput" 
                                   placeholder="Arahkan scanner barcode ke sini..." autofocus>
                            <button class="btn btn-primary btn-lg" type="button" id="scanBtn">
                                <i class="fas fa-search"></i> Scan
                            </button>
                        </div>
                    </form>
                    <div id="scanResult"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
window.__VIEW_CONFIG = {
    'e1': @json(route("loans.scan-barcode"))
};
</script>
<script src="{{ asset('js/views/dashboard/staff.js') }}"></script>
@endsection
@endsection
