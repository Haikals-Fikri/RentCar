@extends('admin.dashboard-admin')

@section('content')

@vite('resources/css/admin/admin-booking.css')
@vite('resources/js/admin/admin-booking.js')

<h2 class="page-title">Data Booking</h2>

<div class="table-wrapper">
    <table class="booking-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Penyewa</th>
                <th>Kendaraan</th>
                <th>Metode</th>
                <th class="center">Bukti Pembayaran</th>
                <th>Status</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>

        <tbody>
        @forelse($bookings as $booking)

            @php
                $proofPath = $booking->payment_proof;
                $proofUrl  = $proofPath ? asset('storage/'.$proofPath) : null;
                $ext       = $proofUrl ? strtolower(pathinfo($proofUrl, PATHINFO_EXTENSION)) : null;
                $isImage   = in_array($ext, ['jpg','jpeg','png','webp']);

                $statusColor = match($booking->status) {
                    'Disetujui' => '#4ECDC4',
                    'Menunggu Konfirmasi' => '#FFD93D',
                    'Dibatalkan Owner', 'Dibatalkan User' => '#FF6B6B',
                    default => '#FFFFFF'
                };
            @endphp

            <tr>
                <td class="bold">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</td>

                <td>
                    <div class="bold">{{ optional($booking->user)->name ?? 'User dihapus' }}</div>
                    <small class="muted">{{ $booking->phone ?? '-' }}</small>
                </td>

                <td>{{ optional($booking->vehicle)->name ?? 'N/A' }}</td>

                <td>
                    <span class="badge">{{ $booking->payment_method }}</span>
                </td>

                {{-- BUKTI PEMBAYARAN --}}
                <td class="center">
                    @if($proofUrl && $isImage)
                        <img
                            src="{{ $proofUrl }}"
                            class="proof-thumb preview-image"
                            data-image="{{ $proofUrl }}"
                            alt="Bukti Pembayaran"
                        >
                    @elseif($proofUrl)
                        <a href="{{ $proofUrl }}" target="_blank" class="doc-link">
                            📄 Lihat Dokumen
                        </a>
                    @else
                        <span class="muted small">Belum Upload</span>
                    @endif
                </td>

                {{-- STATUS --}}
                <td>
                    <span class="status" style="color: {{ $statusColor }}">
                        {{ $booking->status }}
                    </span>
                </td>

                {{-- AKSI --}}
                <td class="center">
                    @if($proofUrl)
                        <a href="{{ route('booking.paymentproof', $booking->id) }}" class="btn-outline">
                            Download Bukti
                        </a>
                    @else
                        <span class="muted small">-</span>
                    @endif
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="7" class="empty-state">
                    <div class="icon">📋</div>
                    Belum ada riwayat booking yang ditemukan.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL PREVIEW --}}
<div id="imageModal" class="image-modal">
    <span class="close-modal">&times;</span>
    <img id="modalImage" alt="Preview Bukti">
</div>

@endsection
