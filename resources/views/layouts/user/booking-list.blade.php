@extends('layouts.user')

@section('title', 'Riwayat Sewa')

@push('styles')
    @vite('resources/css/user/booking-list.css')
@endpush

@section('content')
<div class="booking-section">
    <h2 class="section-title">
        <i class="fas fa-history"></i>
        Riwayat Pemesanan Anda
    </h2>

    <div class="booking-list">
        @forelse($bookings as $booking)
            <div class="booking-card">
                <div class="booking-header">
                    <div>
                        <div class="vehicle-name">{{ $booking->vehicle->name ?? 'Kendaraan Dihapus' }}</div>
                        <div class="booking-id">ID Pesanan: #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="status-badge status-{{ strtolower(str_replace(' ', '-', $booking->status)) }}">
                        {{ $booking->status }}
                    </div>
                </div>

                <div class="booking-details">
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <div class="detail-label">Tanggal Mulai</div>
                            <div class="detail-value">{{ \Carbon\Carbon::parse($booking->start_date)->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-calendar-check"></i></div>
                        <div>
                            <div class="detail-label">Tanggal Selesai</div>
                            <div class="detail-value">{{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div>
                            <div class="detail-label">Total Pembayaran</div>
                            <div class="detail-value">Rp {{ number_format($booking->total_payment, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('booking.print', $booking->id) }}" class="cta-btn" target="_blank">
                        <i class="fas fa-file-pdf"></i> Cetak Pesanan
                    </a>

                    @if(in_array($booking->status, ['Disetujui']))
                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" onclick="return confirm('Yakin batalkan?')" class="cta-btn cancel">
                                <i class="fas fa-times-circle"></i> Batalkan
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('booking.completeForm', $booking->id) }}" class="cta-btn review">
                        <i class="fas fa-star"></i> Selesaikan Pesanan
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <h3>Anda Belum Memiliki Riwayat Pemesanan</h3>
                <p>Silakan lakukan pemesanan pertama Anda.</p>
                <a href="{{ route('user.dashboard') }}" class="cta-btn">
                    <i class="fas fa-search"></i> Cari Kendaraan
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingCards = document.querySelectorAll('.booking-card');
    bookingCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush
