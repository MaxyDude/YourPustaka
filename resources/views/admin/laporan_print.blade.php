<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }} - YourPustaka</title>
    <link rel="stylesheet" href="{{ asset('css/views/admin/laporan_print.css') }}">
</head>
<body>
    <div class="report-header">
        <div>
            <h1 class="report-title">{{ $reportTitle }}</h1>
            <p class="report-meta">Dicetak oleh: {{ $printedBy }}</p>
            <p class="report-meta">Tanggal cetak: {{ $generatedAt->format('d M Y H:i') }}</p>
            <p class="report-meta">Total data: {{ count($rows) }}</p>
        </div>
        <div class="action-group">
            <button class="btn" onclick="window.print()">Cetak</button>
            <button class="btn secondary" onclick="window.close()">Tutup</button>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        @foreach($row as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td class="empty" colspan="{{ count($columns) }}">Tidak ada data untuk dicetak.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="footer-note">Dokumen ini digenerate otomatis oleh sistem YourPustaka.</p>
</body>
</html>
