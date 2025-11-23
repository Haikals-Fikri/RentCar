@extends('layouts.user')

@section('title', 'Riwayat Sewa')

@push('styles')
    @vite('resources/css/user/booking-list.css')
    <style>
        .countdown-timer {
            background: #ffeb3b;
            color: #333;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        .expired-text {
            color: #6c757d;
            font-style: italic;
        }
        .status-expired {
            background: #6c757d;
            color: white;
        }
    </style>
@endpush

@section('content')
<div class="booking-section">
    <h2 class="section-title">
        <i class="fas fa-history"></i>
        Riwayat Pemesanan Anda
    </h2>

    <div class="booking-list">
        @forelse($bookings as $booking)
            @php
                // Hitung waktu expired (24 jam dari created_at)
                $expiresAt = \Carbon\Carbon::parse($booking->created_at)->addHours(24);
                $isExpired = \Carbon\Carbon::now()->gt($expiresAt);
                $timeRemaining = \Carbon\Carbon::now()->diff($expiresAt);

                // Tentukan status display
                $displayStatus = $booking->status;
                if ($booking->status === 'Disetujui' && \Carbon\Carbon::parse($booking->end_date)->isPast()) {
                    $displayStatus = 'COMPLETED';
                } elseif ($isExpired && $booking->status === 'Disetujui') {
                    $displayStatus = 'EXPIRED';
                }

                // Tentukan tombol mana yang tampil
                $canBeCancelled = $booking->status === 'Disetujui'
                    && !$isExpired
                    && \Carbon\Carbon::parse($booking->end_date)->isFuture();

                $canBeCompleted = $booking->status === 'Disetujui'
                    && !$isExpired
                    && \Carbon\Carbon::parse($booking->end_date)->isPast();
            @endphp

            <div class="booking-card">
                <div class="booking-header">
                    <div>
                        <div class="vehicle-name">{{ $booking->vehicle->name ?? 'Kendaraan Dihapus' }}</div>
                        <div class="booking-id">ID Pesanan: #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="status-badge status-{{ strtolower(str_replace(' ', '-', $displayStatus)) }}">
                        {{ $displayStatus }}
                        @if($booking->status === 'Disetujui' && !$isExpired)
                            <span class="countdown-timer" data-expires="{{ $expiresAt }}">
                                {{ $timeRemaining->h }}h {{ $timeRemaining->i }}m
                            </span>
                        @endif
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
                    {{-- Tombol Cetak Pesanan (selalu tampil) --}}
                    <a href="{{ route('booking.print', $booking->id) }}" class="cta-btn" target="_blank">
                        <i class="fas fa-file-pdf"></i> Cetak Pesanan
                    </a>

                    {{-- Tombol Batalkan (hanya untuk yang bisa dibatalkan) --}}
                    @if($canBeCancelled)
                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" onclick="return confirm('Yakin batalkan?')" class="cta-btn cancel">
                                <i class="fas fa-times-circle"></i> Batalkan
                            </button>
                        </form>
                    @endif

                    {{-- Tombol Selesaikan Pesanan (hanya untuk yang sudah lewat tanggal) --}}
                    @if($canBeCompleted)
                        <a href="{{ route('booking.completeForm', $booking->id) }}" class="cta-btn review">
                            <i class="fas fa-star"></i> Selesaikan Pesanan
                        </a>
                    @endif

                    {{-- Pesan jika sudah expired --}}
                    @if($isExpired && $booking->status === 'Disetujui')
                        <span class="expired-text">Pesanan telah kadaluarsa (lebih dari 24 jam)</span>
                    @endif

                    {{-- Jika sudah COMPLETED --}}
                    @if($displayStatus === 'COMPLETED')
                        <span class="completed-text">Pesanan telah selesai</span>
                    @endif
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
    // Animasi cards
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

    // Countdown timer real-time
    function updateCountdowns() {
        const timers = document.querySelectorAll('.countdown-timer');

        timers.forEach(timer => {
            const expiresAt = new Date(timer.dataset.expires).getTime();
            const now = new Date().getTime();
            const distance = expiresAt - now;

            if (distance < 0) {
                timer.innerHTML = 'EXPIRED';
                timer.style.background = '#dc3545';
                timer.style.color = 'white';
                // Auto reload setelah 10 detik expired
                setTimeout(() => {
                    location.reload();
                }, 10000);
            } else {
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                timer.innerHTML = hours + "h " + minutes + "m " + seconds + "s";
            }
        });
    }

    // Update countdown setiap detik
    setInterval(updateCountdowns, 1000);
    updateCountdowns(); // Jalankan sekali saat load
});
</script>
@endpush
