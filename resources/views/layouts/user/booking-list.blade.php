@extends('layouts.user.user')

@section('title', 'Riwayat Sewa')

@push('styles')
    @vite('resources/css/user/Booking-list.css')
@endpush

@stack('scripts')

@section('content')
<div class="booking-section">
    <h2 class="section-title">
        <i class="fas fa-history"></i>
        Riwayat Pemesanan Anda
    </h2>

    @if(session('success'))
        <div class="alert-success-custom">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="booking-list">
        @forelse($bookings as $booking)
            @php
                $expireAt = \Carbon\Carbon::parse($booking->created_at)->addHours(24);
                $isExpire = now()->gt($expireAt);

                $displayStatus = $booking->status;

                $canBeCancelled = in_array($booking->status, ['Menunggu Pembayaran','Menunggu Konfirmasi']) && !$isExpire;
            @endphp

            <div class="booking-card">
                <div class="booking-header">
                    <div>
                        <div class="vehicle-name">
                            <i class="fas fa-car"></i> {{ $booking->vehicle->name ?? 'Kendaraan' }}
                        </div>
                        <div class="booking-id">
                            ID: #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="payment-method-text">
                            <i class="fas fa-credit-card"></i>
                            Metode: <strong>{{ $booking->payment_method }}</strong>
                        </div>
                    </div>

                    <div class="status-badge status-{{ strtolower(str_replace(' ', '-', $displayStatus)) }}">
                        {{ strtoupper($displayStatus) }}
                    </div>
                </div>

                <div class="booking-details">
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div>
                            <div class="detail-label">Periode</div>
                            <div class="detail-value">
                                {{ \Carbon\Carbon::parse($booking->start_date)->format('d M') }}
                                -
                                {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div>
                            <div class="detail-label">Total</div>
                            <div class="detail-value">
                                Rp {{ number_format($booking->total_payment,0,',','.') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TIMER --}}
                @if($booking->status === 'Menunggu Pembayaran' && !$isExpire)
                    <div class="timer-wrapper">
                        Selesaikan pembayaran dalam
                        <span class="timer countdown-timer"
                              data-expire="{{ $expireAt->timestamp }}">
                            --
                        </span>
                    </div>
                @endif

                {{-- INSTRUKSI TRANSFER --}}
                @if($booking->payment_method === 'Transfer Bank' && $booking->status === 'Menunggu Pembayaran' && !$isExpire)
                    <div class="payment-instruction-box">
                        <p><strong><i class="fas fa-university"></i> Rekening Pembayaran</strong></p>

                        <div class="bank-info">
                            <div>
                                <span>No. Rekening</span>
                                <strong>{{ $booking->vehicle->owner->ownerProfile->bank_account ?? '-' }}</strong>
                            </div>
                            <div>
                                <span>Atas Nama</span>
                                <strong>{{ $booking->vehicle->owner->ownerProfile->owner_name ?? '-' }}</strong>
                            </div>
                        </div>

                        <form action="{{ route('booking.uploadProof', $booking->id) }}"
                              method="POST"
                              enctype="multipart/form-data"
                              autocomplete="off">
                            @csrf
                            <label>Unggah Bukti Transfer</label>
                            <input type="file" name="payment_proof" required accept="image/*">
                            <button type="submit" class="submit-btn">Kirim Bukti</button>
                        </form>
                    </div>
                @endif

                @if($booking->payment_proof && $booking->status === 'Menunggu Konfirmasi')
                    <div class="alert-success-custom">
                        <i class="fas fa-clock"></i> Bukti terkirim. Menunggu verifikasi.
                    </div>
                @endif

                <div class="action-buttons">
                    <a href="{{ route('booking.print',$booking->id) }}" class="cta-btn">
                        <i class="fas fa-file-invoice"></i> Invoice
                    </a>

                    @if($canBeCancelled)
                        <form action="{{ route('booking.cancel',$booking->id) }}"
                              method="POST">
                            @csrf
                            <button type="submit"
                                    class="cta-btn cancel"
                                    onclick="return confirm('Batalkan pesanan ini?')">
                                <i class="fas fa-trash"></i> Batalkan
                            </button>
                        </form>
                    @endif

                    @if($displayStatus === 'COMPLETED')
                        <a href="{{ route('booking.completeForm',$booking->id) }}"
                           class="cta-btn review">
                            <i class="fas fa-star"></i> Review
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <h3>Belum Ada Pesanan</h3>
                <a href="{{ route('user.dashboard') }}" class="cta-btn">Cari Kendaraan</a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/user/bookinglist.js')
@endpush
