@extends('layouts.app')

@section('title', 'Dashboard Peminjam')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-4"><i class="fas fa-user"></i> Dashboard Peminjaman Anda</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--secondary-color), #059669);">
                <div class="stat-value">{{ $active_loans }}</div>
                <div class="stat-label"><i class="fas fa-book"></i> Sedang Dipinjam</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning-color), #D97706);">
                <div class="stat-value">{{ $pending_loans }}</div>
                <div class="stat-label"><i class="fas fa-clock"></i> Menunggu Persetujuan</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                <div class="stat-value">{{ $returned_loans }}</div>
                <div class="stat-label"><i class="fas fa-undo"></i> Dikembalikan</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--primary-color), #4338CA);">
                <div class="stat-value">{{ $total_borrowed }}</div>
                <div class="stat-label"><i class="fas fa-history"></i> Total Peminjaman</div>
            </div>
        </div>
    </div>

    <!-- Active Loans -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-book-open"></i> Buku yang Sedang Dipinjam</h5>
                </div>
                <div class="card-body">
                    @if ($active_loans_list->count() > 0)
                        <div class="row">
                            @foreach ($active_loans_list as $loan)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $loan->book->title }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <strong>Penulis:</strong> {{ $loan->book->author }}<br>
                                                    <strong>Tanggal Peminjaman:</strong> {{ $loan->loan_date->format('d M Y') }}<br>
                                                    <strong>Jatuh Tempo:</strong> {{ $loan->due_date->format('d M Y') }}
                                                </small>
                                            </p>
                                            <span class="badge bg-success">Aktif</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Anda tidak memiliki buku yang sedang dipinjam
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-hourglass-half"></i> Permintaan Menunggu Persetujuan</h5>
                </div>
                <div class="card-body">
                    @if ($pending_loans_list->count() > 0)
                        <div class="row">
                            @foreach ($pending_loans_list as $loan)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-warning">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $loan->book->title }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <strong>Penulis:</strong> {{ $loan->book->author }}<br>
                                                    <strong>Diminta pada:</strong> {{ $loan->created_at->format('d M Y H:i') }}
                                                </small>
                                            </p>
                                            <span class="badge bg-warning">Menunggu Persetujuan</span>
                                            <div class="mt-2">
                                                <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Lihat Barcode
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Tidak ada permintaan peminjaman yang sedang menunggu
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <h5 class="mb-3">Aksi Cepat</h5>
            <div class="d-grid gap-2 d-md-flex">
                <a href="{{ route('books.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-book"></i> Jelajahi Koleksi Buku
                </a>
                <a href="{{ route('loans.create') }}" class="btn btn-success btn-lg">
                    <i class="fas fa-plus-circle"></i> Pinjam Buku Baru
                </a>
                <a href="{{ route('loans.index') }}" class="btn btn-info btn-lg">
                    <i class="fas fa-list"></i> Riwayat Peminjaman
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
