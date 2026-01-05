@extends('layouts.owner.owner')

@section('title', 'Riwayat Pemesanan')

@push('styles')
    @vite('resources/css/owner/owner-booking.css')
@endpush

@push('scripts')
    @vite('resources/js/owner/booking-list.js')
@endpush

@section('content')

@include('layouts.owner.partials.header', [
    'headerTitle' => 'Riwayat Pemesanan'
])

<div class="dashboard-content">

    @if(session('success'))
        <div class="alert-success">✓ {{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-error">✗ {{ session('error') }}</div>
    @endif

    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Penyewa</th>
                    <th>Kendaraan</th>
                    <th>Metode</th>
                    <th>Bukti Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
            @forelse($bookings as $booking)

                @php
                    $proofUrl = $booking->payment_proof
                        ? Storage::url($booking->payment_proof)
                        : null;

                    $ext = $proofUrl ? strtolower(pathinfo($proofUrl, PATHINFO_EXTENSION)) : null;
                    $isImage = in_array($ext, ['jpg','jpeg','png','webp']);
                @endphp

                <tr>
                    <td>#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</td>

                    <td>
                        <strong>{{ $booking->user->name ?? 'N/A' }}</strong>
                        <div class="sub">{{ $booking->phone }}</div>
                    </td>

                    <td>
                        <strong>{{ $booking->vehicle->name }}</strong>
                        <div class="sub">
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y') }} –
                            {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y') }}
                        </div>
                        <div class="sub price">
                            Rp {{ number_format($booking->total_payment, 0, ',', '.') }}
                        </div>
                    </td>

                    <td>
                        <span class="badge {{ strtolower($booking->payment_method) === 'bayar di tempat' ? 'badge-cash' : 'badge-transfer' }}">
                            {{ $booking->payment_method }}
                        </span>
                    </td>

                    {{-- BUKTI PEMBAYARAN --}}
                    <td>
                        <div class="proof-action-wrapper">
                            @if($proofUrl && $isImage)
                                <div class="payment-image-container">
                                    <img src="{{ $proofUrl }}" class="payment-thumb" alt="Bukti">
                                    <div class="payment-overlay"><span>👁️</span></div>
                                </div>
                            @elseif($proofUrl)
                                <span class="text-muted">File Dokumen</span>
                            @else
                                <span class="no-proof">Belum ada</span>
                            @endif

                            @if($proofUrl)
                                <div class="proof-btns">
                                    <button type="button" class="btn-mini view">
                                        👁️ Lihat
                                    </button>
                                </div>
                            @endif
                        </div>
                    </td>

                    <td>
                        <span class="status {{ Str::slug($booking->status) }}">
                            {{ $booking->status }}
                        </span>
                    </td>

                    {{-- KOLOM AKSI --}}
                    <td class="action-cell">
                        @if($booking->payment_method === 'Transfer Bank')
                            {{-- Aksi untuk Transfer Bank --}}
                            @if($booking->status === 'Menunggu Konfirmasi')
                                <div class="action-container">
                                    <form method="POST" action="{{ route('booking.approve', $booking->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-approve">✔ Setuju</button>
                                    </form>
                                    <form method="POST" action="{{ route('booking.reject', $booking->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-reject">✖ Tolak</button>
                                    </form>
                                </div>
                            @elseif($booking->status === 'Disetujui' || $booking->status === 'Selesai')
                                <a href="{{ route('booking.paymentproof', $booking->id) }}" class="action-btn">
                                    🖨️ Invoice
                                </a>
                            @endif

                        @elseif($booking->payment_method === 'Bayar di Tempat')
                            {{-- Aksi untuk Bayar di Tempat --}}
                            @if(in_array($booking->status, ['Menunggu Pembayaran', 'Pending']))
                                <div class="action-container">
                                    <form method="POST" action="{{ route('booking.approve', $booking->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-approve">✔ Setuju</button>
                                    </form>
                                    <form method="POST" action="{{ route('booking.reject', $booking->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-reject">✖ Tolak</button>
                                    </form>
                                </div>
                            @elseif($booking->status === 'Disetujui' || $booking->status === 'Selesai')
                                <span class="status-paid" style="color: #28a745; font-weight: bold;">
                                    ✅ Pembayaran Lunas
                                </span>
                            @endif
                        @endif
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="7" class="empty">Belum ada data booking</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL --}}
<div id="imageModal" class="modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <img id="modalImage" class="modal-image">
    </div>
</div>

@endsection
