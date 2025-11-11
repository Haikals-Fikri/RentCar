@extends('layouts.user')

@section('title', 'Ulasan Saya')

@push('styles')
    @vite('resources/css/user/review-user.css')
@endpush

@section('content')

<div class="reviews-grid">
    @forelse($reviews as $booking)
        @php
            $vehicle = $booking->vehicle;
            $isAvailable = $vehicle && $vehicle->status_vehicle === 'Tersedia';

            if($booking->rating >= 4){
                $messageClass = 'excellent';
                $messageText = 'Perjalanan Anda sangat memuaskan.';
            } elseif($booking->rating >= 1) {
                $messageClass = 'good';
                $messageText = 'Terima kasih atas Perjalanan Anda.';
            } else {
                $messageClass = 'no-rating';
                $messageText = 'Belum ada rating.';
            }
        @endphp

        <div class="review-card">
            <div class="vehicle-info">
                <img src="{{ $vehicle && $vehicle->image ? asset('storage/' . $vehicle->image) : '/placeholder.svg' }}"
                     alt="{{ $vehicle->name ?? 'Kendaraan' }}"
                     class="vehicle-image">
                <div class="vehicle-details">
                    <h3>{{ $vehicle->name ?? 'Kendaraan Dihapus' }}</h3>
                    <p>{{ $vehicle->brand ?? '-' }} - {{ $vehicle->type ?? '-' }}</p>
                </div>
            </div>

            <div class="review-content">
                <div class="rating">
                    @if($booking->rating)
                        <span class="rating-stars">{{ str_repeat('⭐', $booking->rating) }}</span>
                    @else
                        <span class="no-rating">Belum ada rating</span>
                    @endif
                </div>

                <p class="review-text">{{ $booking->review ?? 'Belum ada ulasan' }}</p>
                <div class="review-message {{ $messageClass }}">
                    <p>{{ $messageText }}</p>
                </div>

                <div class="review-actions">
                    @if($vehicle)

                        {{-- =============================================== --}}
                        {{-- KODE SUDAH DIPERBARUI --}}
                        {{-- =============================================== --}}
                        <a href="{{ route('booking.completeForm', $booking->id) }}" class="action-btn">
                            {{ $booking->rating ? 'Edit Ulasan' : 'Beri Ulasan' }}
                        </a>
                        {{-- =============================================== --}}
                        {{-- AKHIR PERUBAHAN --}}
                        {{-- =============================================== --}}

                        @if($isAvailable)
                            <a href="{{ route('booking.form', $vehicle->id) }}" class="action-btn">
                                Pesan Kendaraan yang Sama
                            </a>
                        @else
                            <p class="no-rating">Kendaraan tidak tersedia. Mau cari kendaraan lain?</p>
                            <a href="{{ route('user.dashboard') }}" class="action-btn">
                                Cari Kendaraan Lain
                            </a>
                        @endif

                        @if($vehicle->owner)
                            <a href="{{ route('profile.owner.view', $vehicle->owner->id) }}" class="action-btn">
                                Lihat Profil Owner
                            </a>
                        @endif
                    @else
                        <p class="no-rating">Kendaraan sudah dihapus, tidak bisa melakukan aksi.</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <h3>Belum ada ulasan</h3>
            <p>Anda belum memberikan ulasan pada kendaraan yang disewa.</p>
        </div>
    @endforelse
</div>

@endsection
